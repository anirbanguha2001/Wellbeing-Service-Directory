<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';
?>

<!-- Add Font Awesome and custom CSS in header.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .hero-section {
        background: linear-gradient(135deg,rgb(18, 131, 134) 0%,rgb(181, 212, 67) 100%);
        color: white;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
        background-size: cover;
    }

    .feature-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        background: white;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 24px;
        margin-bottom: 1rem;
    }

    .btn-hero {
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    .btn-hero:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .current-time {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .welcome-text {
        font-size: 3.5rem;
        font-weight: 700;
        background: linear-gradient(to right, #fff, #e0e0e0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<div class="container py-5">
    <!-- Hero Section -->
    <div class="hero-section p-5 mb-5 position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="welcome-text mb-4">
                    <?php echo $strings['welcome'] ?? 'Welcome'; ?>!
                </h1>
                <p class="lead mb-4 fs-4 opacity-90">
                    <?php echo $strings['service_directory'] ?? 'wellbeing-directory\public\register.php'; ?>
                </p>
                <div class="d-flex gap-3 mb-4">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a class="btn btn-light btn-hero" href="login.php">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            <?php echo $strings['login'] ?? 'Login'; ?>
                        </a>
                        <a class="btn btn-outline-light btn-hero" href="wellbeing-directory\public\register.php">
                            <i class="fas fa-user-plus me-2"></i>
                            <?php echo $strings['register'] ?? 'Register'; ?>
                        </a>
                    <?php else: ?>
                        <a class="btn btn-light btn-hero" href="dashboard.php">
                            <i class="fas fa-columns me-2"></i>
                            <?php echo $strings['dashboard'] ?? 'Dashboard'; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="feature-card shadow-sm p-4 h-100">
                <div class="feature-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3 class="h4 mb-3"><?php echo $strings['services'] ?? 'Services'; ?></h3>
                <p class="text-muted mb-0">
                    Access a comprehensive directory of wellbeing services tailored to your needs.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card shadow-sm p-4 h-100">
                <div class="feature-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-book-reader"></i>
                </div>
                <h3 class="h4 mb-3"><?php echo $strings['resources'] ?? 'Resources'; ?></h3>
                <p class="text-muted mb-0">
                    Discover valuable resources and information to support your wellbeing journey.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card shadow-sm p-4 h-100">
                <div class="feature-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <h3 class="h4 mb-3"><?php echo $strings['feedback'] ?? 'Feedback'; ?></h3>
                <p class="text-muted mb-0">
                    Share your experiences and help us improve our services for everyone.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>