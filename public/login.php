<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/functions.php';

$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $pdo = get_db();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['name'] = $user['name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = $strings['login'] . ' failed. Invalid credentials.';
    }
}
?>

<!-- Add Font Awesome and custom CSS in header.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    body {
        background-color: rgb(160, 224, 166);
        min-height: 100vh;
    }

    .login-container {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 2.5rem;
        max-width: 450px;
        width: 90%;
        margin: 2rem auto;
    }

    .time-badge {
        background: rgba(255, 255, 255, 0.9);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        color: #2c5233;
        margin-bottom: 1.5rem;
        display: inline-block;
    }

    .form-control {
        border: 2px solid rgba(44, 82, 51, 0.1);
        border-radius: 10px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
    }

    .form-control:focus {
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

    .btn-login {
        background: #2c5233;
        border: none;
        border-radius: 10px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-login:hover {
        background: #1a3720;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(44, 82, 51, 0.2);
    }

    .login-title {
        color: #2c5233;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .register-link {
        color: #2c5233;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .register-link:hover {
        color: #1a3720;
        text-decoration: underline;
    }

    .alert-custom {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border-radius: 10px;
        padding: 1rem;
    }
</style>

<div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 py-5">
    <div class="login-container">

        
        <h2 class="login-title">
            <i class="fas fa-leaf me-2"></i>
            <?php echo $strings['login'] ?? 'Login'; ?>
        </h2>

        <?php if ($error): ?>
            <div class="alert alert-custom mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="input-group">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" 
                       class="form-control input-with-icon" 
                       id="email" 
                       name="email" 
                       placeholder="<?php echo $strings['email'] ?? 'Email'; ?>"
                       required 
                       autofocus>
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

            <button type="submit" class="btn btn-login btn-lg mb-4">
                <i class="fas fa-sign-in-alt me-2"></i>
                <?php echo $strings['login'] ?? 'Login'; ?>
            </button>
        </form>

        <div class="text-center">
            <p class="mb-0">
                <?php echo $strings['register'] ?? 'New here?'; ?> 
                <a href="register.php" class="register-link">
                    <?php echo $strings['register'] ?? 'Create an account'; ?>
                    <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>