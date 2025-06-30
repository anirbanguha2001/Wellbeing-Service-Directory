<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

check_login();
$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';

$pdo = get_db();
$service_id = intval($_GET['service_id'] ?? 0);
$service = null;
$error = '';
$success = '';

if ($service_id > 0) {
    $stmt = $pdo->prepare("SELECT s.*, u.name AS provider_name FROM services s JOIN users u ON s.provider_id = u.id WHERE s.id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch();
}

if (!$service) {
    $error = "Service not found.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $service) {
    $appointment_datetime = $_POST['appointment_datetime'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if (!$appointment_datetime) {
        $error = "Please select date and time.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO appointments (user_id, service_id, provider_id, appointment_datetime, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $service_id, $service['provider_id'], $appointment_datetime, $notes]);
        $success = "Appointment booked successfully!";
    }
}
?>
<div class="container mt-4" style="max-width: 600px;">
    <h2><?php echo $strings['book_appointment'] ?? 'Book Appointment'; ?></h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($service): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($service['name_en']); ?></h5>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($service['description_en'])); ?></p>
                <p><strong><?php echo $strings['provider_dashboard'] ?? 'Provider'; ?>:</strong> <?php echo htmlspecialchars($service['provider_name']); ?></p>
            </div>
        </div>
        <form method="post">
            <div class="mb-3">
                <label for="appointment_datetime" class="form-label"><?php echo $strings['book_appointment'] ?? 'Book Appointment'; ?> Date & Time</label>
                <input type="datetime-local" class="form-control" id="appointment_datetime" name="appointment_datetime" required>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label"><?php echo $strings['notes'] ?? 'Notes'; ?></label>
                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $strings['book_appointment'] ?? 'Book Appointment'; ?></button>
        </form>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>