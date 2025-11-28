<?php
// File: artikel.php

$pageTitle = "Artikel - BloomBelly";
$pageCSS = ["../css/artikel-style.css"];

try {
    include_once 'db.php';
} catch (Exception $e) {
    // Lanjut dulu
}

include 'header.php';

// --- FUNGSI FILTER BARU ---
$current_category_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$filter_condition = "";
if ($current_category_id > 0) {
    $filter_condition = " AND articles.category_id = :category_id ";
}
?>

<main class="artikel-page">
    <div class="container">

        <div class="artikel-header-section animate-on-scroll">
            <div class="header-text">
                <h1><i class="fas fa-book-reader"></i> Bacaan Bunda</h1>
                <p>Temukan tips, panduan, dan informasi terpercaya seputar kehamilan.</p>
            </div>

            <div class="header-controls">
                <div class="filter-artikel">
                    <?php
                    try {
                        // Ambil semua kategori untuk tombol filter
                        if (isset($pdo)) {
                            $stmt_cat = $pdo->query("SELECT id, name FROM article_categories ORDER BY name ASC");
                            $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

                            // Tombol "Semua"
                            $all_active = ($current_category_id == 0) ? 'active' : '';
                            echo '<a href="artikel.php" class="filter-button ' . $all_active . '">Semua</a>';

                            // Tombol Kategori Dinamis
                            foreach ($categories as $cat) {
                                $cat_active = ($cat['id'] == $current_category_id) ? 'active' : '';
                                echo '<a href="artikel.php?kategori=' . htmlspecialchars($cat['id']) . '" class="filter-button ' . $cat_active . '">' . htmlspecialchars($cat['name']) . '</a>';
                            }
                        }
                    } catch (Exception $e) {
                        // Gagal memuat kategori
                    }
                    ?>
                </div>
            </div>

        </div>

        <div class="artikel-grid-layout">

            <div class="main-articles full-width">
                <?php
                // --- KODE DINAMIS ARTIKEL UTAMA (dengan Filter) ---
                try {
                    if (isset($pdo)) {

                        // KOREKSI FINAL: Pastikan semua kolom yang diperlukan diambil secara eksplisit.
                        // Kueri ditulis dalam satu baris string untuk menghindari error concatenation.
                        $sql = "SELECT articles.*, articles.external_url, articles.featured_image_url, article_categories.name AS category_name, users.username AS author_name FROM articles JOIN article_categories ON articles.category_id = article_categories.id JOIN users ON articles.user_id = users.id_users WHERE articles.status = 'published' " . $filter_condition . " ORDER BY articles.created_at DESC";

                        $stmt = $pdo->prepare($sql);

                        // Binding parameter jika ada filter kategori
                        if ($current_category_id > 0) {
                            $stmt->bindParam(':category_id', $current_category_id, PDO::PARAM_INT);
                        }

                        $stmt->execute();

                        if ($stmt->rowCount() > 0) {
                            while ($article = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $img_src = !empty($article['featured_image_url'])
                                    ? htmlspecialchars($article['featured_image_url'])
                                    : 'https://via.placeholder.com/600x400?text=NO+IMAGE';

                                $article_link = !empty($article['external_url'])
                                    ? htmlspecialchars($article['external_url'])
                                    : '#';
                ?>
                                <article class="article-card animate-on-scroll">
                                    <div class="article-image">
                                        <a href="<?php echo $article_link; ?>" target="_blank">
                                            <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                        </a>
                                        <span class="article-category"><?php echo htmlspecialchars($article['category_name']); ?></span>
                                    </div>
                                    <div class="article-content">
                                        <h2>
                                            <a href="<?php echo $article_link; ?>" target="_blank">
                                                <?php echo htmlspecialchars($article['title']); ?>
                                            </a>
                                        </h2>
                                        <p class="article-excerpt">
                                            <?php echo htmlspecialchars($article['short_description']); ?>
                                        </p>
                                        <div class="article-meta">
                                            <span class="author">
                                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($article['author_name']); ?>
                                            </span>
                                            <span class="date">
                                                <i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($article['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </article>
                <?php
                            }
                        } else {
                            $msg = ($current_category_id > 0) ? 'Tidak ada artikel dalam kategori ini.' : 'Belum ada artikel yang dipublikasikan.';
                            echo '<div class="no-articles animate-on-scroll">' . $msg . '</div>';
                        }
                    } else {
                        echo '<div class="no-articles animate-on-scroll">Database belum terhubung. Menampilkan mode pratinjau.</div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="alert-error animate-on-scroll">Gagal memuat artikel: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>

        </div>
    </div>
</main>

<?php include 'footer.php'; ?>