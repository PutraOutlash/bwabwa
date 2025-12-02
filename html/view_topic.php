<?php
session_start(); // Pastikan session dimulai untuk mengambil $_SESSION['user_id']
include '../config/db_connect.php'; // Panggil koneksi DB secara manual

// 1. Ambil dan validasi ID Topik dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: forum.php');
    exit;
}
$topic_id = $_GET['id'];

// 2. Ambil informasi nama topik dari database
try {
    $stmt_topic = $pdo->prepare("SELECT title FROM forum_topics WHERE id = ?");
    $stmt_topic->execute([$topic_id]);
    $topic = $stmt_topic->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Tangani error database saat query awal
    die("Error Database Awal: " . $e->getMessage());
}


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

// =================================================================
// BLOK PESAN STATUS DARI MODIFIKASI SEBELUMNYA
// =================================================================
$message = '';
$message_type = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'edited') {
        $message = "✅ Postingan berhasil diperbarui.";
        $message_type = 'success';
    } elseif ($_GET['status'] == 'deleted') {
        $message = "🗑️ Postingan berhasil dihapus.";
        $message_type = 'success';
    }
} elseif (isset($_GET['error'])) {
    if ($_GET['error'] == 'unauthorized' || $_GET['error'] == 'unauthorized_edit') {
        $message = "❌ Gagal: Anda tidak memiliki izin untuk melakukan aksi ini.";
        $message_type = 'error';
    } elseif ($_GET['error'] == 'content_empty') {
        $message = "⚠️ Gagal: Isi postingan tidak boleh kosong.";
        $message_type = 'error';
    } elseif ($_GET['error'] == 'db_error' || $_GET['error'] == 'db_update_error') {
        $message = "❌ Terjadi kesalahan pada database saat memproses permintaan Anda.";
        $message_type = 'error';
    }
}

// Tampilkan pesan jika ada
if (!empty($message)) {
    echo '<div class="container">';
    // Tambahkan ID 'auto-dismiss-alert' pada div notifikasi
    echo '<div id="auto-dismiss-alert" class="alert alert-' . $message_type . '" style="
        padding: 15px; margin: 15px 0; border-radius: 8px; 
        color: #fff; font-weight: bold; background-color: ' .
        ($message_type == 'success' ? '#28a745' : ($message_type == 'error' ? '#dc3545' : '#17a2b8')) . ';">';
    echo $message;
    echo '</div></div>';
}
// =================================================================
?>

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
                // *** PENTING: Mengganti u.id menjadi u.id_users di semua klausa JOIN dan SELECT ***
                $sql = "SELECT 
                            p.id AS post_id, p.content, p.created_at,
                            u.id_users AS user_id, u.username, u.avatar_url,
                            (SELECT COUNT(*) FROM forum_posts WHERE user_id = u.id_users) AS user_post_count
                        FROM forum_posts p
                        JOIN users u ON p.user_id = u.id_users
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
                        <div class="post-card" id="post-<?php echo $post['post_id']; ?>">
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
                                    <span class="post-timestamp"><i class="far fa-clock"></i> <?php echo date('d M Y, H:i', strtotime($post['created_at'])); ?></span>

                                    <?php
                                    // *** LOGIC TOMBOL EDIT DAN HAPUS ***
                                    // Cek apakah user sudah login DAN user_id post sama dengan user_id session
                                    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']):
                                    ?>
                                        <span class="post-actions">
                                            <button
                                                onclick="openEditModal(<?php echo $post['post_id']; ?>, '<?php echo htmlspecialchars(addslashes($post['content'])); ?>')"
                                                class="btn-action btn-edit"
                                                title="Edit Postingan">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <a
                                                href="delete_post.php?id=<?php echo $post['post_id']; ?>&topic=<?php echo $topic_id; ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')"
                                                class="btn-action btn-delete"
                                                title="Hapus Postingan">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </span>
                                    <?php endif; ?>
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
                // Sekarang menampilkan error SQL yang lebih jelas
                echo "<p class='error'>Error memuat topik: " . htmlspecialchars($e->getMessage()) . "</p>";
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
                    <button type="submit" class="btn-primary">
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

<div id="editModal" class="modal-overlay">
    <div class="modal-content-box">
        <span onclick="closeEditModal()" class="modal-close-btn">&times;</span>
        <h3><i class="fas fa-edit"></i> Edit Postingan Anda</h3>
        <form action="edit_post.php" method="POST">
            <input type="hidden" name="post_id" id="edit-post-id">
            <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
            <textarea name="content" id="edit-content" required></textarea>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
    const editModal = document.getElementById('editModal');
    const editContent = document.getElementById('edit-content');

    function openEditModal(postId, currentContent) {
        // Isi field input di modal
        document.getElementById('edit-post-id').value = postId;

        // Membersihkan HTML entities untuk display di textarea
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = currentContent;
        editContent.value = tempDiv.innerText;

        // Tampilkan modal dengan menambahkan class 'active'
        editModal.classList.add('active');
    }

    function closeEditModal() {
        // Sembunyikan modal dengan menghapus class 'active'
        editModal.classList.remove('active');
    }

    // Tutup modal jika user klik tombol close (X) atau di luar kotak modal
    window.onclick = function(event) {
        if (event.target == editModal) {
            closeEditModal();
        }
    }

    // *** SCRIPT AUTODISMISS ALERT BARU ***
    window.onload = function() {
        const alertElement = document.getElementById('auto-dismiss-alert');
        if (alertElement) {
            // Setelah 5000 milidetik (5 detik), sembunyikan notifikasi
            setTimeout(function() {
                // Tambahkan transisi fade out
                alertElement.style.transition = 'opacity 1s ease';
                alertElement.style.opacity = '0';

                // Hapus elemen dari DOM setelah fade out selesai (1 detik)
                setTimeout(function() {
                    alertElement.style.display = 'none';
                    alertElement.remove();
                }, 1000);

            }, 5000); // 5 detik
        }
    };
</script>

<?php
// 5. Panggil "Kaki" Halaman
include 'footer.php';
?>