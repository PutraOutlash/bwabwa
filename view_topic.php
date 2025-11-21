<?php
// IKI PISAN
include 'db.php'; // Panggil koneksi DB secara manual

// 1. Ambil dan validasi ID Topik dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: forum.php');
    exit;
}
$topic_id = $_GET['id'];

// 2. Ambil informasi nama topik dari database
$stmt_topic = $pdo->prepare("SELECT title FROM forum_topics WHERE id = ?");
$stmt_topic->execute([$topic_id]);
$topic = $stmt_topic->fetch(PDO::FETCH_ASSOC);

if (!$topic) {
    // Jika ID topik tidak ditemukan, kirim ke forum utama
    header('Location: forum.php');
    exit;
}

// 3. SEKARANG kita definisikan variabel untuk header
$pageTitle = htmlspecialchars($topic['title']);
// Kita tambahkan CSS baru untuk topik
$pageCSS = ["../css/forum-style.css", "../css/topic-style.css"]; // Pastikan file ini dipanggil

// 4. Panggil "Kepala" Halaman
include 'header.php';
?>

<style>
    /* Hapus properti warna/shadow yang bertentangan dengan Dark Mode dari inline style */

    .post-card {
        display: flex;
        /* background, box-shadow DIHAPUS */
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .post-user-info {
        width: 180px;
        /* background, border-right DIHAPUS */
        padding: 20px;
        text-align: center;
        flex-shrink: 0;
    }

    /* GAYA AVATAR YANG PENTING - TETAPKAN STYLE BERIKUT */
    .post-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ff8fab;
        margin-bottom: 10px;
        background-color: #eee;
    }

    .post-content {
        flex-grow: 1;
        padding: 20px 25px;
    }

    .post-meta {
        color: #999;
        font-size: 0.9em;
        margin-bottom: 15px;
        /* border-bottom DIHAPUS */
        padding-bottom: 10px;
    }

    .post-body {
        line-height: 1.6;
        /* color DIHAPUS */
    }
</style>

<main class="forum-page">
    <div class="container">

        <div class="topic-header">
            <h1><?php echo htmlspecialchars($topic['title']); ?></h1>
            <a href="#reply-form" class="btn-primary" style="text-decoration: none; padding: 8px 20px; border-radius: 20px;">
                <i class="fas fa-reply"></i> Balas Topik
            </a>
        </div>

        <hr class="topic-divider" style="margin: 20px 0;">

        <div class="topic-post-list">
            <?php
            // ==================================================================
            // AWAL KODE DINAMIS UNTUK MENAMPILKAN SEMUA POSTINGAN
            // ==================================================================
            try {
                $sql = "SELECT 
                            p.content, p.created_at,
                            u.id AS user_id, u.username, u.avatar_url,
                            (SELECT COUNT(*) FROM forum_posts WHERE user_id = u.id) AS user_post_count
                        FROM forum_posts p
                        JOIN users u ON p.user_id = u.id
                        WHERE p.topic_id = ?
                        ORDER BY p.created_at ASC";

                $stmt_posts = $pdo->prepare($sql);
                $stmt_posts->execute([$topic_id]);

                if ($stmt_posts->rowCount() > 0) {
                    while ($post = $stmt_posts->fetch(PDO::FETCH_ASSOC)) {
                        $avatar_src = !empty($post['avatar_url']) && file_exists($post['avatar_url'])
                            ? $post['avatar_url']
                            : 'https://via.placeholder.com/80?text=User';
            ?>
                        <div class="post-card">
                            <div class="post-user-info">
                                <img src="<?php echo htmlspecialchars($avatar_src); ?>" alt="<?php echo htmlspecialchars($post['username']); ?>" class="post-avatar">

                                <h4><?php echo htmlspecialchars($post['username']); ?></h4>
                                <span class="user-title">Member</span>
                                <span class="user-stats">
                                    Posts: <?php echo $post['user_post_count']; ?>
                                </span>
                            </div>
                            <div class="post-content">
                                <div class="post-meta">
                                    <i class="far fa-clock"></i> <?php echo date('d M Y, H:i', strtotime($post['created_at'])); ?>
                                </div>
                                <div class="post-body">
                                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                                </div>
                            </div>
                        </div>
            <?php
                    }
                } else {
                    echo "<p>Topik ini belum memiliki balasan.</p>";
                }
            } catch (Exception $e) {
                echo "<p class='error'>Error memuat topik: " . $e->getMessage() . "</p>";
            }
            // ==================================================================
            // AKHIR KODE DINAMIS
            // ==================================================================
            ?>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div id="reply-form" class="reply-box" style="margin-top: 40px;">
                <h3><i class="fas fa-pen"></i> Tulis Balasan</h3>
                <form action="post_reply.php" method="POST">
                    <textarea name="content" rows="6" placeholder="Tulis balasan Anda di sini..." required></textarea>
                    <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
                    <button type="submit" class="btn-primary" style="border: none; padding: 10px 25px; font-size: 16px; cursor: pointer;">
                        Kirim Balasan
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-info" style="margin-top: 40px; padding: 20px; background: #e3f2fd; color: #0c5460; border-radius: 8px;">
                Silakan <a href="login.php" style="font-weight: bold;">Login</a> untuk membalas topik ini.
            </div>
        <?php endif; ?>

    </div>
</main>

<?php
// 5. Panggil "Kaki" Halaman
include 'footer.php';
?>