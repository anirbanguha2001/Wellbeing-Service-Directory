<?php
require_once __DIR__ . '/../../includes/auth.php';
check_login();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/functions.php';

$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'] ?? '';
if ($user_type !== 'provider') {
    header("Location: dashboard.php");
    exit;
}

$pdo = get_db();
$error = '';
$success = '';

// Add new service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name_en = trim($_POST['name_en'] ?? '');
    $description_en = trim($_POST['description_en'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $image = null;

    // Handle image upload (optional)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $img_name = uniqid('svc_', true) . '.' . $ext;
        $img_path = __DIR__ . '/../uploads/service_images/' . $img_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $img_path)) {
            $image = $img_name;
        }
    }

    if ($name_en && $description_en) {
        $stmt = $pdo->prepare("INSERT INTO services (provider_id, name_en, description_en, location, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $name_en, $description_en, $location, $image]);
        $success = "Service added successfully!";
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Delete service
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $sid = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ? AND provider_id = ?");
    $stmt->execute([$sid, $user_id]);
    $success = "Service deleted.";
}

// Fetch provider's services
$stmt = $pdo->prepare("SELECT * FROM services WHERE provider_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$services = $stmt->fetchAll();
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

    .add-service-card {
        background: white;
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .card-header {
        background: rgba(44, 82, 51, 0.05);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        font-weight: 600;
        color: #2c5233;
    }

    .form-control {
        border: 2px solid rgba(44, 82, 51, 0.1);
        border-radius: 10px;
        padding: 0.8rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #2c5233;
        box-shadow: none;
    }

    .btn-submit {
        background: #2c5233;
        border: none;
        border-radius: 50px;
        padding: 0.8rem 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background: #1a3720;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(44, 82, 51, 0.2);
    }

    .service-card {
        background: white;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .service-image {
        height: 200px;
        object-fit: cover;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .service-title {
        color: #2c5233;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .location-badge {
        background: rgba(44, 82, 51, 0.1);
        color: #2c5233;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .btn-delete {
        background: #dc3545;
        border: none;
        border-radius: 50px;
        padding: 0.8rem 2rem;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-delete:hover {
        background: #bb2d3b;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.2);
    }

    .alert {
        border-radius: 15px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
    }
</style>

<div class="container py-4">

    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-0">
            <i class="fas fa-hand-holding-heart me-2"></i>
            My Services
        </h2>
        <p class="mb-0 mt-2 opacity-75">Manage your service offerings and descriptions</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- Add Service Form -->
    <div class="add-service-card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-2"></i>
            Add New Service
        </div>
        <div class="card-body p-4">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-tag me-2"></i>
                        Service Name (English)
                    </label>
                    <input type="text" name="name_en" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-align-left me-2"></i>
                        Description (English)
                    </label>
                    <textarea name="description_en" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Location
                    </label>
                    <input type="text" name="location" class="form-control">
                </div>
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-image me-2"></i>
                        Service Image
                    </label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <button type="submit" name="add_service" class="btn btn-submit">
                    <i class="fas fa-plus-circle me-2"></i>
                    Add Service
                </button>
            </form>
        </div>
    </div>

    <!-- Listed Services -->
    <h4 class="mb-4">
        <i class="fas fa-list me-2"></i>
        Your Listed Services
    </h4>

    <?php if (empty($services)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No services found.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($services as $svc): ?>
                <div class="col-md-6">
                    <div class="service-card h-100">
                        <?php if ($svc['image'] && file_exists('./uploads/service_images/' . $svc['image'])): ?>
                            <img src="./uploads/service_images/<?php echo htmlspecialchars($svc['image']); ?>" 
                                 class="service-image w-100" 
                                 alt="<?php echo htmlspecialchars($svc['name_en']); ?>">
                        <?php endif; ?>
                        <div class="card-body p-4">
                            <h5 class="service-title">
                                <?php echo htmlspecialchars($svc['name_en']); ?>
                            </h5>
                            <p class="text-muted mb-3">
                                <?php echo nl2br(htmlspecialchars($svc['description_en'])); ?>
                            </p>
                            <?php if ($svc['location']): ?>
                                <div class="location-badge">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?php echo htmlspecialchars($svc['location']); ?>
                                </div>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $svc['id']; ?>" 
                               class="btn btn-delete w-100" 
                               onclick="return confirm('Are you sure you want to delete this service?');">
                                <i class="fas fa-trash-alt me-2"></i>
                                Delete Service
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>