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
    $pdo->prepare("DELETE FROM services WHERE id=?")->execute([$id]);
    echo "<div class='alert alert-success'>Service deleted.</div>";
}

$services = $pdo->query(
    "SELECT s.id, s.name_en, s.description_en, u.name as provider, s.location, s.created_at
     FROM services s
     LEFT JOIN users u ON s.provider_id = u.id
     ORDER BY s.id DESC"
)->fetchAll();
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

    .services-table-container {
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

    .location-badge {
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
    }

    .provider-badge {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
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

    .btn-add-service {
        background: #2c5233;
        border: none;
        border-radius: 50px;
        padding: 0.8rem 2rem;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-add-service:hover {
        background: #1a3720;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(44, 82, 51, 0.2);
        color: white;
    }

    .description-cell {
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
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0">
                <i class="fas fa-hand-holding-heart me-2"></i>
                Manage Services
            </h2>
            <p class="mb-0 mt-2 opacity-75">View and manage available services</p>
        </div>
        <a href="service_add.php" class="btn btn-add-service">
            <i class="fas fa-plus-circle me-2"></i>
            Add New Service
        </a>
    </div>

    <!-- Services Table -->
    <div class="services-table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>ID</th>
                    <th><i class="fas fa-tag me-2"></i>Name</th>
                    <th><i class="fas fa-align-left me-2"></i>Description</th>
                    <th><i class="fas fa-user-md me-2"></i>Provider</th>
                    <th><i class="fas fa-map-marker-alt me-2"></i>Location</th>
                    <th><i class="fas fa-calendar-alt me-2"></i>Created At</th>
                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $svc): ?>
                    <tr>
                        <td><?= htmlspecialchars($svc['id']) ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($svc['name_en']) ?></td>
                        <td class="description-cell" title="<?= htmlspecialchars($svc['description_en']) ?>">
                            <?= htmlspecialchars($svc['description_en']) ?>
                        </td>
                        <td>
                            <span class="provider-badge">
                                <i class="fas fa-user-md me-2"></i>
                                <?= htmlspecialchars($svc['provider']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($svc['location']): ?>
                                <span class="location-badge">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?= htmlspecialchars($svc['location']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <i class="far fa-clock me-2 text-muted"></i>
                            <?= htmlspecialchars(date('Y-m-d H:i', strtotime($svc['created_at']))) ?>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="service_edit.php?id=<?= $svc['id'] ?>" 
                                   class="btn btn-action btn-primary">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit
                                </a>
                                <a href="services.php?delete_id=<?= $svc['id'] ?>" 
                                   class="btn btn-action btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this service?')">
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