<?php
// bUAT TOPIK BARUUUU BOSS 

// 1. KONEKSI & OTENTIKASI
try {
    include_once __DIR__ . '/db.php';
} catch (Exception $e) {
    // Lanjut dulu
}

// 2. Setup Variabel Halaman SEBELUM Header
$pageTitle = "Buat Topik Baru - BloomBelly";
$css_version = time();
// Menghubungkan dengan file CSS khusus halaman ini
$pageCSS = ["../css/create-topic-style.css?v=" . $css_version];

// 3. Panggil Header
include 'header.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login untuk membuat topik.'); window.location.href='login.php';</script>";
    exit;
}

// 4. Ambil ID Section (Kategori) dari URL jika ada
// Contoh: create_topic.php?section_id=5
$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : '';
$section_name_display = "";

// Jika ada section_id di URL, ambil namanya untuk judul agar lebih personal
if ($section_id && isset($pdo)) {
    $stmt = $pdo->prepare("SELECT name FROM forum_sections WHERE id = ?");
    $stmt->execute([$section_id]);
    $sec = $stmt->fetch();
    if ($sec) {
        $section_name_display = " di " . htmlspecialchars($sec['name']);
    }
}
?>

<main class="create-topic-page">
    <div class="container">

        <div class="create-topic-wrapper animate-on-scroll">
            <!-- Header Card -->
            <div class="create-topic-header">
                <h1><i class="fas fa-pen-fancy"></i> Buat Topik Baru<?php echo $section_name_display; ?></h1>
                <p>Bagikan pertanyaan, cerita, atau diskusi Anda dengan komunitas Bunda lainnya.</p>
            </div>

            <!-- Form Card -->
            <div class="create-topic-card">
                <!-- Form akan dikirim ke process_create_topic.php -->
                <form action="process_create_topic.php" method="POST">

                    <!-- BAGIAN PILIH KATEGORI -->
                    <!-- Jika section_id SUDAH ada di URL (misal diklik dari halaman kategori), 
                         kita sembunyikan dropdown-nya dan kunci nilainya -->
                    <?php if (!empty($section_id)): ?>
                        <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($section_id); ?>">
                    <?php else: ?>
                        <!-- Jika TIDAK ada section_id di URL (diklik dari tombol umum), 
                             tampilkan dropdown agar user memilih -->
                        <div class="form-group">
                            <label for="section_id"><i class="fas fa-folder-open"></i> Pilih Kategori</label>
                            <select name="section_id" id="section_id" class="form-input" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                if (isset($pdo)) {
                                    $stmt_cat = $pdo->query("SELECT id, name FROM forum_sections ORDER BY name ASC");
                                    while ($row_cat = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $row_cat['id'] . '">' . htmlspecialchars($row_cat['name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="title"><i class="fas fa-heading"></i> Judul Topik</label>
                        <input type="text" id="title" name="title" class="form-input" placeholder="Contoh: Tips mengatasi mual di pagi hari..." required>
                        <small class="form-hint">Buat judul yang singkat namun jelas menggambarkan isi topik Anda.</small>
                    </div>

                    <div class="form-group">
                        <label for="content"><i class="fas fa-align-left"></i> Isi Postingan Pertama</label>
                        <textarea id="content" name="content" rows="12" class="form-input" placeholder="Tuliskan detail pertanyaan atau cerita Anda di sini..." required></textarea>
                    </div>

                    <div class="form-actions">
                        <!-- Tombol Batal kembali ke halaman sebelumnya -->
                        <a href="javascript:history.back()" class="btn-cancel">Batal</a>
                        <button type="submit" class="btn-publish">
                            <i class="fas fa-paper-plane"></i> Publikasikan Topik
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<?php include 'footer.php'; ?>