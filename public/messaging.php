<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

check_login();
$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';
$pdo = get_db();
$user_id = $_SESSION['user_id'] ?? 0;

// Get users (for messaging dropdown, only providers for community, only community for providers)
$stmt = $pdo->prepare("SELECT id, name, user_type FROM users WHERE id != ?");
$stmt->execute([$user_id]);
$users = $stmt->fetchAll();

// Handle new message
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = intval($_POST['receiver_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($receiver_id && $message) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $receiver_id, $message]);
        $success = "Message sent!";
    } else {
        $error = "Please select a recipient and type a message.";
    }
}

// Mark all messages as read for this user
$pdo->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ?")->execute([$user_id]);

// Fetch recent messages (either sent or received)
$stmt = $pdo->prepare("SELECT m.*, u1.name AS sender_name, u2.name AS receiver_name
                       FROM messages m
                       JOIN users u1 ON m.sender_id = u1.id
                       JOIN users u2 ON m.receiver_id = u2.id
                       WHERE m.sender_id = ? OR m.receiver_id = ?
                       ORDER BY m.sent_at DESC
                       LIMIT 20");
$stmt->execute([$user_id, $user_id]);
$messages = $stmt->fetchAll();
?>
<div class="container mt-4" style="max-width:700px;">
    <h2><?php echo $strings['messages'] ?? 'Messages'; ?></h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form method="post" class="mb-4">
        <div class="mb-3">
            <label for="receiver_id" class="form-label">Send To</label>
            <select name="receiver_id" id="receiver_id" class="form-select" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['user_type']); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea name="message" id="message" class="form-control" rows="2" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
    <h4>Recent Messages</h4>
    <?php if ($messages): ?>
        <ul class="list-group">
        <?php foreach ($messages as $msg): ?>
            <li class="list-group-item<?php echo ($msg['receiver_id'] == $user_id && !$msg['is_read']) ? ' list-group-item-info' : ''; ?>">
                <strong><?php echo htmlspecialchars($msg['sender_name']); ?></strong>
                <span class="text-muted">→ <?php echo htmlspecialchars($msg['receiver_name']); ?></span><br>
                <em><?php echo nl2br(htmlspecialchars($msg['message'])); ?></em><br>
                <small><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($msg['sent_at']))); ?></small>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info mt-3">No messages yet.</div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>