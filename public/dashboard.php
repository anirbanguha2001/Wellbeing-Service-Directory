<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

check_login();
$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';
$user_name = $_SESSION['name'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'community';
?>

<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body {
        background:rgb(190, 231, 196);
        color: #2c3e50;
    }

    .dashboard-container {
        padding: 2rem;
    }

    .status-bar {
        background: linear-gradient(135deg,rgb(35, 71, 121) 0%, #20c997 100%);
        color: white;
        padding: 1rem 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(30, 76, 129, 0.2);
    }

    .dashboard-card {
        background: white;
        border-radius: 15px;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-bottom: 1rem;
        font-size: 24px;
    }

    .quick-action-card {
        background: white;
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .action-item {
        padding: 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .action-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }

    .welcome-section {
        background: linear-gradient(135deg,rgb(22, 107, 31) 0%, #7ec485 100%);
        padding: 2rem;
        border-radius: 15px;
        color: white;
        margin-bottom: 2rem;
    }

    .nav-card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .nav-card:hover {
        transform: translateY(-5px);
    }

    .nav-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
</style>

<div class="dashboard-container">
    <!-- Status Bar -->
    <div class="status-bar d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-user-circle me-2"></i>
            <?php echo htmlspecialchars($user_name); ?>
        </div>
    </div>

    <div class="row">
        <!-- Main Content Area -->
        <div class="col-lg-8 mb-4">
            <!-- Welcome Section -->
            <div class="welcome-section mb-4 shadow-sm">
                <h2 class="mb-3">
                    <i class="fas fa-smile-beam me-2"></i>
                    <?php echo $strings['welcome'] ?? 'Welcome Back'; ?>, <?php echo htmlspecialchars($user_name); ?>!
                </h2>
                <p class="mb-0 opacity-75">
                    <?php 
                        switch($user_type) {
                            case 'provider':
                                echo 'Manage your services and appointments';
                                break;
                            case 'community':
                                echo 'Explore services and manage your appointments';
                                break;
                            case 'admin':
                                echo 'Access administrative controls and overview';
                                break;
                        }
                    ?>
                </p>
            </div>

            <!-- Navigation Cards -->
            <div class="row g-4">
                <?php if ($user_type === 'provider'): ?>
                    <!-- Provider Cards -->
                    <div class="col-md-4">
                        <a href="\wellbeing-directory\public\provider\services.php" class="text-decoration-none">
                            <div class="dashboard-card nav-card p-4 text-center">
                                <div class="feature-icon bg-success bg-opacity-10 mx-auto">
                                    <i class="fas fa-briefcase text-success"></i>
                                </div>
                                <h5 class="text-success"><?php echo $strings['my_services'] ?? 'My Services'; ?></h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="\wellbeing-directory\public\provider\appointments.php" class="text-decoration-none">
                            <div class="dashboard-card nav-card p-4 text-center">
                                <div class="feature-icon bg-primary bg-opacity-10 mx-auto">
                                    <i class="fas fa-calendar-check text-primary"></i>
                                </div>
                                <h5 class="text-primary"><?php echo $strings['appointments'] ?? 'Appointments'; ?></h5>
                            </div>
                        </a>
                    </div>
                <?php elseif ($user_type === 'community'): ?>
                    <!-- Community Cards -->
                    <div class="col-md-4">
                        <a href="\wellbeing-directory\public\service-directory.php" class="text-decoration-none">
                            <div class="dashboard-card nav-card p-4 text-center">
                                <div class="feature-icon bg-primary bg-opacity-10 mx-auto">
                                    <i class="fas fa-search text-primary"></i>
                                </div>
                                <h5 class="text-primary"><?php echo $strings['services'] ?? 'Services'; ?></h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="\wellbeing-directory\public\appointments.php" class="text-decoration-none">
                            <div class="dashboard-card nav-card p-4 text-center">
                                <div class="feature-icon bg-success bg-opacity-10 mx-auto">
                                    <i class="fas fa-calendar-check text-success"></i>
                                </div>
                                <h5 class="text-success"><?php echo $strings['my_appointments'] ?? 'My Appointments'; ?></h5>
                            </div>
                        </a>
                    </div>
                <?php elseif ($user_type === 'admin'): ?>
                    <!-- Admin Card -->
                    <div class="col-md-4">
                        <a href="\wellbeing-directory\public\admin\index.php" class="text-decoration-none">
                            <div class="dashboard-card nav-card p-4 text-center">
                                <div class="feature-icon bg-warning bg-opacity-10 mx-auto">
                                    <i class="fas fa-cogs text-warning"></i>
                                </div>
                                <h5 class="text-warning"><?php echo $strings['admin_panel'] ?? 'Admin Panel'; ?></h5>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>

                      <div class="col-md-4">
                        <a href="\wellbeing-directory\public\profile.php" class="text-decoration-none">
                            <div class="dashboard-card nav-card p-4 text-center">
                                <div class="feature-icon bg-primary bg-opacity-10 mx-auto">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <h5 class="text-primary"><?php echo $strings['profile'] ?? 'Profile'; ?></h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="\wellbeing-directory\public\messaging.php" class="text-decoration-none">
                            <div class="dashboard-card nav-card p-4 text-center">
                                <div class="feature-icon bg-primary bg-opacity-10 mx-auto">
                                    <i class="fas fa-message text-primary"></i>
                                </div>
                                <h5 class="text-primary"><?php echo $strings['message'] ?? 'Message'; ?></h5>
                            </div>
                        </a>
                    </div>
            </div>
        </div>

        <!-- Quick Actions Sidebar -->
        <div class="col-lg-4">
            <div class="quick-action-card p-4">
                <h4 class="mb-4">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Quick Actions
                </h4>
                <div class="quick-actions">
                    <a href="#" class="text-decoration-none">
                        <div class="action-item d-flex align-items-center">
                            <div class="feature-icon bg-primary bg-opacity-10 me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                <i class="fas fa-calendar-plus text-primary"></i>
                            </div>
                            <span class="text-dark">Schedule Appointment</span>
                        </div>
                    </a>
                    <a href="#" class="text-decoration-none">
                        <div class="action-item d-flex align-items-center">
                            <div class="feature-icon bg-success bg-opacity-10 me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                <i class="fas fa-envelope text-success"></i>
                            </div>
                            <span class="text-dark">Check Messages</span>
                        </div>
                    </a>
                    <a href="#" class="text-decoration-none">
                        <div class="action-item d-flex align-items-center">
                            <div class="feature-icon bg-info bg-opacity-10 me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                <i class="fas fa-cog text-info"></i>
                            </div>
                            <span class="text-dark">Update Settings</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>