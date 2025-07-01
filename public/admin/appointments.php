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

// Define possible statuses for appointments
$statuses = ['pending', 'approved', 'rejected', 'cancelled', 'completed'];

// Delete
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    echo "<div class='alert alert-success'>Appointment deleted.</div>";
}

// Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO appointments (user_id, service_id, provider_id, appointment_datetime, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['user_id'],
        $_POST['service_id'],
        $_POST['provider_id'],
        $_POST['appointment_datetime'],
        $_POST['status']
    ]);
    echo "<div class='alert alert-success'>Appointment added.</div>";
}

// Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $stmt = $pdo->prepare("UPDATE appointments SET user_id=?, service_id=?, provider_id=?, appointment_datetime=?, status=? WHERE id=?");
    $stmt->execute([
        $_POST['user_id'],
        $_POST['service_id'],
        $_POST['provider_id'],
        $_POST['appointment_datetime'],
        $_POST['status'],
        $_POST['edit_id']
    ]);
    echo "<div class='alert alert-success'>Appointment updated.</div>";
}

// For edit form
$edit = null;
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch();
}

// For dropdowns
$users = $pdo->query("SELECT id, name FROM users")->fetchAll();
$services = $pdo->query("SELECT id, name_en FROM services")->fetchAll();
$providers = $pdo->query("SELECT id, name FROM users WHERE user_type='provider' OR user_type='admin'")->fetchAll();

$appointments = $pdo->query(
    "SELECT a.id, a.user_id, a.service_id, a.provider_id, u.name as user_name, s.name_en as service_name, p.name as provider_name, a.appointment_datetime, a.status, a.created_at
     FROM appointments a
     LEFT JOIN users u ON a.user_id = u.id
     LEFT JOIN services s ON a.service_id = s.id
     LEFT JOIN users p ON a.provider_id = p.id
     ORDER BY a.created_at DESC"
)->fetchAll();
?>

<!-- Add Font Awesome and Bootstrap Datepicker -->
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

    .appointment-form {
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

    .appointments-table {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
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

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-pending { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .status-approved { background: rgba(25, 135, 84, 0.1); color: #198754; }
    .status-rejected { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .status-cancelled { background: rgba(108, 117, 125, 0.1); color: #6c757d; }
    .status-completed { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }

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

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
</style>

<div class="container py-4">
    <!-- Status Bar -->
    <div class="d-flex justify-content-between align-items-center">
       
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-0">
            <i class="fas fa-calendar-check me-2"></i>
            Manage Appointments
        </h2>
        <p class="mb-0 mt-2 opacity-75">Schedule and manage appointment bookings</p>
    </div>

    <!-- Add/Edit Form -->
    <div class="appointment-form">
        <form method="post">
            <?php if ($edit): ?>
                <input type="hidden" name="edit_id" value="<?= $edit['id'] ?>">
                <h4 class="mb-4">
                    <i class="fas fa-edit me-2"></i>
                    Edit Appointment #<?= $edit['id'] ?>
                </h4>
            <?php else: ?>
                <input type="hidden" name="add" value="1">
                <h4 class="mb-4">
                    <i class="fas fa-plus-circle me-2"></i>
                    Add New Appointment
                </h4>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user me-2"></i>User</label>
                    <select name="user_id" class="form-control" required>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($edit && $u['id'] == $edit['user_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-hand-holding-heart me-2"></i>Service</label>
                    <select name="service_id" class="form-control" required>
                        <?php foreach ($services as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= ($edit && $s['id'] == $edit['service_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name_en']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user-md me-2"></i>Provider</label>
                    <select name="provider_id" class="form-control" required>
                        <?php foreach ($providers as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($edit && $p['id'] == $edit['provider_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-clock me-2"></i>Date & Time</label>
                    <input type="datetime-local" name="appointment_datetime" class="form-control"
                           value="<?= isset($edit['appointment_datetime']) ? date('Y-m-d\\TH:i', strtotime($edit['appointment_datetime'])) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-flag me-2"></i>Status</label>
                    <select name="status" class="form-control" required>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status ?>" <?= ($edit && $edit['status'] === $status) ? 'selected' : '' ?>>
                                <?= ucfirst($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-action btn-primary">
                    <i class="fas <?= $edit ? 'fa-save' : 'fa-plus-circle' ?> me-2"></i>
                    <?= $edit ? "Update Appointment" : "Add Appointment" ?>
                </button>
                <?php if ($edit): ?>
                    <a href="appointments.php" class="btn btn-action btn-secondary ms-2">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="appointments-table">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>ID</th>
                    <th><i class="fas fa-user me-2"></i>User</th>
                    <th><i class="fas fa-hand-holding-heart me-2"></i>Service</th>
                    <th><i class="fas fa-user-md me-2"></i>Provider</th>
                    <th><i class="fas fa-clock me-2"></i>Date & Time</th>
                    <th><i class="fas fa-flag me-2"></i>Status</th>
                    <th><i class="fas fa-calendar me-2"></i>Created</th>
                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($appointments as $appt): ?>
                <tr>
                    <td><?= $appt['id'] ?></td>
                    <td>
                        <i class="fas fa-user-circle me-2 text-muted"></i>
                        <?= htmlspecialchars($appt['user_name']) ?>
                    </td>
                    <td><?= htmlspecialchars($appt['service_name']) ?></td>
                    <td>
                        <i class="fas fa-user-md me-2 text-muted"></i>
                        <?= htmlspecialchars($appt['provider_name']) ?>
                    </td>
                    <td>
                        <i class="far fa-calendar-alt me-2 text-muted"></i>
                        <?= htmlspecialchars(date('Y-m-d H:i', strtotime($appt['appointment_datetime']))) ?>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $appt['status'] ?>">
                            <?= ucfirst(htmlspecialchars($appt['status'])) ?>
                        </span>
                    </td>
                    <td>
                        <i class="far fa-clock me-2 text-muted"></i>
                        <?= htmlspecialchars(date('Y-m-d H:i', strtotime($appt['created_at']))) ?>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="?edit=<?= $appt['id'] ?>" class="btn btn-action btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Edit
                            </a>
                            <a href="?delete=<?= $appt['id'] ?>" 
                               class="btn btn-action btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this appointment?')">
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