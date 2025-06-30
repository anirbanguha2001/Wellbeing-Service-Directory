<?php
// Database configuration and connection

$DB_CONFIG = [
    'host' => 'localhost',
    'dbname' => 'wellbeing_directory',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4'
];

function get_db() {
    global $DB_CONFIG;
    $dsn = sprintf(
        "mysql:host=%s;dbname=%s;charset=%s",
        $DB_CONFIG['host'],
        $DB_CONFIG['dbname'],
        $DB_CONFIG['charset']
    );
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, $DB_CONFIG['user'], $DB_CONFIG['pass'], $options);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}