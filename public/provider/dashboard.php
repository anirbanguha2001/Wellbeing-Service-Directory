<?php
require_once __DIR__ . '/../../includes/auth.php';
check_login();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/functions.php';

$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';

$user_name = $_SESSION['name'] ?? '';
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'] ?? '';

if ($user_type !== 'provider') {
    header("Location: dashboard.php");
    exit;
}

// Stats for provider
$pdo = get_db();
$total_services = $pdo->query("SELECT COUNT(*) FROM services WHERE provider_id = $user_id")->fetchColumn();
$total_appointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE provider_id = $user_id")->fetchColumn();

?>
<!-- Add Font Awesome and custom CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body {
        background: linear-gradient(135deg, rgb(128, 169, 192) 0%, rgb(220, 228, 191) 100%);
        min-height: 100vh;
    }

    .dashboard-container {
        padding: 2rem;
    }

    .status-bar {
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .welcome-section {
        background: linear-gradient(135deg, #43a047 0%, rgb(79, 168, 76) 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(67, 160, 71, 0.2);
    }

    .stats-card {
        background: white;
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stats-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .stats-body {
        padding: 2rem;
        text-align: center;
    }

    .stats-number {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1;
    }

    .stats-text {
        color: #666;
        margin-bottom: 1.5rem;
    }

    .btn-dashboard {
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-dashboard:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .services-card {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .appointments-card {
        background: linear-gradient(135deg, #43a047 0%, #7cb342 100%);
    }

    .quick-stats {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="dashboard-container">
    <!-- Status Bar -->
    <div class="status-bar d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-user-circle me-2"></i>
            <span class="fw-bold"><?php echo htmlspecialchars($user_name); ?></span>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-0">
                    <i class="fas fa-clinic-medical me-2"></i>
                    Provider Dashboard
                </h2>
                <p class="mb-0 mt-2 opacity-75">
                    Manage your services and appointments efficiently
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-light text-dark p-2 px-3 rounded-pill">
                    <i class="fas fa-star me-1 text-warning"></i>
                    Professional Provider
                </span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4">
        <!-- Services Card -->
        <div class="col-md-6">
            <div class="stats-card h-100">
                <div class="stats-header d-flex align-items-center">
                    <i class="fas fa-hand-holding-medical fa-2x text-primary me-3"></i>
                    <h3 class="mb-0">My Services</h3>
                </div>
                <div class="stats-body">
                    <div class="stats-number text-primary"><?php echo $total_services; ?></div>
                    <p class="stats-text">Active services in your portfolio</p>
                    <a href="services.php" class="btn btn-primary btn-dashboard">
                        <i class="fas fa-cog me-2"></i>
                        Manage Services
                    </a>
                </div>
            </div>
        </div>

        <!-- Appointments Card -->
        <div class="col-md-6">
            <div class="stats-card h-100">
                <div class="stats-header d-flex align-items-center">
                    <i class="fas fa-calendar-check fa-2x text-success me-3"></i>
                    <h3 class="mb-0">My Appointments</h3>
                </div>
                <div class="stats-body">
                    <div class="stats-number text-success"><?php echo $total_appointments; ?></div>
                    <p class="stats-text">Total appointments scheduled</p>
                    <a href="appointments.php" class="btn btn-success btn-dashboard">
                        <i class="fas fa-calendar me-2"></i>
                        View Appointments
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="fas fa-star text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Service Rating</h6>
                        <small class="text-muted">4.8/5.0 Average</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="fas fa-users text-success"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Total Clients</h6>
                        <small class="text-muted">23 Active Clients</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>