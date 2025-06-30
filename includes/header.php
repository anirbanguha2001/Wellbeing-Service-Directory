<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$lang = $_SESSION['lang'] ?? 'en';
// Fix: Use DIR instead of DIR
$lang_file = __DIR__ . "/language/lang.$lang.php";
$strings = file_exists($lang_file) ? include $lang_file : include DIR . "/language/lang.en.php";
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($strings['service_directory'] ?? 'Wellbeing Service Directory'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Simple and clean custom styles -->
    <style>
        body {
            background-color:rgb(160, 224, 166);
        }
        .navbar {
            background-color:rgb(156, 130, 190);
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .nav-link {
            color: black;
            padding: 0.5rem 1rem;
        }
        .nav-link:hover {
            color:rgb(131, 185, 165);
        }
        .btn {
            padding: 0.375rem 1.5rem;
        }
        .form-select {
            min-width: 120px;
        }
    </style>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">