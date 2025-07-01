<?php
require_once __DIR__ . '/../includes/auth.php';
check_login();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/functions.php';

$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';

$pdo = get_db();
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'] ?? '';

if ($user_type !== 'community') {
    // Redirect non-community members (e.g. providers, admins) away
    header("Location: dashboard.php");
    exit;
}

// Fetch appointments for the community member
$stmt = $pdo->prepare(
    "SELECT a.*, s.name_en AS service_name, u.name AS provider_name
     FROM appointments a
     JOIN services s ON a.service_id = s.id
     JOIN users u ON a.provider_id = u.id
     WHERE a.user_id = ?
     ORDER BY a.appointment_datetime DESC"
);
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();
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

    .appointments-table {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
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

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-booked { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }
    .status-pending { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .status-approved { background: rgba(25, 135, 84, 0.1); color: #198754; }
    .status-cancelled { background: rgba(220, 53, 69, 0.1); color: #dc3545; }

    .btn-book {
        background: #2c5233;
        border: none;
        border-radius: 50px;
        padding: 0.8rem 2rem;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-book:hover {
        background: #1a3720;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(44, 82, 51, 0.2);
        color: white;
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

    .no-appointments {
        background: white;
        border-radius: 15px;
        padding: 3rem;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="container py-4" style="max-width:900px;">
    <!-- Status Bar -->
    <div class="d-flex justify-content-between align-items-center">
      
    </div>

    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0">
                <i class="fas fa-calendar-check me-2"></i>
                My Appointments
            </h2>
            <p class="mb-0 mt-2 opacity-75">View and manage your scheduled appointments</p>
        </div>
        <a href="service-directory.php" class="btn btn-book">
            <i class="fas fa-plus-circle me-2"></i>
            Book New Appointment
        </a>
    </div>

    <?php if (empty($appointments)): ?>
        <div class="no-appointments">
            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
            <h4>No Appointments Booked</h4>
            <p class="text-muted mb-4">You currently don't have any appointments scheduled.</p>
            <a href="service-directory.php" class="btn btn-book">
                <i class="fas fa-plus-circle me-2"></i>
                Browse Services
            </a>
        </div>
    <?php else: ?>
        <div class="appointments-table">
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hand-holding-heart me-2"></i>Service</th>
                        <th><i class="fas fa-user-md me-2"></i>Provider</th>
                        <th><i class="fas fa-clock me-2"></i>Date & Time</th>
                        <th><i class="fas fa-info-circle me-2"></i>Status</th>
                        <th><i class="fas fa-sticky-note me-2"></i>Notes</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($appointments as $appt): ?>
                    <tr>
                        <td class="fw-bold">
                            <?php echo htmlspecialchars($appt['service_name']); ?>
                        </td>
                        <td>
                            <span class="provider-badge">
                                <i class="fas fa-user-md me-2"></i>
                                <?php echo htmlspecialchars($appt['provider_name']); ?>
                            </span>
                        </td>
                        <td>
                            <i class="far fa-calendar-alt me-2 text-muted"></i>
                            <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($appt['appointment_datetime']))); ?>
                        </td>
                        <td>
                            <?php
                            $status = isset($appt['status']) ? $appt['status'] : 'booked';
                            $statusClass = 'status-' . $status;
                            $icon = $status === 'approved' ? 'fa-check' : 
                                   ($status === 'cancelled' ? 'fa-times' : 
                                   ($status === 'pending' ? 'fa-clock' : 'fa-calendar-check'));
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <i class="fas <?php echo $icon; ?> me-1"></i>
                                <?php echo htmlspecialchars(ucfirst($status)); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($appt['notes'])): ?>
                                <i class="fas fa-sticky-note me-2 text-muted"></i>
                                <?php echo htmlspecialchars($appt['notes']); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>