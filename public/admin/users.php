<?php
require_once __DIR__ . '/../../includes/auth.php';
check_login();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../config/db.php';

$user_type = $_SESSION['user_type'] ?? '';
if ($user_type !== 'admin') {
    header('Location: /wellbeing-directory/public/index.php');
    exit;
}

$pdo = get_db();

// Handle delete action
if (isset($_GET['delete_id']) && ctype_digit($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if ($id !== $_SESSION['user_id']) { // Prevent self-delete
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
        echo "<div class='alert alert-success'>User deleted.</div>";
    }
}

// Fetch all users
$users = $pdo->query("SELECT id, name, email, user_type, language_preference, created_at FROM users ORDER BY id DESC")->fetchAll();
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

    .users-table-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .table {
        margin-bottom: 0;
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

    .user-type-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-admin {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .badge-provider {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .badge-community {
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
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

    .btn-add-user {
        background: #2c5233;
        border: none;
        border-radius: 50px;
        padding: 0.8rem 2rem;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-add-user:hover {
        background: #1a3720;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(44, 82, 51, 0.2);
        color: white;
    }
</style>

<div class="container py-4">
    <!-- Status Bar -->
    <div class="d-flex justify-content-between align-items-center">
       
    </div>

    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0">
                <i class="fas fa-users-cog me-2"></i>
                Manage Users
            </h2>
            <p class="mb-0 mt-2 opacity-75">View and manage system users</p>
        </div>
        <a href="user_add.php" class="btn btn-add-user">
            <i class="fas fa-user-plus me-2"></i>
            Add New User
        </a>
    </div>

    <!-- Users Table -->
    <div class="users-table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>ID</th>
                    <th><i class="fas fa-user me-2"></i>Name</th>
                    <th><i class="fas fa-envelope me-2"></i>Email</th>
                    <th><i class="fas fa-user-tag me-2"></i>User Type</th>
                    <th><i class="fas fa-globe me-2"></i>Language</th>
                    <th><i class="fas fa-calendar-alt me-2"></i>Created At</th>
                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['id']) ?></td>
                        <td>
                            <i class="fas fa-user-circle me-2 text-muted"></i>
                            <?= htmlspecialchars($u['name']) ?>
                        </td>
                        <td>
                            <i class="fas fa-envelope me-2 text-muted"></i>
                            <?= htmlspecialchars($u['email']) ?>
                        </td>
                        <td>
                            <span class="user-type-badge badge-<?= $u['user_type'] ?>">
                                <?= htmlspecialchars(ucfirst($u['user_type'])) ?>
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-language me-2 text-muted"></i>
                            <?= htmlspecialchars(strtoupper($u['language_preference'])) ?>
                        </td>
                        <td>
                            <i class="far fa-clock me-2 text-muted"></i>
                            <?= htmlspecialchars(date('Y-m-d H:i', strtotime($u['created_at']))) ?>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="user_edit.php?id=<?= $u['id'] ?>" 
                                   class="btn btn-action btn-primary">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit
                                </a>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="users.php?delete_id=<?= $u['id'] ?>" 
                                       class="btn btn-action btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash-alt me-1"></i>
                                        Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>