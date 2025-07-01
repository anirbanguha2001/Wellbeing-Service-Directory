<?php
session_start();

$lang = $_POST['lang'] ?? '';
$allowed = ['en', 'mi'];

if (!in_array($lang, $allowed)) {
    // If it's an AJAX request, return JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'Invalid language.']);
        exit;
    }
    // Otherwise redirect back
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/wellbeing-directory/public/index.php'));
    exit;
}

// Fix: Use 'lang' instead of 'language' to match other files
$_SESSION['lang'] = $lang;

// Get the referring page to redirect back to it
$redirect_url = $_SERVER['HTTP_REFERER'] ?? '/wellbeing-directory/public/index.php';

// If it's an AJAX request, return JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    echo json_encode(['success' => true, 'message' => 'Language changed successfully.']);
    exit;
}

// Otherwise redirect back to the previous page
header('Location: ' . $redirect_url);
exit;
?>