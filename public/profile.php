<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

check_login();
$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';

$pdo = get_db();
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$success = '';
$error = '';

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// For providers, fetch profile details
$provider_profile = null;
if ($user_type === 'provider') {
    $stmt2 = $pdo->prepare("SELECT * FROM provider_profiles WHERE user_id = ?");
    $stmt2->execute([$user_id]);
    $provider_profile = $stmt2->fetch();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $website = trim($_POST['website'] ?? '');

    // Update users table
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $user_id]);

    // Update or insert provider_profiles
    if ($user_type === 'provider') {
        if ($provider_profile) {
            $stmt2 = $pdo->prepare("UPDATE provider_profiles SET bio = ?, phone = ?, address = ?, website = ? WHERE user_id = ?");
            $stmt2->execute([$bio, $phone, $address, $website, $user_id]);
        } else {
            $stmt2 = $pdo->prepare("INSERT INTO provider_profiles (user_id, bio, phone, address, website) VALUES (?, ?, ?, ?, ?)");
            $stmt2->execute([$user_id, $bio, $phone, $address, $website]);
        }
    }

    // Update session name if changed
    $_SESSION['name'] = $name;
    $success = "Profile updated successfully!";
    // Refresh data
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user_type === 'provider') {
        $stmt2->execute([$user_id]);
        $provider_profile = $stmt2->fetch();
    }
}

?>
<div class="container mt-4" style="max-width:700px;">
    <h2><?php echo $strings['profile'] ?? 'Profile'; ?></h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label for="name" class="form-label"><?php echo $strings['name'] ?? 'Name'; ?></label>
            <input type="text" class="form-control" id="name" name="name" required
                   value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label"><?php echo $strings['email'] ?? 'Email'; ?></label>
            <input type="email" class="form-control" id="email" name="email" required
                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
        </div>
        <?php if ($user_type === 'provider'): ?>
            <div class="mb-3">
                <label for="bio" class="form-label">Bio</label>
                <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($provider_profile['bio'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone"
                       value="<?php echo htmlspecialchars($provider_profile['phone'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address"
                       value="<?php echo htmlspecialchars($provider_profile['address'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="website" class="form-label">Website</label>
                <input type="url" class="form-control" id="website" name="website"
                       value="<?php echo htmlspecialchars($provider_profile['website'] ?? ''); ?>">
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary"><?php echo $strings['update'] ?? 'Update'; ?></button>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>