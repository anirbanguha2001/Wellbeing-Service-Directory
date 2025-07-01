<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
check_login();

$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';

$pdo = get_db();
$user_id = $_SESSION['user_id'] ?? null;

// Get services for feedback dropdown
$stmt = $pdo->query("SELECT s.id, s.name_en FROM services s ORDER BY s.name_en ASC");
$services = $stmt->fetchAll();

// Handle feedback submission
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = intval($_POST['service_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($service_id && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, service_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $service_id, $rating, $comment]);
        $success = "Thank you for your feedback!";
    } else {
        $error = "Please select a service and provide a valid rating.";
    }
}

// Show recent feedback
$feedbacks = [];
$stmt = $pdo->query("SELECT f.*, u.name AS user_name, s.name_en AS service_name
                     FROM feedback f
                     JOIN users u ON f.user_id = u.id
                     JOIN services s ON f.service_id = s.id
                     ORDER BY f.created_at DESC LIMIT 10");
$feedbacks = $stmt->fetchAll();
?>

<!-- Add Font Awesome in header.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Add this CSS in header.php -->
<style>
    .custom-star {
        color: #ffc107;
        font-size: 1.5rem;
        cursor: pointer;
    }
    .rating-container {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .feedback-card {
        transition: transform 0.2s;
        border-radius: 10px;
    }
    .feedback-card:hover {
        transform: translateY(-3px);
    }
    .rating-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: bold;
    }
    .form-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-comment-dots text-primary me-2"></i>
                    <?php echo $strings['feedback'] ?? 'Feedback'; ?>
                </h2>
                <div class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    <?php echo date('Y-m-d H:i:s'); ?>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php elseif ($success): ?>
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="form-container p-4 mb-5">
                <h4 class="mb-4">Share Your Experience</h4>
                <form method="post">
                    <div class="mb-4">
                        <label for="service_id" class="form-label">
                            <i class="fas fa-hand-holding-medical me-2"></i>
                            <?php echo $strings['services'] ?? 'Services'; ?>
                        </label>
                        <select name="service_id" id="service_id" class="form-select form-select-lg" required>
                            <option value="">-- Select Service --</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['name_en']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="rating" class="form-label">
                            <i class="fas fa-star me-2"></i>
                            Rating
                        </label>
                        <div class="rating-container mb-2">
                            <select name="rating" id="rating" class="form-select form-select-lg" required>
                                <option value="">-- Select Rating --</option>
                                <?php for ($i=1; $i<=5; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="comment" class="form-label">
                            <i class="fas fa-comment me-2"></i>
                            Your Comments
                        </label>
                        <textarea name="comment" id="comment" class="form-control form-control-lg" rows="3" 
                                  placeholder="Share your experience with this service..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-paper-plane me-2"></i>
                        Submit Feedback
                    </button>
                </form>
            </div>

            <div class="recent-feedback">
                <h4 class="mb-4">
                    <i class="fas fa-history me-2"></i>
                    Recent Feedback
                </h4>
                
                <?php if ($feedbacks): ?>
                    <div class="row">
                        <?php foreach ($feedbacks as $fb): ?>
                            <div class="col-12 mb-3">
                                <div class="card feedback-card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0">
                                                <?php echo htmlspecialchars($fb['service_name']); ?>
                                            </h5>
                                            <span class="rating-badge bg-warning text-dark">
                                                <?php echo intval($fb['rating']); ?> 
                                                <i class="fas fa-star"></i>
                                            </span>
                                        </div>
                                        
                                        <?php if ($fb['comment']): ?>
                                            <p class="card-text mb-3">
                                                <i class="fas fa-quote-left text-muted me-2"></i>
                                                <?php echo htmlspecialchars($fb['comment']); ?>
                                                <i class="fas fa-quote-right text-muted ms-2"></i>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="fas fa-user-circle me-2"></i>
                                            <?php echo htmlspecialchars($fb['user_name']); ?>
                                            <span class="mx-2">•</span>
                                            <i class="far fa-calendar-alt me-2"></i>
                                            <?php echo htmlspecialchars(date('Y-m-d', strtotime($fb['created_at']))); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No feedback submitted yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>