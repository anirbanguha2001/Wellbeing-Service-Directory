<?php
// General application configuration

// Base URL of the application (change as needed)
define('BASE_URL', 'http://localhost/wellbeing-directory/public');

// Default language
define('DEFAULT_LANGUAGE', 'en');

// Session setup
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Other global config options
define('UPLOADS_DIR', __DIR__ . '/../uploads/');
define('PROFILE_PIC_DIR', UPLOADS_DIR . 'user_profiles/');
define('SERVICE_IMAGES_DIR', UPLOADS_DIR . 'service_images/');
define('RESOURCES_DIR', UPLOADS_DIR . 'resources/');

// Email settings (for mail.php)
define('MAIL_FROM_ADDRESS', 'no-reply@wellbeing-directory.local');
define('MAIL_FROM_NAME', 'Wellbeing Service Directory');