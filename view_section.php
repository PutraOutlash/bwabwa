<?php
// DELOK FORUM IKI 
try {
    include_once __DIR__ . '/db.php';
} catch (Exception $e) {
    // Tangani jika koneksi DB gagal
}

// 2. Ambil dan validasi ID Section dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: forum.php'); // Kembali ke forum jika ID tidak valid
    exit;
}
$section_id = $_GET['id'];

// 3. Ambil informasi Section dari database
$section_name = "Kategori Tidak Ditemukan";
$section_description = "";
if (isset($pdo)) {
    try {
        $stmt_section = $pdo->prepare("SELECT name, description FROM forum_sections WHERE id = ?");
        $stmt_section->execute([$section_id]);
        $section_info = $stmt_section->fetch(PDO::FETCH_ASSOC);

        if ($section_info) {
            $section_name = htmlspecialchars($section_info['name']);
            $section_description = htmlspecialchars($section_info['description']);
        } else {
            // Jika section tidak ditemukan
            header('Location: forum.php');
            exit;
        }
    } catch (Exception $e) {
        $section_name = "Error Memuat Kategori";
        $section_description = $e->getMessage();
    }
}

// 4. Definisikan variabel untuk header
$pageTitle = $section_name . " - Forum BloomBelly";
$pageCSS = ["../css/section-style.css"]; // CSS khusus untuk halaman ini

// 5. Panggil "Kepala" Halaman
include 'header.php';
?>

<main class="section-page">
    <div class="container">

        <!-- HEADER SECTION & KONTROL -->
        <div class="section-header-controls animate-on-scroll">
            <div class="section-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari di section ini...">
            </div>
            <a href="create_topic.php?section_id=<?php echo $section_id; ?>" class="btn-create-topic">
                <i class="fas fa-plus-circle"></i> Buat Topik Baru
            </a>
        </div>

        <div class="section-grid-layout">
            <!-- KOLOM KIRI: DAFTAR TOPIK -->
            <div class="section-main">
                <div class="section-title-card animate-on-scroll">
                    <h3><i class="fas fa-folder-open"></i> Topik di: <?php echo $section_name; ?></h3>
                    <?php if (!empty($section_description)): ?>
                        <p><?php echo $section_description; ?></p>
                    <?php endif; ?>
                </div>

                <ul class="topic-list">
                    <?php
                    // =================================================
                    // KODE DINAMIS UNTUK MENAMPILKAN TOPIK
                    // =================================================
                    try {
                        if (!isset($pdo)) {
                            throw new Exception("Koneksi database terputus atau file db.php bermasalah.");
                        }

                        // Query untuk mengambil topik
                        $sql_topics = "SELECT 
                                            t.id, t.title, t.created_at,
                                            u.username AS author_username,
                                            (SELECT COUNT(*) FROM forum_posts WHERE topic_id = t.id) AS post_count
                                        FROM forum_topics t
                                        JOIN users u ON t.user_id = u.id
                                        WHERE t.section_id = ?
                                        ORDER BY t.created_at DESC"; // Topik terbaru di atas

                        $stmt_topics = $pdo->prepare($sql_topics);
                        $stmt_topics->execute([$section_id]);

                        if ($stmt_topics->rowCount() > 0) {
                            while ($topic = $stmt_topics->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                                <li class="topic-item animate-on-scroll">
                                    <div class="topic-icon-wrapper">
                                        <i class="fas fa-lightbulb topic-icon"></i>
                                    </div>
                                    <div class="topic-content">
                                        <a href="view_topic.php?id=<?php echo $topic['id']; ?>" class="topic-title">
                                            <?php echo htmlspecialchars($topic['title']); ?>
                                        </a>
                                        <p class="topic-meta">
                                            Dimulai oleh <strong><?php echo htmlspecialchars($topic['author_username']); ?></strong>
                                            pada <?php echo date('d M Y, H:i', strtotime($topic['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div class="topic-stats">
                                        <span class="stat-highlight">
                                            <i class="fas fa-comment-dots"></i> <?php echo $topic['post_count']; ?> Pesan
                                        </span>
                                    </div>
                                </li>
                    <?php
                            }
                        } else {
                            echo '<li class="no-content-message animate-on-scroll">Belum ada topik di kategori ini.</li>';
                        }
                    } catch (Exception $e) {
                        // TAMPILKAN PESAN ERROR JIKA ADA MASALAH DENGAN QUERY DB
                        echo '<li class="error-message animate-on-scroll">
                                <strong>Error:</strong> ' . $e->getMessage() . '
                              </li>';
                    }
                    ?>
                </ul>
            </div>

            <!-- KOLOM KANAN: STATISTIK -->
            <aside class="section-sidebar">
                <div class="stats-card animate-on-scroll">
                    <h4><i class="fas fa-chart-line" style="color: #ff8fab; margin-right: 10px;"></i> Statistik Section</h4>
                    <?php
                    try {
                        if (isset($pdo)) {
                            $topic_count_sec = $pdo->prepare("SELECT COUNT(*) FROM forum_topics WHERE section_id = ?");
                            $topic_count_sec->execute([$section_id]);
                            $topic_c = $topic_count_sec->fetchColumn();

                            $post_count_sec = $pdo->prepare("SELECT COUNT(fp.id) FROM forum_posts fp JOIN forum_topics ft ON fp.topic_id = ft.id WHERE ft.section_id = ?");
                            $post_count_sec->execute([$section_id]);
                            $post_c = $post_count_sec->fetchColumn();
                        } else {
                            $topic_c = $post_c = '-';
                        }
                    } catch (Exception $e) {
                        $topic_c = $post_c = '?';
                    }
                    ?>
                    <div class="stat-row">
                        <span>Jumlah Topik</span>
                        <span class="stat-value"><?php echo $topic_c; ?></span>
                    </div>
                    <div class="stat-row">
                        <span>Total Postingan</span>
                        <span class="stat-value"><?php echo $post_c; ?></span>
                    </div>
                </div>

                <!-- Bisa ditambahkan widget lain di sini -->
            </aside>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>