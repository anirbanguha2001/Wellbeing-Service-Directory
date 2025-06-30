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

// Delete
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM feedback WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    echo "<div class='alert alert-success'>Feedback deleted.</div>";
}

// Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO feedback (user_id, service_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['user_id'], $_POST['service_id'], $_POST['rating'], $_POST['comment']]);
    echo "<div class='alert alert-success'>Feedback added.</div>";
}

// Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $stmt = $pdo->prepare("UPDATE feedback SET user_id=?, service_id=?, rating=?, comment=? WHERE id=?");
    $stmt->execute([$_POST['user_id'], $_POST['service_id'], $_POST['rating'], $_POST['comment'], $_POST['edit_id']]);
    echo "<div class='alert alert-success'>Feedback updated.</div>";
}

// For edit form
$edit = null;
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM feedback WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch();
}

// For dropdowns
$users = $pdo->query("SELECT id, name FROM users")->fetchAll();
$services = $pdo->query("SELECT id, name_en FROM services")->fetchAll();

$feedback = $pdo->query(
    "SELECT f.id, f.user_id, u.name as user_name, f.service_id, s.name_en as service_name, f.rating, f.comment, f.created_at
     FROM feedback f
     LEFT JOIN users u ON f.user_id = u.id
     LEFT JOIN services s ON f.service_id = s.id
     ORDER BY f.created_at DESC"
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

    .feedback-form {
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

    .feedback-table {
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

    .rating-stars {
        color: #ffc107;
        font-size: 1.1rem;
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

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .comment-cell {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<div class="container py-4">
    <!-- Status Bar -->
    <div class="d-flex justify-content-between align-items-center">
      
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-0">
            <i class="fas fa-comments me-2"></i>
            Manage Feedback
        </h2>
        <p class="mb-0 mt-2 opacity-75">Review and manage user feedback and ratings</p>
    </div>

    <!-- Add/Edit Form -->
    <div class="feedback-form">
        <form method="post">
            <?php if ($edit): ?>
                <input type="hidden" name="edit_id" value="<?= $edit['id'] ?>">
                <h4 class="mb-4">
                    <i class="fas fa-edit me-2"></i>
                    Edit Feedback #<?= $edit['id'] ?>
                </h4>
            <?php else: ?>
                <input type="hidden" name="add" value="1">
                <h4 class="mb-4">
                    <i class="fas fa-plus-circle me-2"></i>
                    Add New Feedback
                </h4>
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-2"></i>User
                    </label>
                    <select name="user_id" class="form-control" required>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($edit && $u['id'] == $edit['user_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-hand-holding-heart me-2"></i>Service
                    </label>
                    <select name="service_id" class="form-control" required>
                        <?php foreach ($services as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= ($edit && $s['id'] == $edit['service_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name_en']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-star me-2"></i>Rating
                    </label>
                    <input type="number" name="rating" min="1" max="5" class="form-control" 
                           value="<?= htmlspecialchars($edit['rating'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-comment me-2"></i>Comment
                    </label>
                    <input type="text" name="comment" class="form-control" 
                           value="<?= htmlspecialchars($edit['comment'] ?? '') ?>">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-action btn-primary">
                    <i class="fas <?= $edit ? 'fa-save' : 'fa-plus-circle' ?> me-2"></i>
                    <?= $edit ? "Update Feedback" : "Add Feedback" ?>
                </button>
                <?php if ($edit): ?>
                    <a href="feedback.php" class="btn btn-action btn-secondary ms-2">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Feedback Table -->
    <div class="feedback-table">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>ID</th>
                    <th><i class="fas fa-user me-2"></i>User</th>
                    <th><i class="fas fa-hand-holding-heart me-2"></i>Service</th>
                    <th><i class="fas fa-star me-2"></i>Rating</th>
                    <th><i class="fas fa-comment me-2"></i>Comment</th>
                    <th><i class="fas fa-calendar me-2"></i>Created At</th>
                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($feedback as $fb): ?>
                <tr>
                    <td><?= $fb['id'] ?></td>
                    <td>
                        <i class="fas fa-user-circle me-2 text-muted"></i>
                        <?= htmlspecialchars($fb['user_name']) ?>
                    </td>
                    <td><?= htmlspecialchars($fb['service_name']) ?></td>
                    <td>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?= $i <= $fb['rating'] ? '' : '-o text-muted' ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </td>
                    <td class="comment-cell" title="<?= htmlspecialchars($fb['comment']) ?>">
                        <?= htmlspecialchars($fb['comment']) ?>
                    </td>
                    <td>
                        <i class="far fa-clock me-2 text-muted"></i>
                        <?= htmlspecialchars(date('Y-m-d H:i', strtotime($fb['created_at']))) ?>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="?edit=<?= $fb['id'] ?>" class="btn btn-action btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Edit
                            </a>
                            <a href="?delete=<?= $fb['id'] ?>" 
                               class="btn btn-action btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this feedback?')">
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