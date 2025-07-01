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
<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body {
        background: linear-gradient(135deg, rgb(128, 169, 192) 0%, rgb(220, 228, 191) 100%);
        min-height: 100vh;
        color: #2c3e50;
    }

    .status-bar {
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .page-header {
        background: linear-gradient(135deg, #43a047 0%, rgb(79, 168, 76) 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(67, 160, 71, 0.2);
    }

    .message-compose {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .form-control, .form-select {
        border: 2px solid rgba(44, 82, 51, 0.1);
        border-radius: 10px;
        padding: 0.8rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #2c5233;
        box-shadow: none;
    }

    .form-label {
        font-weight: 600;
        color: #2c5233;
        margin-bottom: 0.5rem;
    }

    .btn-send {
        background: #2c5233;
        border: none;
        border-radius: 50px;
        padding: 0.8rem 2rem;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-send:hover {
        background: #1a3720;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(44, 82, 51, 0.2);
    }

    .messages-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .message-item {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: none;
        transition: all 0.3s ease;
    }

    .message-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .message-item.unread {
        background: rgba(13, 110, 253, 0.1);
    }

    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .message-sender {
        font-weight: 600;
        color: #2c5233;
    }

    .message-arrow {
        color: #6c757d;
        margin: 0 0.5rem;
    }

    .message-receiver {
        color: #6c757d;
    }

    .message-content {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        margin: 0.5rem 0;
    }

    .message-time {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .user-type-badge {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.8rem;
    }
</style>

<div class="container py-4" style="max-width:800px;">
    <!-- Status Bar -->
    <div class="d-flex justify-content-between align-items-center">
       
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-0">
            <i class="fas fa-comments me-2"></i>
            Messages
        </h2>
        <p class="mb-0 mt-2 opacity-75">Send and receive messages with other users</p>
    </div>

    <!-- New Message Form -->
    <div class="message-compose">
        <h4 class="mb-4">
            <i class="fas fa-paper-plane me-2"></i>
            New Message
        </h4>
        <form method="post">
            <div class="mb-3">
                <label for="receiver_id" class="form-label">
                    <i class="fas fa-user me-2"></i>Send To
                </label>
                <select name="receiver_id" id="receiver_id" class="form-select" required>
                    <option value="">-- Select Recipient --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>">
                            <?php echo htmlspecialchars($user['name']); ?> 
                            (<?php echo htmlspecialchars(ucfirst($user['user_type'])); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Message
                </label>
                <textarea name="message" id="message" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-send">
                <i class="fas fa-paper-plane me-2"></i>
                Send Message
            </button>
        </form>
    </div>

    <!-- Messages List -->
    <div class="messages-container">
        <h4 class="mb-4">
            <i class="fas fa-inbox me-2"></i>
            Recent Messages
        </h4>
        
        <?php if ($messages): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message-item <?php echo ($msg['receiver_id'] == $user_id && !$msg['is_read']) ? 'unread' : ''; ?>">
                    <div class="message-header">
                        <div>
                            <span class="message-sender">
                                <i class="fas fa-user-circle me-2"></i>
                                <?php echo htmlspecialchars($msg['sender_name']); ?>
                            </span>
                            <span class="message-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </span>
                            <span class="message-receiver">
                                <?php echo htmlspecialchars($msg['receiver_name']); ?>
                            </span>
                        </div>
                        <div class="message-time">
                            <i class="far fa-clock me-1"></i>
                            <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($msg['sent_at']))); ?>
                        </div>
                    </div>
                    <div class="message-content">
                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No messages yet</h5>
                <p class="text-muted mb-0">Start a conversation by sending a message</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>