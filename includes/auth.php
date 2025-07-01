<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function is_admin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function is_provider() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'provider';
}

function is_community_member() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'community';
}

function require_role($role) {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== $role) {
        header('Location: /login.php');
        exit;
    }
}