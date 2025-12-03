<?php
// File: articles.php
// Menampilkan daftar artikel menggunakan layout global.

include 'layout/security_check.php';

$pageTitle = "Articles Management";

// Variabel untuk pesan sukses (jika redirect dari halaman create/edit)
$success_message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'created') {
        $success_message = 'Artikel baru berhasil ditambahkan! 🚀';
    } elseif ($_GET['success'] == 'updated') {
        $success_message = 'Artikel berhasil diperbarui! ✨';
    } elseif ($_GET['success'] == 'deleted') {
        $success_message = 'Artikel berhasil dihapus. 🗑️';
    }
}

// --- 3. Query Data Statistik Articles ---
// Catatan: Asumsi $pdo sudah tersedia dari security_check.php
$published_count = number_format($pdo->query("SELECT COUNT(id_articles) AS total FROM articles WHERE status = 'published'")->fetchColumn());
$draft_count = number_format($pdo->query("SELECT COUNT(id_articles) AS total FROM articles WHERE status = 'draft'")->fetchColumn());

// --- Placeholder/Simulasi untuk growth rate ---
$published_growth = '+5 this month';
$draft_review = '2 needs review';
$deleted_count = 3;
$deleted_growth = '-1 this week';


// --- 4. Query untuk Tabel Daftar Artikel ---
$stmt = $pdo->query("
    SELECT 
        a.id_articles, 
        a.title, 
        a.short_description,
        a.author, 
        a.status, 
        a.created_at, 
        u.name AS user_name 
    FROM articles a
    LEFT JOIN users u ON a.user_id = u.id_users
    ORDER BY a.created_at DESC
");
$articles = $stmt->fetchAll();
?>

<?php include 'layout/header_admin.php'; ?>

<style>
    /* Variabel Warna */
    :root {
        --color-published: #28a745;
        --color-draft: #ffc107;
        --color-deleted: #dc3545;
        --color-muted: #6c757d;
<<<<<<< HEAD
        --color-view: #17a2b8; /* Warna untuk tombol view */
=======
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
        --table-header-bg: #f8f9fa;

        /* Warna Latar Belakang Samar untuk Kartu Statistik */
        --bg-published-samar: #e8f5e9;
        --bg-draft-samar: #fff9e6;
        --bg-deleted-samar: #fce8e8;

        /* Warna Ikon Kartu Statistik */
        --bg-icon-published: #d4edda;
        --bg-icon-draft: #ffeeba;
        --bg-icon-deleted: #f5c6cb;

        /* Warna untuk badge status */
        --badge-published-bg: var(--color-published);
        --badge-draft-bg: var(--color-draft);
    }

    /* --- 1. Styling Card Statistik (Diambil dari desain dashboard) --- */
    .article-card {
        border-radius: 15px;
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
        border: none !important;
        position: relative;
        height: 100%;
        /* Memastikan tinggi sama */
    }

    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15) !important;
    }

    .display-5 {
        font-size: 2.8rem;
        font-weight: 800;
        color: #343a40;
    }

    .card-icon-container {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        float: right;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: absolute;
        top: 15px;
        right: 15px;
    }


    /* --- 2. Styling Tabel (Dibuat lebih terpadu) --- */
    .table-container {
        background: #ffffff;
        border-radius: 15px;
        /* Sama dengan kartu statistik */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    /* Header Card Tabel */
    .table-container .card-header-custom {
        padding: 1.5rem 1.5rem 1rem 1.5rem;
        /* Padding lebih besar */
        background-color: #ffffff;
        border-bottom: none;
        /* Menghapus garis bawah */
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Header Kolom Tabel */
    .table-articles th {
        background-color: #ffffff;
        /* Dibuat putih bersih */
        color: var(--color-muted);
        font-weight: 600;
        /* Sedikit dikurangi */
        text-transform: uppercase;
        font-size: 0.8rem;
        border-bottom: 1px solid #e9ecef;
        /* Hanya garis tipis */
        border-top: none !important;
        padding: 0.5rem 1.5rem;
        /* Padding atas bawah dikurangi agar header lebih kompak */
        letter-spacing: 0.5px;
    }

    /* Sel Tabel */
    .table-articles td {
        vertical-align: middle;
        border-top: 1px solid #f1f1f1;
        /* Garis pemisah yang sangat halus */
        padding: 1.25rem 1.5rem;
        /* Padding baris diperbesar */
    }

    /* Hover Effect */
    .table-articles tbody tr:hover {
        background-color: #fcfcfc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        /* Shadow halus saat hover */
    }

    /* Tombol Aksi */
    .btn-action {
        width: 38px;
        /* Dibuat sedikit lebih besar */
        height: 38px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
<<<<<<< HEAD
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Tombol View */
    .btn-view {
        background-color: var(--color-view);
        border-color: var(--color-view);
        color: white;
    }
    
    .btn-view:hover {
        background-color: #138496;
        border-color: #117a8b;
        color: white;
    }

    /* Tombol Edit */
    .btn-edit {
        background-color: var(--color-draft);
        border-color: var(--color-draft);
        color: white;
    }
    
    .btn-edit:hover {
        background-color: #e0a800;
        border-color: #d39e00;
        color: white;
    }

    /* Tombol Delete */
    .btn-delete {
        background-color: var(--color-deleted);
        border-color: var(--color-deleted);
        color: white;
    }
    
    .btn-delete:hover {
        background-color: #c82333;
        border-color: #bd2130;
        color: white;
=======
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
    }

    /* Style untuk Badge Status */
    .badge-published {
        background-color: var(--badge-published-bg);
        color: white;
    }

    .badge-draft {
        background-color: var(--badge-draft-bg);
        color: #333;
    }

    /* Menghapus garis pada tabel terakhir */
    .table-articles tbody tr:last-child td {
        border-bottom: none;
    }
<<<<<<< HEAD
    
    /* Article Title Detail */
    .article-title-detail {
        cursor: pointer;
    }
    
    .article-title-detail strong {
        display: block;
        margin-bottom: 5px;
        font-size: 1rem;
    }
    
    .article-title-detail small {
        font-size: 0.85rem;
        line-height: 1.4;
        display: block;
        color: #6c757d;
    }
=======
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
</style>

<div class="row mb-4 g-4">
    <div class="col-lg-4 col-md-6">
        <div class="card article-card" style="background-color: var(--bg-published-samar);">
            <div class="card-body">
                <div class="card-icon-container" style="background-color: var(--bg-icon-published); color: var(--color-published);">
                    <i class="fas fa-check-double"></i>
                </div>

                <h6 class="card-title text-uppercase text-muted mb-2 fw-semibold">Published Articles</h6>
                <h1 class="display-5 mb-0 fw-bold"><?php echo $published_count; ?></h1>
                <p class="mt-3 mb-0 text-sm">
                    <span class="text-success me-2 fw-semibold"><i class="fas fa-arrow-up"></i> <?php echo $published_growth; ?></span>
                    <span class="text-muted text-nowrap">Growth</span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card article-card" style="background-color: var(--bg-draft-samar);">
            <div class="card-body">
                <div class="card-icon-container" style="background-color: var(--bg-icon-draft); color: var(--color-draft);">
                    <i class="fas fa-edit"></i>
                </div>

                <h6 class="card-title text-uppercase text-muted mb-2 fw-semibold">Draft Articles</h6>
                <h1 class="display-5 mb-0 fw-bold"><?php echo $draft_count; ?></h1>
                <p class="mt-3 mb-0 text-sm">
                    <span class="text-warning me-2 fw-semibold"><i class="fas fa-eye"></i> <?php echo $draft_review; ?></span>
                    <span class="text-muted text-nowrap">Pending Review</span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-12">
        <div class="card article-card" style="background-color: var(--bg-deleted-samar);">
            <div class="card-body">
                <div class="card-icon-container" style="background-color: var(--bg-icon-deleted); color: var(--color-deleted);">
                    <i class="fas fa-trash-alt"></i>
                </div>

                <h6 class="card-title text-uppercase text-muted mb-2 fw-semibold">Deleted Articles</h6>
                <h1 class="display-5 mb-0 fw-bold"><?php echo $deleted_count; ?></h1>
                <p class="mt-3 mb-0 text-sm">
                    <span class="text-danger me-2 fw-semibold"><i class="fas fa-arrow-down"></i> <?php echo $deleted_growth; ?></span>
                    <span class="text-muted text-nowrap">Restoration Rate</span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="card-header-custom">
        <h4 class="mb-0 fw-bold">Daftar Artikel</h4>
        <a href="article_create.php" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus me-2"></i> Add New Article
        </a>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-articles align-middle mb-0">
            <thead>
                <tr>
<<<<<<< HEAD
                    <th scope="col" style="width: 35%;">Article Detail</th>
                    <th scope="col" style="width: 15%;">Author</th>
                    <th scope="col" style="width: 12%;">Date Created</th>
                    <th scope="col" style="width: 10%;">Status</th>
                    <th scope="col" style="width: 20%;">Actions</th>
=======
                    <th scope="col" style="width: 40%;">Article Detail</th>
                    <th scope="col" style="width: 15%;">Author</th>
                    <th scope="col" style="width: 15%;">Date Created</th>
                    <th scope="col" style="width: 15%;">Status</th>
                    <th scope="col" style="width: 15%;">Actions</th>
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
<<<<<<< HEAD
                    <tr>
                        <td class="article-title-detail" onclick="window.location='article_detail.php?id=<?php echo $article['id_articles']; ?>'">
=======
                    <tr onclick="window.location='article_edit.php?id=<?php echo $article['id_articles']; ?>'">
                        <td class="article-title-detail">
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
                            <strong class="text-dark"><?php echo htmlspecialchars($article['title']); ?></strong>
                            <small class="text-muted"><?php echo htmlspecialchars($article['short_description']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($article['author'] ?: $article['user_name'] ?: 'N/A'); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($article['created_at'])); ?></td>
                        <td>
                            <?php
                            $statusClass = '';
                            if ($article['status'] == 'published') {
                                $statusClass = 'badge-published';
                            } elseif ($article['status'] == 'draft') {
                                $statusClass = 'badge-draft';
                            } else {
                                $statusClass = 'bg-secondary';
                            }
                            ?>
                            <span class="badge badge-status <?php echo $statusClass; ?>">
                                <?php echo ucfirst($article['status']); ?>
                            </span>
                        </td>
                        <td>
<<<<<<< HEAD
                            <div class="d-flex gap-2">
                                <!-- Tombol View/Detail -->
                                <a href="article_detail.php?id=<?php echo $article['id_articles']; ?>" 
                                   class="btn btn-action btn-view" 
                                   title="View Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Tombol Edit -->
                                <a href="article_edit.php?id=<?php echo $article['id_articles']; ?>" 
                                   class="btn btn-action btn-edit" 
                                   title="Edit Article">
                                    <i class="fas fa-pen"></i>
                                </a>
                                
                                <!-- Tombol Delete -->
                                <form method="POST" action="article_delete.php" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $article['id_articles']; ?>">
                                    <button type="submit" 
                                            class="btn btn-action btn-delete" 
                                            title="Delete Article" 
                                            onclick="return confirm('Yakin ingin menghapus artikel: <?php echo htmlspecialchars($article['title']); ?>?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
=======
                            <a href="article_edit.php?id=<?php echo $article['id_articles']; ?>" class="btn btn-sm btn-info text-white btn-action me-2" title="Edit Article" onclick="event.stopPropagation();">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="article_delete.php" style="display:inline-block;" onclick="event.stopPropagation();">
                                <input type="hidden" name="id" value="<?php echo $article['id_articles']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger btn-action" title="Delete Article" onclick="return confirm('Yakin ingin menghapus artikel: <?php echo htmlspecialchars($article['title']); ?>?');">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($articles)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-light mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada artikel yang tersedia.</p>
                            <a href="article_create.php" class="btn btn-outline-primary btn-sm mt-2"><i class="fas fa-plus me-1"></i> Buat Artikel Pertama Anda</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout/footer_admin.php'; ?>