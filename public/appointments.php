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
<div class="container mt-4" style="max-width:800px;">
    <h2><?php echo $strings['my_appointments'] ?? 'My Appointments'; ?></h2>
    <?php if (empty($appointments)): ?>
        <div class="alert alert-info">You have no appointments booked.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Service</th>
                    <th>Provider</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($appointments as $appt): ?>
                <tr>
                    <td><?php echo htmlspecialchars($appt['service_name']); ?></td>
                    <td><?php echo htmlspecialchars($appt['provider_name']); ?></td>
                    <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($appt['appointment_datetime']))); ?></td>
                    <td>
                        <?php
                        if (isset($appt['status'])) {
                            // You may have a status field (pending, confirmed, cancelled, etc.)
                            echo htmlspecialchars(ucfirst($appt['status']));
                        } else {
                            echo 'Booked';
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($appt['notes']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="service-directory.php" class="btn btn-primary mt-3"><?php echo $strings['book_appointment'] ?? 'Book Appointment'; ?></a>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>