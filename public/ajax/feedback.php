<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$service_id = $_POST['service_id'] ?? null;
$rating = intval($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if (!$user_id || !$service_id || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid fields.']);
    exit;
}

try {
    $pdo = get_db();
    $stmt = $pdo->prepare("INSERT INTO feedback (user_id, service_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $service_id, $rating, $comment]);
    echo json_encode(['success' => true, 'message' => 'Feedback submitted.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to submit feedback.']);
}