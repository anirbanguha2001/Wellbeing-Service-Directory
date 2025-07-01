<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$action = $_POST['action'] ?? '';

if ($action === 'send') {
    $sender_id = $_SESSION['user_id'] ?? null;
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message = trim($_POST['message'] ?? '');

    if (!$sender_id || !$receiver_id || $message === '') {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    try {
        $pdo = get_db();
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$sender_id, $receiver_id, $message]);
        echo json_encode(['success' => true, 'message' => 'Message sent.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
    }
    exit;
}

if ($action === 'fetch') {
    $user_id = $_SESSION['user_id'] ?? null;
    $with_id = $_POST['with_id'] ?? null;

    if (!$user_id || !$with_id) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    try {
        $pdo = get_db();
        $stmt = $pdo->prepare("SELECT id, sender_id, receiver_id, message, sent_at, is_read FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_at ASC");
        $stmt->execute([$user_id, $with_id, $with_id, $user_id]);
        $messages = $stmt->fetchAll();
        // Optionally mark messages as read
        $pdo->prepare("UPDATE messages SET is_read=1 WHERE receiver_id=? AND sender_id=? AND is_read=0")->execute([$user_id, $with_id]);
        echo json_encode(['success' => true, 'messages' => $messages]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch messages.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);