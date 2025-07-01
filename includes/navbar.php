<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$lang = $_SESSION['lang'] ?? 'en';
$lang_file = __DIR__ . "/language/lang.$lang.php";
$strings = file_exists($lang_file) ? include $lang_file : include DIR . "/language/lang.en.php";
$user_type = $_SESSION['user_type'] ?? null;
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/wellbeing-directory/public/index.php">
            <?php echo htmlspecialchars($strings['service_directory'] ?? 'Wellbeing Service Directory'); ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($user_type === 'provider'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/wellbeing-directory/public/provider/services.php"><?php echo $strings['services'] ?? 'Services'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/wellbeing-directory/public/provider/appointments.php"><?php echo $strings['my_appointments'] ?? 'My Appointments'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/wellbeing-directory/public/provider/dashboard.php"><?php echo $strings['provider_dashboard'] ?? 'Provider Dashboard'; ?></a>
                    </li>
                <?php elseif ($user_type === 'community'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/wellbeing-directory/public/service-directory.php"><?php echo $strings['services'] ?? 'Services'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/wellbeing-directory/public/resources.php"><?php echo $strings['resources'] ?? 'Resources'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/wellbeing-directory/public/dashboard.php"><?php echo $strings['dashboard'] ?? 'Dashboard'; ?></a>
                    </li>
                <?php elseif ($user_type === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/wellbeing-directory/public/admin/index.php"><?php echo $strings['admin_panel'] ?? 'Admin Panel'; ?></a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/wellbeing-directory/public/service-directory.php"><?php echo $strings['services'] ?? 'Services'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/wellbeing-directory/public/resources.php"><?php echo $strings['resources'] ?? 'Resources'; ?></a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="/wellbeing-directory/public/feedback.php"><?php echo $strings['feedback'] ?? 'Feedback'; ?></a>
                </li>
            </ul>
            <form method="post" action="/wellbeing-directory/public/ajax/language.php" class="d-flex me-2" id="langForm">
                <select name="lang" class="form-select form-select-sm" onchange="document.getElementById('langForm').submit()">
                    <option value="en" <?php echo $lang === 'en' ? 'selected' : ''; ?>>English</option>
                    <option value="mi" <?php echo $lang === 'mi' ? 'selected' : ''; ?>>Te Reo Māori</option>
                </select>
            </form>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="d-flex align-items-center">
                    <span class="navbar-text me-3"><?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></span>
                    <a class="btn btn-outline-danger" href="/wellbeing-directory/public/logout.php"><?php echo $strings['logout'] ?? 'Logout'; ?></a>
                </div>
            <?php else: ?>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-primary" href="/wellbeing-directory/public/login.php"><?php echo $strings['login'] ?? 'Login'; ?></a>
                    <a class="btn btn-primary" href="/wellbeing-directory/public/register.php"><?php echo $strings['register'] ?? 'Register'; ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>