<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/functions.php';

$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $userType = $_POST['user_type'] ?? 'community'; // Default user type

    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Database connection
        $pdo = get_db(); // Assuming get_db() returns a PDO instance

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = 'Email already registered.';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            // Insert new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashedPassword, $userType])) {
                $success = 'Registration successful! You can now log in.';
                // Clear form data
                $_POST = [];
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!-- Add Font Awesome and custom CSS in header.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    body {
        background: linear-gradient(135deg, #a0e0a6 0%, #7ec485 100%);
        min-height: 100vh;
    }

    .register-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 2.5rem;
        max-width: 600px;
        width: 90%;
        margin: 2rem auto;
    }

    .status-bar {
        background: rgba(255, 255, 255, 0.9);
        padding: 0.8rem 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
        color: #2c5233;
    }

    .form-control, .form-select {
        border: 2px solid rgba(44, 82, 51, 0.1);
        border-radius: 10px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
    }

    .form-control:focus, .form-select:focus {
        box-shadow: none;
        border-color: #2c5233;
        background: white;
    }

    .input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #2c5233;
        z-index: 10;
    }

    .input-with-icon {
        padding-left: 2.8rem;
    }

    .btn-register {
        background: #2c5233;
        border: none;
        border-radius: 10px;
        padding: 1rem 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        width: 100%;
        color: white;
    }

    .btn-register:hover {
        background: #1a3720;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(44, 82, 51, 0.2);
    }

    .register-title {
        color: #2c5233;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .alert-custom {
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .alert-custom.alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }

    .alert-custom.alert-success {
        background: rgba(40, 167, 69, 0.1);
        border: 1px solid rgba(40, 167, 69, 0.2);
        color: #28a745;
    }

    .login-link {
        color: #2c5233;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .login-link:hover {
        color: #1a3720;
        text-decoration: underline;
    }
</style>

<div class="container d-flex flex-column justify-content-center align-items-center py-5">
    <div class="register-container">

        <h2 class="register-title">
            <i class="fas fa-user-plus me-2"></i>
            <?php echo $strings['register'] ?? 'Create Account'; ?>
        </h2>

        <?php if ($error): ?>
            <div class="alert alert-custom alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif ($success): ?>
            <div class="alert alert-custom alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <div class="input-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" 
                       class="form-control input-with-icon" 
                       id="name" 
                       name="name" 
                       placeholder="<?php echo $strings['name'] ?? 'Full Name'; ?>"
                       required 
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="input-group">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" 
                       class="form-control input-with-icon" 
                       id="email" 
                       name="email" 
                       placeholder="<?php echo $strings['email'] ?? 'Email Address'; ?>"
                       required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" 
                       class="form-control input-with-icon" 
                       id="password" 
                       name="password" 
                       placeholder="<?php echo $strings['password'] ?? 'Password'; ?>"
                       required>
            </div>

            <div class="input-group">
                <i class="fas fa-key input-icon"></i>
                <input type="password" 
                       class="form-control input-with-icon" 
                       id="confirm_password" 
                       name="confirm_password" 
                       placeholder="<?php echo $strings['confirm_password'] ?? 'Confirm Password'; ?>"
                       required>
            </div>

            <div class="input-group">
                <i class="fas fa-users input-icon"></i>
                <select class="form-select input-with-icon" id="user_type" name="user_type" required>
                    <option value="community"<?php if (($_POST['user_type'] ?? '') === 'community') echo ' selected'; ?>>Community Member</option>
                    <option value="provider"<?php if (($_POST['user_type'] ?? '') === 'provider') echo ' selected'; ?>>Service Provider</option>
                </select>
            </div>

            <button type="submit" class="btn btn-register mb-4">
                <i class="fas fa-user-plus me-2"></i>
                <?php echo $strings['register'] ?? 'Create Account'; ?>
            </button>
        </form>

        <div class="text-center">
            <p class="mb-0">
                <?php echo $strings['login'] ?? 'Already have an account?'; ?> 
                <a href="login.php" class="login-link">
                    <?php echo $strings['login'] ?? 'Sign in'; ?>
                    <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>