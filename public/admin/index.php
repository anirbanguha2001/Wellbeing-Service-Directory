<?php
require_once __DIR__ . '/../../includes/auth.php';
check_login();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

$user_type = $_SESSION['user_type'] ?? '';
if ($user_type !== 'admin') {
    header('Location: /wellbeing-directory/public/index.php');
    exit;
}
?>
<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body {
        background: linear-gradient(135deg, rgb(128, 169, 192) 0%, rgb(220, 228, 191) 100%);
        min-height: 100vh;
        color: #2c3e50;
    }

    .status-bar {
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .page-header {
        background: linear-gradient(135deg, #43a047 0%, rgb(79, 168, 76) 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(67, 160, 71, 0.2);
    }

    .admin-card {
        background: white;
        border-radius: 15px;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        height: 100%;
    }

    .admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .admin-link {
        text-decoration: none;
        color: inherit;
    }

    .card-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        background: rgba(44, 82, 51, 0.1);
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #2c5233;
    }

    .stats-badge {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: rgba(44, 82, 51, 0.1);
        color: #2c5233;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .quick-stats {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="container py-4">
    <!-- Status Bar -->
    <div class="d-flex justify-content-between align-items-center">
       
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-0">
            <i class="fas fa-cogs me-2"></i>
            Admin Dashboard
        </h2>
        <p class="mb-0 mt-2 opacity-75">Manage and monitor all system components</p>
    </div>

    <!-- Admin Cards -->
    <div class="row g-4">
        <!-- Users Card -->
        <div class="col-md-4">
            <a href="users.php" class="admin-link">
                <div class="admin-card p-4 text-center position-relative">
                    <div class="card-icon mx-auto">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="mb-2">Manage Users</h4>
                    <p class="text-muted mb-0">Manage user accounts and permissions</p>
                    <span class="stats-badge">150 Users</span>
                </div>
            </a>
        </div>

        <!-- Services Card -->
        <div class="col-md-4">
            <a href="services.php" class="admin-link">
                <div class="admin-card p-4 text-center position-relative">
                    <div class="card-icon mx-auto">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h4 class="mb-2">Manage Services</h4>
                    <p class="text-muted mb-0">Monitor and control service listings</p>
                    <span class="stats-badge">45 Services</span>
                </div>
            </a>
        </div>

        <!-- Appointments Card -->
        <div class="col-md-4">
            <a href="appointments.php" class="admin-link">
                <div class="admin-card p-4 text-center position-relative">
                    <div class="card-icon mx-auto">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4 class="mb-2">Manage Appointments</h4>
                    <p class="text-muted mb-0">Track and manage bookings</p>
                    <span class="stats-badge">28 Active</span>
                </div>
            </a>
        </div>

        <!-- Resources Card -->
        <div class="col-md-6">
            <a href="resources.php" class="admin-link">
                <div class="admin-card p-4 text-center position-relative">
                    <div class="card-icon mx-auto">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h4 class="mb-2">Manage Resources</h4>
                    <p class="text-muted mb-0">Update and organize resources</p>
                    <span class="stats-badge">65 Items</span>
                </div>
            </a>
        </div>

        <!-- Feedback Card -->
        <div class="col-md-6">
            <a href="feedback.php" class="admin-link">
                <div class="admin-card p-4 text-center position-relative">
                    <div class="card-icon mx-auto">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h4 class="mb-2">Manage Feedback</h4>
                    <p class="text-muted mb-0">Review user feedback and ratings</p>
                    <span class="stats-badge">92 Reviews</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats mt-4">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="fas fa-chart-line text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">System Status</h6>
                        <small class="text-success">All Systems Operational</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="fas fa-user-check text-success"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Active Users</h6>
                        <small class="text-muted">32 Online Now</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="fas fa-bell text-warning"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Notifications</h6>
                        <small class="text-muted">3 New Alerts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="fas fa-server text-info"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Server Load</h6>
                        <small class="text-muted">23% Capacity</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>