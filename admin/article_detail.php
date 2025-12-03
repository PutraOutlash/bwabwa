<?php
// File: article_detail.php
// Menampilkan detail artikel dengan statistik likes, saves, views

include 'layout/security_check.php';

$pageTitle = "Article Detail";

// Ambil ID dari URL
$article_id = $_GET['id'] ?? 0;

if (!$article_id) {
    header('Location: articles.php');
    exit();
}

try {
    // --- Query untuk data artikel ---
    // PERBAIKAN: Gunakan id_articles (bukan id) dan created_at__ (bukan created_at)
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            cat.name AS category_name
        FROM articles a
        LEFT JOIN article_categories cat ON a.category_id = cat.id
        WHERE a.id_articles = ?
    ");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch();
    
    if (!$article) {
        header('Location: articles.php');
        exit();
    }
    
    // --- Query untuk statistik ---
    // Jumlah likes - PERBAIKAN: pastikan tabel article_likes ada dan punya kolom article_id
    $likes_stmt = $pdo->prepare("SELECT COUNT(*) FROM article_likes WHERE article_id = ?");
    $likes_stmt->execute([$article_id]);
    $like_count = $likes_stmt->fetchColumn() ?? 0;
    
    // Jumlah saves (bookmarks) - PERBAIKAN: tabel article_simpan ada
    $saves_stmt = $pdo->prepare("SELECT COUNT(*) FROM article_simpan WHERE article_id = ?");
    $saves_stmt->execute([$article_id]);
    $save_count = $saves_stmt->fetchColumn() ?? 0;
    
    // Jika ada kolom views di tabel articles - PERBAIKAN: tambah pengecekan
    $view_count = 0;
    if (isset($article['views'])) {
        $view_count = $article['views'];
    }
    
    // Format tanggal - PERBAIKAN: gunakan created_at__ (dengan 2 underscore)
    $created_at = $article['created_at'] ?? $article['created_at'] ?? date('Y-m-d H:i:s');
    $created_date = date('d F Y', strtotime($created_at));
    $created_time = date('H:i', strtotime($created_at));
    
} catch (PDOException $e) {
    $error_message = "Error mengambil data: " . $e->getMessage();
}

include 'layout/header_admin.php';
?>

<style>
    .article-detail-card {
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: none;
        margin-bottom: 25px;
    }
    
    .stat-card {
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.3s;
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 15px;
        color: white;
    }
    
    .stat-icon.likes { background: linear-gradient(135deg, #ff4757, #ff3838); }
    .stat-icon.saves { background: linear-gradient(135deg, #3742fa, #5352ed); }
    .stat-icon.views { background: linear-gradient(135deg, #2ed573, #1dd1a1); }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .article-content {
        font-size: 1.05rem;
        line-height: 1.7;
        color: #333;
    }
    
    .badge-category {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .badge-published {
        background: linear-gradient(135deg, #27ae60, #219a52);
        color: white;
    }
    
    .badge-draft {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
    }
    
    .author-info {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
    }
    
    .author-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #3498db;
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header dengan Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="articles.php">Articles</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Article Detail</li>
                </ol>
            </nav>
            
            <!-- Header dengan Judul dan Tombol -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 fw-bold mb-1">
                        <i class="fas fa-newspaper me-2 text-primary"></i>
                        Article Detail
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-eye me-1"></i>
                        ID: #<?php echo $article['id_articles'] ?? $article_id; ?> | 
                        Created: <?php echo $created_date; ?> at <?php echo $created_time; ?>
                    </p>
                </div>
                <div>
                    <a href="articles.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                    <a href="article_edit.php?id=<?php echo $article_id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i> Edit Article
                    </a>
                </div>
            </div>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <!-- Row 1: Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon likes">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-number"><?php echo $like_count; ?></div>
                        <div class="stat-label">Total Likes</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon saves">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <div class="stat-number"><?php echo $save_count; ?></div>
                        <div class="stat-label">Total Saves</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon views">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stat-number"><?php echo $view_count; ?></div>
                        <div class="stat-label">Total Views</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Left Column: Article Content -->
                <div class="col-lg-12">
                    <!-- Article Card -->
                    <div class="card article-detail-card">
                        <div class="card-body">
                            <!-- Article Header -->
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <?php if (isset($article['category_name'])): ?>
                                            <span class="badge-category">
                                                <?php echo htmlspecialchars($article['category_name']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (isset($article['status'])): ?>
                                            <span class="badge-status <?php echo $article['status'] == 'published' ? 'badge-published' : 'badge-draft'; ?>">
                                                <?php echo ucfirst($article['status']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <h1 class="h3 fw-bold mb-3"><?php echo isset($article['title']) ? htmlspecialchars($article['title']) : 'No Title'; ?></h1>
                                </div>
                            </div>
                            
                            <!-- Short Description -->
                            <?php if (isset($article['short_description']) && !empty($article['short_description'])): ?>
                            <div class="mb-4">
                                <p class="lead text-muted"><?php echo htmlspecialchars($article['short_description']); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Featured Image -->
                            <?php if (isset($article['featured_image_url']) && !empty($article['featured_image_url'])): ?>
                            <div class="mb-4">
                                <img src="<?php echo htmlspecialchars($article['featured_image_url']); ?>" 
                                     alt="<?php echo isset($article['title']) ? htmlspecialchars($article['title']) : 'Featured Image'; ?>"
                                     class="img-fluid rounded">
                            </div>
                            <?php endif; ?>
                            
                            <!-- Author Info -->
                            <?php if (isset($article['author']) && !empty($article['author'])): ?>
                            <div class="author-info mb-4">
                                <div class="author-avatar d-flex align-items-center justify-content-center bg-primary text-white fs-3">
                                    <?php echo strtoupper(substr($article['author'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($article['author']); ?></h6>
                                    <p class="mb-0 text-muted">Author</p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- External URL -->
                            <?php if (isset($article['external_url']) && !empty($article['external_url'])): ?>
                            <div class="mb-4">
                                <a href="<?php echo htmlspecialchars($article['external_url']); ?>" 
                                   target="_blank" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-2"></i>Baca artikel lengkap
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Article Metadata -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title text-muted mb-3">Article Information</h6>
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="40%"><strong>ID Artikel:</strong></td>
                                                    <td><?php echo $article['id_articles'] ?? $article_id; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>User ID:</strong></td>
                                                    <td><?php echo $article['user_id'] ?? '-'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Category ID:</strong></td>
                                                    <td><?php echo $article['category_id'] ?? '-'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Created:</strong></td>
                                                    <td><?php echo $created_date; ?> at <?php echo $created_time; ?></td>
                                                </tr>
                                                <?php if (isset($article['published_at']) && !empty($article['published_at'])): ?>
                                                <tr>
                                                    <td><strong>Published:</strong></td>
                                                    <td><?php echo date('d F Y H:i', strtotime($article['published_at'])); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    <small>
                                        Artikel ID: <?php echo $article['id_articles'] ?? $article_id; ?>
                                        | Status: <?php echo isset($article['status']) ? ucfirst($article['status']) : 'Unknown'; ?>
                                    </small>
                                </div>
                                <div>
                                    <form method="POST" action="article_delete.php" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                        <input type="hidden" name="id" value="<?php echo $article_id; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash-alt me-2"></i>Delete Article
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer_admin.php'; ?>