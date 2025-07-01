<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$query = trim($_GET['q'] ?? '');
$language = $_GET['language'] ?? ($_SESSION['language'] ?? 'en');

if ($query === '') {
    echo json_encode(['success' => false, 'message' => 'No search term.']);
    exit;
}

$column = $language === 'mi' ? 'name_mi' : 'name_en';

try {
    $pdo = get_db();
    $stmt = $pdo->prepare("SELECT id, $column AS name, provider_id, category, location FROM services WHERE $column LIKE ? LIMIT 10");
    $stmt->execute(['%' . $query . '%']);
    $results = $stmt->fetchAll();
    echo json_encode(['success' => true, 'results' => $results]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Search failed.']);
}