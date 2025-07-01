<?php
require_once __DIR__ . '/../../includes/auth.php';
check_login();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../config/db.php';

$pdo = get_db();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $user_type = trim($_POST['user_type'] ?? '');
    $password = $_POST['password'] ?? '';
    $lang = $_POST['language_preference'] ?? 'EN';

    if ($name && $email && $password && in_array($user_type, ['admin', 'provider', 'community'])) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, user_type, password, language_preference) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $user_type, $hash, $lang])) {
            $success = "User added!";
        } else {
            $error = "Failed to add user.";
        }
    } else {
        $error = "Please fill all fields correctly.";
    }
}
?>
<div class="container">
    <h2>Add New User</h2>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3"><label>Name</label><input class="form-control" name="name" required></div>
        <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email" required></div>
        <div class="mb-3">
            <label>User Type</label>
            <select class="form-control" name="user_type" required>
                <option value="community">Community</option>
                <option value="provider">Provider</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="mb-3"><label>Password</label><input class="form-control" name="password" type="password" required></div>
        <div class="mb-3"><label>Language</label><input class="form-control" name="language_preference" value="EN"></div>
        <button type="submit" class="btn btn-primary">Add User</button>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>