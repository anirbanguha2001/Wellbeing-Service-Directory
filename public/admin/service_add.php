<?php
require_once __DIR__ . '/../../includes/auth.php';
check_login();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../config/db.php';

if ($_SESSION['user_type'] !== 'admin') {
    header('Location: /wellbeing-directory/public/index.php');
    exit;
}

$pdo = get_db();
$success = '';
$error = '';

// Fetch all providers for selection
$providers = $pdo->query("SELECT id, name FROM users WHERE user_type='provider'")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_en = trim($_POST['name_en'] ?? '');
    $description_en = trim($_POST['description_en'] ?? '');
    $provider_id = $_POST['provider_id'] ?? '';
    $location = trim($_POST['location'] ?? '');

    if ($name_en && $description_en && $provider_id && ctype_digit($provider_id)) {
        $stmt = $pdo->prepare("INSERT INTO services (name_en, description_en, provider_id, location, created_at) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt->execute([$name_en, $description_en, $provider_id, $location])) {
            $success = "Service added!";
        } else {
            $error = "Failed to add service.";
        }
    } else {
        $error = "Please fill all fields correctly.";
    }
}
?>
<div class="container mt-4">
    <h2>Add New Service</h2>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3"><label>Name</label><input class="form-control" name="name_en" required></div>
        <div class="mb-3"><label>Description</label><textarea class="form-control" name="description_en" rows="3" required></textarea></div>
        <div class="mb-3">
            <label>Provider</label>
            <select class="form-control" name="provider_id" required>
                <option value="">Select provider</option>
                <?php foreach ($providers as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3"><label>Location</label><input class="form-control" name="location"></div>
        <button type="submit" class="btn btn-primary">Add Service</button>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>