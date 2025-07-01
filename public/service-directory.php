<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
check_login();

$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';

$pdo = get_db();

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $searchSql = "%{$search}%";
    $stmt = $pdo->prepare(
        "SELECT s.*, u.name AS provider_name
         FROM services s
         JOIN users u ON s.provider_id = u.id
         WHERE s.name_en LIKE ? 
            OR s.description_en LIKE ?
            OR s.location LIKE ?
            OR u.name LIKE ?
         ORDER BY s.created_at DESC"
    );
    $stmt->execute([$searchSql, $searchSql, $searchSql, $searchSql]);
} else {
    $stmt = $pdo->query("SELECT s.*, u.name AS provider_name FROM services s JOIN users u ON s.provider_id = u.id ORDER BY s.created_at DESC");
}
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
        background: rgba(255, 255, 255, 0.9);
        padding: 1rem 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-header {
        background: linear-gradient(135deg, #43a047 0%, rgb(79, 168, 76) 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(67, 160, 71, 0.2);
    }

    .search-form {
        margin-bottom: 2rem;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .search-input {
        max-width: 400px;
        border-radius: 50px 0 0 50px;
        border-right: none;
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
        outline: none;
        border: 1px solid #2c5233;
        background: #fff;
    }
    .search-btn {
        border-radius: 0 50px 50px 0;
        border: 1px solid #2c5233;
        border-left: none;
        background: #2c5233;
        color: #fff;
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
        transition: background 0.2s;
    }
    .search-btn:hover {
        background: #1a3720;
    }

    .service-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .service-image {
        height: 200px;
        object-fit: cover;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .card-title {
        color: #2c5233;
        font-weight: 600;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .card-text {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .provider-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(67, 160, 71, 0.1);
        color: #2c5233;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .location-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(0, 123, 255, 0.1);
        color: #0d6efd;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
    }

    .book-btn {
        background: #2c5233;
        border: none;
        border-radius: 50px;
        padding: 0.8rem 2rem;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .book-btn:hover {
        background: #1a3720;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(44, 82, 51, 0.2);
        color: white;
    }

    .service-info {
        padding: 1.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .no-services {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
    }
</style>

<div class="container py-4"> 

    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-0">
            <i class="fas fa-hand-holding-heart me-2"></i>
            <?php echo $strings['services'] ?? 'Available Services'; ?>
        </h2>
        <p class="mb-0 mt-2 opacity-75">Browse and book our available wellbeing services</p>
    </div>

    <!-- Search Form -->
    <form class="search-form" method="get" action="">
        <input class="search-input" type="text" name="search" placeholder="Search services, providers, location..." value="<?php echo htmlspecialchars($search); ?>">
        <button class="search-btn" type="submit"><i class="fas fa-search"></i></button>
    </form>

    <div class="row g-4">
        <?php if (empty($services)): ?>
            <div class="col-12">
                <div class="no-services">
                    <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                    <h4><?php echo $strings['services'] ?? 'Services'; ?> not found.</h4>
                    <p class="text-muted mb-0">Please check back later for available services.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($services as $service): ?>
                <div class="col-md-4">
                    <div class="service-card h-100">
                        <?php if (!empty($service['image']) && file_exists('./uploads/service_images/' . $service['image'])): ?>
                            <img src="./uploads/service_images/<?php echo htmlspecialchars($service['image']); ?>" 
                                 class="service-image w-100" 
                                 alt="<?php echo htmlspecialchars($service['name_en']); ?>">
                        <?php else: ?>
                            <img src="/assets/img/default-service.png" 
                                 class="service-image w-100" 
                                 alt="Default Service Image">
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($service['name_en']); ?>
                            </h5>
                            <p class="card-text">
                                <?php echo nl2br(htmlspecialchars($service['description_en'])); ?>
                            </p>
                        </div>

                        <div class="service-info">
                            <div class="provider-badge mb-2">
                                <i class="fas fa-user-md me-2"></i>
                                <?php echo htmlspecialchars($service['provider_name']); ?>
                            </div>
                            
                            <?php if ($service['location']): ?>
                                <div class="location-badge">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?php echo htmlspecialchars($service['location']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3">
                                <a href="booking.php?service_id=<?php echo $service['id']; ?>" 
                                   class="book-btn btn w-100">
                                    <i class="fas fa-calendar-plus me-2"></i>
                                    <?php echo $strings['book_appointment'] ?? 'Book Appointment'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>