<?php
// File: artikel.php

$pageTitle = "Artikel - BloomBelly";
// Gunakan CSS baru khusus artikel
$pageCSS = ["../css/artikel-style.css"];

try {
    include_once __DIR__ . '/db.php';
} catch (Exception $e) {
    // Lanjut dulu
}

include 'header.php';
?>

<main class="artikel-page">
    <div class="container">

        <!-- HEADER SECTION ARTIKEL (BARU) -->
        <div class="artikel-header-section animate-on-scroll">
            <div class="header-text">
                <h1><i class="fas fa-book-reader"></i> Bacaan Bunda</h1>
                <p>Temukan tips, panduan, dan informasi terpercaya seputar kehamilan.</p>
            </div>
            <div class="header-controls">
                <div class="search-artikel">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari artikel...">
                </div>
                <div class="filter-artikel">
                    <select class="custom-select">
                        <option value="">Semua Kategori</option>
                        <option value="nutrisi">Nutrisi</option>
                        <option value="kesehatan">Kesehatan</option>
                        <option value="tips">Tips Harian</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="artikel-grid-layout">

            <!-- KOLOM UTAMA: DAFTAR ARTIKEL -->
            <div class="main-articles">
                <?php
                // --- KODE DINAMIS ARTIKEL UTAMA ---
                try {
                    if (isset($pdo)) {
                        $sql = "SELECT 
                                    articles.*, 
                                    article_categories.name AS category_name, 
                                    users.username AS author_name 
                                FROM articles
                                JOIN article_categories ON articles.category_id = article_categories.id
                                JOIN users ON articles.user_id = users.id
                                ORDER BY articles.created_at DESC"; // Tampilkan semua, bukan cuma LIMIT 1

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();

                        if ($stmt->rowCount() > 0) {
                            while ($article = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                                <!-- KARTU ARTIKEL BARU -->
                                <article class="article-card animate-on-scroll">
                                    <div class="article-image">
                                        <a href="artikel_detail.php?slug=<?php echo htmlspecialchars($article['slug']); ?>">
                                            <img src="<?php echo htmlspecialchars($article['featured_image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                        </a>
                                        <span class="article-category"><?php echo htmlspecialchars($article['category_name']); ?></span>
                                    </div>
                                    <div class="article-content">
                                        <h2>
                                            <a href="artikel_detail.php?slug=<?php echo htmlspecialchars($article['slug']); ?>">
                                                <?php echo htmlspecialchars($article['title']); ?>
                                            </a>
                                        </h2>
                                        <p class="article-excerpt">
                                            <?php echo substr(strip_tags(htmlspecialchars_decode($article['content'])), 0, 120); ?>...
                                        </p>
                                        <div class="article-meta">
                                            <span class="author">
                                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($article['author_name']); ?>
                                            </span>
                                            <span class="date">
                                                <i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($article['published_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </article>
                <?php
                            }
                        } else {
                            echo '<div class="no-articles animate-on-scroll">Belum ada artikel yang dipublikasikan.</div>';
                        }
                    } else {
                        // Tampilan Dummy jika DB belum siap (agar tidak kosong saat preview)
                        echo '<div class="no-articles animate-on-scroll">Database belum terhubung. Menampilkan mode pratinjau.</div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="alert-error animate-on-scroll">Gagal memuat artikel: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>

            <!-- SIDEBAR -->
            <aside class="artikel-sidebar">
                <!-- Widget: Artikel Terbaru -->
                <div class="sidebar-widget animate-on-scroll">
                    <h3><i class="fas fa-bolt" style="color: #ff8fab;"></i> Terpopuler Minggu Ini</h3>
                    <ul class="widget-list">
                        <?php
                        // Contoh Query untuk Popular (Bisa disesuaikan nanti)
                        // Sementara pakai data dummy atau query latest limit 5
                        if (isset($pdo)) {
                            $stmt_pop = $pdo->query("SELECT title, slug FROM articles ORDER BY RAND() LIMIT 3");
                            while ($pop = $stmt_pop->fetch(PDO::FETCH_ASSOC)) {
                                echo '<li><a href="artikel_detail.php?slug=' . $pop['slug'] . '">' . htmlspecialchars($pop['title']) . '</a></li>';
                            }
                        }
                        ?>
                        <!-- Dummy Content jika DB kosong -->
                        <li><a href="#">5 Makanan Super untuk Trimester Pertama</a></li>
                        <li><a href="#">Tips Mengatasi *Morning Sickness* Tanpa Obat</a></li>
                        <li><a href="#">Senam Hamil Sederhana di Rumah</a></li>
                    </ul>
                </div>

                <!-- Widget: Kategori -->
                <div class="sidebar-widget animate-on-scroll">
                    <h3><i class="fas fa-tags" style="color: #ff8fab;"></i> Topik Pilihan</h3>
                    <div class="tag-cloud">
                        <a href="#" class="tag-item">Nutrisi</a>
                        <a href="#" class="tag-item">Kesehatan Mental</a>
                        <a href="#" class="tag-item">Olahraga</a>
                        <a href="#" class="tag-item">Persiapan Lahir</a>
                        <a href="#" class="tag-item">Parenting</a>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</main>

<?php include 'footer.php'; ?>