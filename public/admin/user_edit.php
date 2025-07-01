<?php
require_once __DIR__ . '/../../includes/auth.php';
check_login();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../config/db.php';

$pdo = get_db();
$id = $_GET['id'] ?? '';
$success = '';
$error = '';

if (!ctype_digit($id)) die("Invalid user id");

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) die("User not found");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $user_type = trim($_POST['user_type'] ?? '');
    $lang = $_POST['language_preference'] ?? 'EN';

    if ($name && $email && in_array($user_type, ['admin', 'provider', 'community'])) {
        $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, user_type=?, language_preference=? WHERE id=?");
        if ($stmt->execute([$name, $email, $user_type, $lang, $id])) {
            $success = "User updated!";
        } else {
            $error = "Failed to update.";
        }
    } else {
        $error = "Please fill all fields correctly.";
    }
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
}
?>
<div class="container">
    <h2>Edit User</h2>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3"><label>Name</label><input class="form-control" name="name" required value="<?= htmlspecialchars($user['name']) ?>"></div>
        <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email" required value="<?= htmlspecialchars($user['email']) ?>"></div>
        <div class="mb-3">
            <label>User Type</label>
            <select class="form-control" name="user_type" required>
                <option value="community" <?= $user['user_type'] == 'community' ? 'selected' : '' ?>>Community</option>
                <option value="provider" <?= $user['user_type'] == 'provider' ? 'selected' : '' ?>>Provider</option>
                <option value="admin" <?= $user['user_type'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div class="mb-3"><label>Language</label><input class="form-control" name="language_preference" value="<?= htmlspecialchars($user['language_preference']) ?>"></div>
        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>