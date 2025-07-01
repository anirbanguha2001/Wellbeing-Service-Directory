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
$provider_id = $_POST['provider_id'] ?? null;
$appointment_datetime = $_POST['appointment_datetime'] ?? null;
$notes = $_POST['notes'] ?? '';

if (!$user_id || !$service_id || !$provider_id || !$appointment_datetime) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

try {
    $pdo = get_db();
    $stmt = $pdo->prepare("INSERT INTO appointments (user_id, service_id, provider_id, appointment_datetime, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $service_id, $provider_id, $appointment_datetime, $notes]);
    echo json_encode(['success' => true, 'message' => 'Appointment booked.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Booking failed.']);
}