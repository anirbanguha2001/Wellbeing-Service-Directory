<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../config/db.php';
check_login();

$user_type = $_SESSION['user_type'] ?? '';
if ($user_type !== 'admin') {
    header('Location: /wellbeing-directory/public/index.php');
    exit;
}

$pdo = get_db();

// Define available resource types
$types = ['article', 'video', 'document']; // Add or modify types as needed

// Handle Delete Action
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM resources WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    echo "<div class='alert alert-success'>Resource deleted.</div>";
}

// Handle Add Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $title_en = $_POST['title_en'] ?? '';
    $title_mi = $_POST['title_mi'] ?? '';
    $description_en = $_POST['description_en'] ?? '';
    $description_mi = $_POST['description_mi'] ?? '';
    $type = $_POST['type'] ?? 'article';
    $url = $_POST['url'] ?? '';
    $language = $_POST['language'] ?? 'en';
    $uploaded_by = $_SESSION['user_id'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO resources (title_en, title_mi, description_en, description_mi, type, url, language, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title_en, $title_mi, $description_en, $description_mi, $type, $url, $language, $uploaded_by]);
    echo "<div class='alert alert-success'>Resource added.</div>";
}

// Handle Edit Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $title_en = $_POST['title_en'] ?? '';
    $title_mi = $_POST['title_mi'] ?? '';
    $description_en = $_POST['description_en'] ?? '';
    $description_mi = $_POST['description_mi'] ?? '';
    $type = $_POST['type'] ?? 'article';
    $url = $_POST['url'] ?? '';
    $language = $_POST['language'] ?? 'en';

    $stmt = $pdo->prepare("UPDATE resources SET title_en=?, title_mi=?, description_en=?, description_mi=?, type=?, url=?, language=? WHERE id=?");
    $stmt->execute([$title_en, $title_mi, $description_en, $description_mi, $type, $url, $language, $edit_id]);
    echo "<div class='alert alert-success'>Resource updated.</div>";
}

// For edit form population
$edit = null;
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch();
}

// Get all resources
$resources = $pdo->query(
    "SELECT r.id, r.title_en, r.title_mi, r.description_en, r.description_mi, r.type, r.url, r.language, r.created_at, u.name as uploader
     FROM resources r
     LEFT JOIN users u ON r.uploaded_by = u.id
     ORDER BY r.created_at DESC"
)->fetchAll();
?>

<?php
// [Previous PHP code remains the same until HTML part]
?>

<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body {
        background: linear-gradient(135deg, rgb(128, 169, 192) 0%, rgb(220, 228, 191) 100%);
        min-height: 100vh;
        color: #2c3e50;
    }

    .status-bar {
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .page-header {
        background: linear-gradient(135deg, #43a047 0%, rgb(79, 168, 76) 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(67, 160, 71, 0.2);
    }

    .resource-form {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .form-control {
        border: 2px solid rgba(44, 82, 51, 0.1);
        border-radius: 10px;
        padding: 0.8rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #2c5233;
        box-shadow: none;
    }

    .form-label {
        font-weight: 600;
        color: #2c5233;
        margin-bottom: 0.5rem;
    }

    .resources-table {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow-x: auto;
    }

    .table th {
        background: #2c5233;
        color: white;
        padding: 1rem;
        border: none;
        white-space: nowrap;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: rgba(44, 82, 51, 0.05);
    }

    .type-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .type-article { background: rgba(108, 117, 125, 0.1); color: #6c757d; }
    .type-video { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .type-document { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }

    .language-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .url-link {
        color: #2c5233;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .url-link:hover {
        color: #1a3720;
        text-decoration: underline;
    }
</style>

<div class="container py-4">
    <!-- Status Bar -->
    <div class="d-flex justify-content-between align-items-center">
       
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-0">
            <i class="fas fa-book-reader me-2"></i>
            Manage Cultural Resources
        </h2>
        <p class="mb-0 mt-2 opacity-75">Add and manage cultural resources and materials</p>
    </div>

    <!-- Add/Edit Form -->
    <div class="resource-form">
        <form method="post">
            <?php if ($edit): ?>
                <input type="hidden" name="edit_id" value="<?= $edit['id'] ?>">
                <h4 class="mb-4">
                    <i class="fas fa-edit me-2"></i>
                    Edit Resource #<?= $edit['id'] ?>
                </h4>
            <?php else: ?>
                <input type="hidden" name="add" value="1">
                <h4 class="mb-4">
                    <i class="fas fa-plus-circle me-2"></i>
                    Add New Resource
                </h4>
            <?php endif; ?>

            <div class="form-section">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-heading me-2"></i>Title (EN)
                    </label>
                    <input type="text" name="title_en" class="form-control" value="<?= htmlspecialchars($edit['title_en'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-heading me-2"></i>Title (MI)
                    </label>
                    <input type="text" name="title_mi" class="form-control" value="<?= htmlspecialchars($edit['title_mi'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-tags me-2"></i>Type
                    </label>
                    <select name="type" class="form-control" required>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= $type ?>" <?= (isset($edit['type']) && $edit['type'] === $type) ? 'selected' : '' ?>>
                                <?= ucfirst($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-left me-2"></i>Description (EN)
                    </label>
                    <textarea name="description_en" class="form-control" rows="3"><?= htmlspecialchars($edit['description_en'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-left me-2"></i>Description (MI)
                    </label>
                    <textarea name="description_mi" class="form-control" rows="3"><?= htmlspecialchars($edit['description_mi'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-link me-2"></i>URL or File Link
                    </label>
                    <input type="text" name="url" class="form-control" value="<?= htmlspecialchars($edit['url'] ?? '') ?>" placeholder="https://... or /uploads/resources/filename.pdf">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-globe me-2"></i>Language
                    </label>
                    <select name="language" class="form-control">
                        <option value="en" <?= (isset($edit['language']) && $edit['language'] === 'en') ? 'selected' : '' ?>>English</option>
                        <option value="mi" <?= (isset($edit['language']) && $edit['language'] === 'mi') ? 'selected' : '' ?>>Te Reo</option>
                        <option value="both" <?= (isset($edit['language']) && $edit['language'] === 'both') ? 'selected' : '' ?>>Both</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-action btn-primary">
                    <i class="fas <?= $edit ? 'fa-save' : 'fa-plus-circle' ?> me-2"></i>
                    <?= $edit ? "Update Resource" : "Add Resource" ?>
                </button>
                <?php if ($edit): ?>
                    <a href="resources.php" class="btn btn-action btn-secondary ms-2">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Resources Table -->
    <div class="resources-table">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>ID</th>
                    <th><i class="fas fa-heading me-2"></i>Title (EN)</th>
                    <th><i class="fas fa-heading me-2"></i>Title (MI)</th>
                    <th><i class="fas fa-tags me-2"></i>Type</th>
                    <th><i class="fas fa-globe me-2"></i>Language</th>
                    <th><i class="fas fa-link me-2"></i>URL</th>
                    <th><i class="fas fa-user me-2"></i>Uploader</th>
                    <th><i class="fas fa-calendar me-2"></i>Created</th>
                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($resources as $res): ?>
                <tr>
                    <td><?= htmlspecialchars($res['id']) ?></td>
                    <td><?= htmlspecialchars($res['title_en']) ?></td>
                    <td><?= htmlspecialchars($res['title_mi']) ?></td>
                    <td>
                        <span class="type-badge type-<?= $res['type'] ?>">
                            <i class="fas <?= $res['type'] === 'video' ? 'fa-video' : ($res['type'] === 'document' ? 'fa-file-alt' : 'fa-newspaper') ?> me-1"></i>
                            <?= ucfirst(htmlspecialchars($res['type'])) ?>
                        </span>
                    </td>
                    <td>
                        <span class="language-badge">
                            <?= strtoupper(htmlspecialchars($res['language'])) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($res['url']): ?>
                            <a href="<?= htmlspecialchars($res['url']) ?>" class="url-link" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>
                                View
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <i class="fas fa-user-circle me-2 text-muted"></i>
                        <?= htmlspecialchars($res['uploader'] ?? 'N/A') ?>
                    </td>
                    <td>
                        <i class="far fa-clock me-2 text-muted"></i>
                        <?= htmlspecialchars(date('Y-m-d H:i', strtotime($res['created_at']))) ?>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="?edit=<?= $res['id'] ?>" class="btn btn-action btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Edit
                            </a>
                            <a href="?delete=<?= $res['id'] ?>" 
                               class="btn btn-action btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this resource?')">
                                <i class="fas fa-trash-alt me-1"></i>
                                Delete
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>