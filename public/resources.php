<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
check_login();

$strings = $strings ?? include __DIR__ . '/../includes/language/lang.en.php';

$pdo = get_db();
$stmt = $pdo->query("SELECT * FROM resources ORDER BY created_at DESC");
$resources = $stmt->fetchAll();
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
        padding: 0.8rem 1.5rem;
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

    .resources-table {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background: #2c5233;
        color: white;
        font-weight: 600;
        border: none;
        padding: 1rem;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .table tr:hover {
        background: rgba(44, 82, 51, 0.05);
    }

    .resource-type-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-video {
        background: #ffefef;
        color: #dc3545;
    }

    .badge-document {
        background: #e7f2ff;
        color: #0d6efd;
    }

    .badge-article {
        background: #f8f9fa;
        color: #6c757d;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .iframe-preview {
        width: 200px;
        height: 120px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container py-4">

    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-0">
            <i class="fas fa-book-reader me-2"></i>
            <?php echo $strings['resources'] ?? 'Resources'; ?>
        </h2>
        <p class="mb-0 mt-2 opacity-75">Explore our collection of helpful materials</p>
    </div>

    <?php if (empty($resources)): ?>
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            No resources found.
        </div>
    <?php else: ?>
        <div class="resources-table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Resource</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resources as $res): ?>
                        <tr>
                            <td style="min-width: 200px;">
                                <?php if ($res['type'] === 'video'): ?>
                                    <div class="iframe-preview mb-2">
                                        <iframe src="<?php echo htmlspecialchars($res['url']); ?>" 
                                                width="100%" 
                                                height="100%" 
                                                frameborder="0" 
                                                allowfullscreen>
                                        </iframe>
                                    </div>
                                <?php endif; ?>
                                <h6 class="mb-0">
                                    <?php if ($res['type'] === 'video'): ?>
                                        <i class="fas fa-video me-2 text-danger"></i>
                                    <?php elseif ($res['type'] === 'document'): ?>
                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                    <?php else: ?>
                                        <i class="fas fa-newspaper me-2 text-secondary"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($res['title_en']); ?>
                                </h6>
                            </td>
                            <td>
                                <span class="resource-type-badge badge-<?php echo $res['type']; ?>">
                                    <?php echo ucfirst($res['type']); ?>
                                </span>
                            </td>
                            <td style="max-width: 300px;">
                                <p class="mb-0 text-muted">
                                    <?php echo nl2br(htmlspecialchars($res['description_en'])); ?>
                                </p>
                            </td>
                            <td>
                                <span class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <?php echo htmlspecialchars(date('Y-m-d', strtotime($res['created_at']))); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($res['type'] === 'document'): ?>
                                    <a href="<?php echo htmlspecialchars($res['url']); ?>" 
                                       class="btn btn-action btn-outline-primary" 
                                       target="_blank">
                                        <i class="fas fa-download me-2"></i>
                                        Download
                                    </a>
                                <?php elseif (!empty($res['url']) && $res['type'] !== 'video'): ?>
                                    <a href="<?php echo htmlspecialchars($res['url']); ?>" 
                                       class="btn btn-action btn-outline-secondary" 
                                       target="_blank">
                                        <i class="fas fa-external-link-alt me-2"></i>
                                        View
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>