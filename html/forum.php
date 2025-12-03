<?php
// File: forum.php
// IKI SENG RUWET DEWEEE 

// 1. Definisikan variabel khusus
$pageTitle = "Forum - BloomBelly";
// Anti-cache CSS agar perubahan tampilan langsung terlihat
$pageCSS = ["../css/forum-style.css?v=" . time()];

// 2. KONEKSI DATABASE
try {
    // Menggunakan include_once './db.php'; karena file db.php ada di folder yang sama
    include_once '../config/db_connect.php';
    $pdo_available = true;
} catch (Exception $e) {
    // Jika koneksi gagal, set $pdo_available menjadi false
    $pdo_available = false;
}

// 3. Panggil Header
// Asumsi 'header.php' ada di folder yang sama
include 'header.php';

// 4. Hitung Postingan User & Statistik (LOGIKA BARU - Menggunakan kolom yang benar)
$user_post_count = 0;
$thread_count = $post_count = $member_count = 0; // Default untuk statistik
$forum_error = null;

if (isset($pdo) && $pdo_available) {
    try {
        // HITUNG POSTINGAN USER (Koreksi: Menggunakan kolom user_id)
        if (isset($_SESSION['user_id'])) {
            $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM forum_posts WHERE user_id = :user_id");
            // Catatan: $_SESSION['user_id'] berisi nilai dari kolom id_users
            $stmt_count->execute([':user_id' => $_SESSION['user_id']]);
            $user_post_count = $stmt_count->fetchColumn();
        }

        // HITUNG STATISTIK FORUM (Koreksi: users harus menggunakan COUNT(id_users))
        $thread_count = $pdo->query("SELECT COUNT(*) FROM forum_topics")->fetchColumn();
        $post_count = $pdo->query("SELECT COUNT(*) FROM forum_posts")->fetchColumn();

        // KOREKSI UTAMA: Menghitung anggota harus menggunakan kolom ID yang benar
        $member_count = $pdo->query("SELECT COUNT(id_users) FROM users")->fetchColumn();
    } catch (Exception $e) {
        $forum_error = "Gagal memuat statistik: " . $e->getMessage();
        $user_post_count = $thread_count = $post_count = $member_count = '?';
    }
}


// 5. Ambil Parameter Search & Sort
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_order = $_GET['sort'] ?? 'latest';

// Mapping teks untuk tombol dropdown
$sort_options_display = [
    'latest' => 'Terbaru',
    'popular' => 'Terpopuler',
    'name' => 'A-Z'
];
if (!array_key_exists($sort_order, $sort_options_display)) {
    $sort_order = 'latest';
}
?>

<main class="forum-page">
    <div class="container">

        <div class="forum-header-section animate-on-scroll">
            <form action="forum.php" method="GET" class="search-forum">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Cari topik diskusi..." value="<?php echo htmlspecialchars($search_query); ?>">
                <?php if ($sort_order != 'latest'): ?>
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_order); ?>">
                <?php endif; ?>
            </form>

            <div class="forum-controls-right">
                <form action="forum.php" method="GET" id="sortForm">
                    <?php if (!empty($search_query)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                    <?php endif; ?>

                    <div class="custom-dropdown-container">
                        <div class="dropdown-selected-value" tabindex="0">
                            <span><?php echo $sort_options_display[$sort_order]; ?></span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </div>
                        <ul class="dropdown-options">
                            <li data-value="latest" class="<?php echo ($sort_order == 'latest' ? 'selected' : ''); ?>">Terbaru</li>
                            <li data-value="popular" class="<?php echo ($sort_order == 'popular' ? 'selected' : ''); ?>">Terpopuler</li>
                            <li data-value="name" class="<?php echo ($sort_order == 'name' ? 'selected' : ''); ?>">A-Z</li>
                        </ul>
                        <select name="sort" id="realSortSelect" class="filter-select hidden">
                            <option value="latest" <?php if ($sort_order == 'latest') echo 'selected'; ?>>Terbaru</option>
                            <option value="popular" <?php if ($sort_order == 'popular') echo 'selected'; ?>>Terpopuler</option>
                            <option value="name" <?php if ($sort_order == 'name') echo 'selected'; ?>>A-Z</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="forum-grid-layout">

            <div class="forum-main">
                <?php
                try {
                    if (!$pdo_available) {
                        throw new Exception("Koneksi database terputus.");
                    }
                    if ($forum_error) {
                        echo '<div class="alert-box error" style="margin-bottom: 20px;">' . htmlspecialchars($forum_error) . '</div>';
                    }

                    echo '<div class="forum-category-card animate-on-scroll">';
                    echo '<div class="category-title-bar">';
                    echo '<h3><i class="fas fa-comments"></i> Diskusi Umum</h3>';
                    echo '</div>';
                    echo '<ul class="thread-list">';

                    // Logic Sorting SQL
                    switch ($sort_order) {
                        case 'popular':
                            $sql_order_by = "ORDER BY topic_count DESC";
                            break;
                        case 'name':
                            $sql_order_by = "ORDER BY s.name ASC";
                            break;
                        case 'latest':
                        default:
                            $sql_order_by = "ORDER BY last_post_time DESC";
                            break;
                    }

                    // Logic Searching SQL
                    $sql_where = "";
                    $params = [];
                    if (!empty($search_query)) {
                        $sql_where = "HAVING (s.name LIKE ? OR s.description LIKE ?)";
                        $search_term = "%" . $search_query . "%";
                        $params[] = $search_term;
                        $params[] = $search_term;
                    }

                    // KOREKSI QUERY UTAMA: Ganti ID di subquery JOIN users
                    $sql_sections = "
                        SELECT 
                            s.id, s.name, s.description, 
                            COUNT(DISTINCT t.id) AS topic_count, 
                            MAX(p.created_at) AS last_post_time, 
                            (SELECT u_inner.username 
                             FROM forum_posts p_inner
                             JOIN users u_inner ON p_inner.user_id = u_inner.id_users -- KOREKSI: user_id JOIN id_users
                             WHERE p_inner.topic_id IN (SELECT id FROM forum_topics WHERE section_id = s.id)
                             ORDER BY p_inner.created_at DESC LIMIT 1) AS last_post_author
                        FROM forum_sections s
                        LEFT JOIN forum_topics t ON s.id = t.section_id
                        LEFT JOIN forum_posts p ON t.id = p.topic_id
                        GROUP BY s.id, s.name, s.description
                        $sql_where
                        $sql_order_by";

                    $stmt_sections = $pdo->prepare($sql_sections);
                    $stmt_sections->execute($params);

                    if ($stmt_sections->rowCount() > 0) {
                        while ($section = $stmt_sections->fetch(PDO::FETCH_ASSOC)) {
                            // ... (Logic icon_class tidak diubah) ...
                            $icon_class = 'fa-comments';
                            if (stripos($section['name'], 'gizi') !== false) $icon_class = 'fa-apple-alt';
                            elseif (stripos($section['name'], 'saran') !== false) $icon_class = 'fa-lightbulb';
                            elseif (stripos($section['name'], 'komunitas') !== false) $icon_class = 'fa-users';
                            elseif (stripos($section['name'], 'mainan') !== false) $icon_class = 'fa-shapes';
                            elseif (stripos($section['name'], 'kesehatan') !== false) $icon_class = 'fa-heartbeat';
                ?>
                            <li class="thread-item">
                                <div class="thread-icon-wrapper">
                                    <i class="fas <?php echo $icon_class; ?> thread-icon"></i>
                                </div>
                                <div class="thread-content">
                                    <a href="view_section.php?id=<?php echo $section['id']; ?>" class="thread-title">
                                        <?php echo htmlspecialchars($section['name']); ?>
                                    </a>
                                    <p class="thread-description"><?php echo htmlspecialchars($section['description']); ?></p>
                                </div>
                                <div class="thread-stats">
                                    <span class="stat-highlight"><?php echo $section['topic_count']; ?> Topik</span>
                                    <div class="latest-meta">
                                        <?php if ($section['last_post_time']): ?>
                                            <i class="far fa-clock"></i> <?php echo date('d M, H:i', strtotime($section['last_post_time'])); ?><br>
                                            oleh <strong><?php echo htmlspecialchars($section['last_post_author'] ?? '-'); ?></strong>
                                        <?php else: ?>
                                            Belum ada aktivitas
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                <?php
                        }
                    } else {
                        // ... (Logic tampilan jika tidak ada kategori tidak diubah) ...
                        echo '<li style="padding: 40px; text-align: center; color: #999;">';
                        if (!empty($search_query)) {
                            echo '<i class="fas fa-search" style="font-size: 2em; margin-bottom: 10px; display:block;"></i>';
                            echo 'Tidak ditemukan kategori dengan kata kunci "<strong>' . htmlspecialchars($search_query) . '</strong>".';
                        } else {
                            echo 'Belum ada kategori forum.';
                        }
                        echo '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                } catch (Exception $e) {
                    echo '<div class="alert-box error">Gagal memuat data forum. Detail: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                ?>
            </div>

            <aside class="forum-sidebar">
                <div class="stats-card animate-on-scroll">
                    <h4><i class="fas fa-chart-pie" style="color: #ff8fab; margin-right: 10px;"></i> Statistik</h4>
                    <div class="stat-row"><span>Total Topik</span><span class="stat-value text-pink"><?php echo $thread_count; ?></span></div>
                    <div class="stat-row"><span>Total Postingan</span><span class="stat-value text-pink"><?php echo $post_count; ?></span></div>
                    <div class="stat-row"><span>Anggota Bergabung</span><span class="stat-value text-pink"><?php echo $member_count; ?></span></div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-stat-box">
                            <p>Kontribusi Anda:</p>
                            <strong><i class="fas fa-comment-dots"></i> <?php echo $user_post_count; ?> Post</strong>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</main>

<script src="dropdown-script.js"></script>

<?php include 'footer.php'; ?>