<?php
// File: forum_view_posts.php
// Menampilkan semua postingan/komentar dalam satu topik untuk moderasi.

// --- PANGGIL SECURITY CHECK dan DB CONNECT (seharusnya sudah menangani session_start()) ---
include 'layout/security_check.php';

$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$topic = null;
$posts = [];
$error_message = '';
$success_message = '';

if (isset($_GET['success']) && $_GET['success'] == 'post_deleted') {
    $success_message = 'Komentar berhasil dihapus! 🗑️';
}

if ($topic_id > 0) {
    try {
        // --- 1. Ambil Detail Topik ---
        $stmt_topic = $pdo->prepare("SELECT id, title FROM forum_topics WHERE id = ?");
        $stmt_topic->execute([$topic_id]);
        $topic = $stmt_topic->fetch();

        if (!$topic) {
            $error_message = "Topik tidak ditemukan.";
        } else {
            // --- 2. Ambil Semua Postingan dalam Topik (Termasuk post awal) ---
            $stmt_posts = $pdo->prepare("
                SELECT 
                    p.id AS post_id, 
                    p.content, 
                    p.created_at, 
                    u.name AS user_name,
                    u.id_users AS user_id,
                    u.avatar_url, /* Tambahkan avatar_url jika ada */
                    u.role /* Tambahkan role untuk moderasi/identifikasi */
                FROM forum_posts p
                JOIN users u ON p.user_id = u.id_users
                WHERE p.topic_id = ?
                ORDER BY p.created_at ASC
            ");
            $stmt_posts->execute([$topic_id]);
            $posts = $stmt_posts->fetchAll();
        }
    } catch (\PDOException $e) {
        $error_message = "Gagal mengambil data: " . $e->getMessage();
    }
} else {
    $error_message = "ID Topik tidak valid.";
}

$pageTitle = $topic ? "Moderasi Post: " . htmlspecialchars($topic['title']) : "Moderasi Postingan Forum";
?>

<?php include 'layout/header_admin.php'; ?>

<style>
    /* Variabel Warna */
    :root {
        --color-primary-pink: #ff8fab;
        --color-info: #17a2b8;
        /* Teal/Cyan untuk aksi */
        --color-starter-bg: #fff0f5;
        /* Background sangat samar (pink muda) */
        --color-comment-bg: #f8f9fa;
        /* Background abu-abu terang */
        --color-text-dark: #343a40;
    }

    /* --- Styling Header Topik (Card Header) --- */
    .topic-header-card {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        padding: 1.5rem 2rem;
        border: none;
        margin-bottom: 2rem;
        /* Margin dikurangi sedikit */
    }

    .topic-header-card h4 {
        font-weight: 700;
        color: #343a40;
        /* Dibuat gelap agar kontras dengan ikon */
        margin: 0;
        display: flex;
        align-items: center;
    }

    /* Ikon Kembali (Aesthetic) */
    .icon-back-link {
        font-size: 1.5rem;
        color: var(--color-secondary);
        margin-right: 15px;
        transition: color 0.2s;
    }

    .icon-back-link:hover {
        color: var(--color-primary-pink);
    }

    /* --- END MODIFIKASI HEADER --- */


    /* --- Post Card Styling --- */
    .post-card {
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #eee;
        margin-bottom: 15px;
        /* Margin dikurangi */
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
    }

    .post-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Varian Warna Latar Belakang */
    .post-starter {
        background-color: var(--color-starter-bg);
    }

    .post-comment {
        background-color: var(--color-comment-bg);
    }

    /* Post Header (Meta Info) */
    .post-header {
        padding: 1rem 1.5rem 0.5rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Konten Post */
    .post-content {
        padding: 0.5rem 1.5rem 1.5rem 1.5rem;
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--color-text-dark);
    }

    /* Info Pengguna */
    .user-meta-info {
        display: flex;
        align-items: center;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: var(--color-secondary);
        margin-right: 10px;
        flex-shrink: 0;
    }

    .user-name {
        font-weight: 600;
        color: var(--color-text-dark);
        margin-bottom: 0;
    }

    .post-date {
        font-size: 0.8rem;
        color: var(--color-secondary);
    }

    /* Tombol Aksi */
    .btn-action {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 8px;
    }

    /* Badge Status */
    .badge-status {
        padding: 0.5em 1em;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
    }

    .badge-starter {
        background-color: var(--color-primary-pink);
        color: white;
    }

    .badge-comment {
        background-color: var(--color-info);
        color: white;
    }
</style>

<?php if ($error_message): ?>
    <div class="alert alert-danger fade show mb-4"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($topic): ?>

    <div class="topic-header-card">
        <h4 class="mb-3">
            <a href="forum.php" title="Kembali ke Daftar Topik" class="icon-back-link">
                <i class="fas fa-arrow-left"></i>
            </a>
            <span class="me-2" style="color: var(--color-primary-pink);"><i class="fas fa-comments"></i></span> Moderasi Topik: <?php echo htmlspecialchars($topic['title']); ?>
        </h4>

        <p class="text-muted mb-0"><i class="fas fa-layer-group me-1"></i> Total Postingan: <?php echo count($posts); ?></p>
    </div>


    <div class="posts-list">
        <?php
        $post_counter = 0;
        foreach ($posts as $post):
            $post_counter++;
            $is_first_post = ($post_counter == 1);
            $card_class = $is_first_post ? 'post-starter' : 'post-comment';
            $badge_text = $is_first_post ? 'TOPIC STARTER' : 'KOMENTAR';
            $badge_class = $is_first_post ? 'badge-starter' : 'badge-comment';

            // Tentukan ikon avatar placeholder
            $avatar_icon = ($post['role'] == 'admin') ? 'fas fa-user-shield' : 'fas fa-user';
        ?>
            <div class="card post-card <?php echo $card_class; ?>">

                <div class="post-header">
                    <div class="user-meta-info">
                        <div class="user-avatar" title="ID: <?php echo $post['user_id']; ?>">
                            <i class="<?php echo $avatar_icon; ?>"></i>
                        </div>

                        <div>
                            <p class="user-name mb-0"><?php echo htmlspecialchars($post['user_name']); ?></p>
                            <small class="post-date"><i class="far fa-clock me-1"></i> <?php echo date('d M Y, H:i', strtotime($post['created_at'])); ?></small>
                        </div>
                    </div>

                    <div class="flex-shrink-0 d-flex align-items-center">
                        <span class="badge badge-status <?php echo $badge_class; ?> me-3"><?php echo $badge_text; ?></span>

                        <form method="POST" action="forum_delete_post.php" style="display:inline-block;">
                            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                            <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
                            <button type="submit"
                                class="btn btn-sm btn-danger btn-action"
                                title="Hapus Postingan Ini"
                                onclick="return confirm('Yakin ingin menghapus postingan ini? Tindakan ini tidak dapat dibatalkan.');">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="post-content">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($posts)): ?>
        <div class="alert alert-info text-center py-4">
            <i class="fas fa-box-open fa-2x mb-2"></i>
            <p class="mb-0">Topik ini belum memiliki postingan/komentar.</p>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php include 'layout/footer_admin.php'; ?>