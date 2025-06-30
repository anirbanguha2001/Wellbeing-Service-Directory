<?php
require_once __DIR__ . '/../../includes/auth.php';
check_login();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/functions.php';

$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'] ?? '';
if ($user_type !== 'provider') {
    header("Location: dashboard.php");
    exit;
}

$pdo = get_db();

// Update status (approve/cancel)
if (isset($_GET['action'], $_GET['id']) && is_numeric($_GET['id'])) {
    $appt_id = intval($_GET['id']);
    $action = $_GET['action'];
    if (in_array($action, ['approve', 'cancel'])) {
        $status = $action === 'approve' ? 'approved' : 'cancelled';
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND provider_id = ?");
        $stmt->execute([$status, $appt_id, $user_id]);
    }
}

// Fetch appointments for this provider
$stmt = $pdo->prepare(
    "SELECT a.*, u.name AS client_name, s.name_en AS service_name 
     FROM appointments a
     JOIN users u ON a.user_id = u.id
     JOIN services s ON a.service_id = s.id
     WHERE a.provider_id = ?
     ORDER BY a.appointment_datetime DESC"
);
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body {
        background: linear-gradient(135deg, rgb(128, 169, 192) 0%, rgb(220, 228, 191) 100%);
        min-height: 100vh;
        color: #2c3e50;
    }

    .status-bar {
        background: rgba(255, 255, 255, 0.9);
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

    .appointments-table {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background: #2c5233;
        color: white;
        font-weight: 600;
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

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 500;
    }

    .badge.bg-success {
        background: rgba(25, 135, 84, 0.1) !important;
        color: #198754;
    }

    .badge.bg-danger {
        background: rgba(220, 53, 69, 0.1) !important;
        color: #dc3545;
    }

    .badge.bg-secondary {
        background: rgba(108, 117, 125, 0.1) !important;
        color: #6c757d;
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

    .btn-approve {
        background: #198754;
        border: none;
        color: white;
    }

    .btn-cancel {
        background: #dc3545;
        border: none;
        color: white;
    }

    .no-appointments {
        background: white;
        border-radius: 15px;
        padding: 3rem;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
</style>

    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-0">
            <i class="fas fa-calendar-check me-2"></i>
            My Service Appointments
        </h2>
        <p class="mb-0 mt-2 opacity-75">Manage your service appointments and bookings</p>
    </div>

    <?php if (empty($appointments)): ?>
        <div class="no-appointments">
            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
            <h4>No appointments yet</h4>
            <p class="text-muted mb-0">New appointments will appear here when clients book your services.</p>
        </div>
    <?php else: ?>
        <div class="appointments-table">
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-concierge-bell me-2"></i>Service</th>
                        <th><i class="fas fa-user me-2"></i>Client</th>
                        <th><i class="fas fa-clock me-2"></i>Date & Time</th>
                        <th><i class="fas fa-info-circle me-2"></i>Status</th>
                        <th><i class="fas fa-sticky-note me-2"></i>Notes</th>
                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($appointments as $appt): ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($appt['service_name']); ?></td>
                        <td>
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($appt['client_name']); ?>
                        </td>
                        <td>
                            <i class="far fa-calendar-alt me-1"></i>
                            <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($appt['appointment_datetime']))); ?>
                        </td>
                        <td>
                            <?php
                            $status = $appt['status'] ?? 'booked';
                            if ($status === 'approved') {
                                echo '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Approved</span>';
                            } elseif ($status === 'cancelled') {
                                echo '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Cancelled</span>';
                            } else {
                                echo '<span class="badge bg-secondary"><i class="fas fa-clock me-1"></i>Pending</span>';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($appt['notes']); ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <?php if (($appt['status'] ?? '') !== 'approved'): ?>
                                    <a href="?action=approve&id=<?php echo $appt['id']; ?>" 
                                       class="btn btn-action btn-approve">
                                        <i class="fas fa-check me-1"></i>
                                        Approve
                                    </a>
                                <?php endif; ?>
                                <?php if (($appt['status'] ?? '') !== 'cancelled'): ?>
                                    <a href="?action=cancel&id=<?php echo $appt['id']; ?>" 
                                       class="btn btn-action btn-cancel">
                                        <i class="fas fa-times me-1"></i>
                                        Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>