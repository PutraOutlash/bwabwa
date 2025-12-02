<?php
session_start();
// --- Pengecekan Keamanan ---
// Gunakan security_check.php yang sudah kita perbaiki
// Pastikan path ke security_check.php sudah benar
// include 'layout/security_check.php'; 
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../html/login.php");
    exit;
}

// Pastikan include path ini benar
include '../config/db_connect.php';

$pageTitle = "Tambah Artikel Baru";

$error_message = '';
$success_message = '';
$user_id = $_SESSION['user_id'] ?? 1; // ID admin yang sedang login (Gunakan default jika user_id tidak diset)

// --- 1. Ambil Data Kategori ---
$stmt_cat = $pdo->query("SELECT id, name FROM article_categories ORDER BY name ASC");
$categories = $stmt_cat->fetchAll();

// --- 2. Proses Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $short_description = trim($_POST['short_description']);
    $external_url = trim($_POST['external_url']);
    $author = trim($_POST['author']);
    $status = $_POST['status'];

    // Slug otomatis dari title
    $slug = strtolower(str_replace(' ', '-', $title));

    // Ambil data POST untuk mempertahankan input jika terjadi error
    $post_data = $_POST;

    // Periksa apakah semua field wajib terisi
    if (empty($title) || empty($short_description) || empty($external_url) || empty($author) || $category_id == 0) {
        $error_message = "Semua field wajib diisi dan Kategori harus dipilih.";
    } else {
        try {
            // Tangani tanggal publikasi
            $published_at = ($status == 'published') ? date('Y-m-d H:i:s') : NULL;

            // Query untuk memasukkan data artikel baru
            $sql = "INSERT INTO articles (user_id, category_id, title, slug, short_description, external_url, author, status, created_at, published_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $user_id,
                $category_id,
                $title,
                $slug,
                $short_description,
                $external_url,
                $author,
                $status,
                $published_at
            ]);

            $success_message = "Artikel **{$title}** berhasil ditambahkan!";
            // Redirect ke halaman daftar artikel setelah berhasil
            header("Location: articles.php?success=created");
            exit;
        } catch (\PDOException $e) {
            $error_message = "Gagal menyimpan data: " . $e->getMessage();
            // Logging error detail jika perlu: error_log($e->getMessage());
        }
    }
} else {
    // Inisialisasi data POST kosong jika bukan submission
    $post_data = [];
}
?>

<?php include 'layout/header_admin.php'; ?>

<style>
    /* Variabel Warna */
    :root {
        /* Menggunakan primary pink dari tema BloomBelly */
        --color-primary-pink: #ff8fab;
        --color-secondary: #6c757d;
        --color-info: #17a2b8;
    }

    /* MODIFIKASI: Styling Card Formulir Utama */
    .form-main-card {
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
    }

    /* MODIFIKASI: Header di dalam Card */
    .card-header-custom {
        padding: 1.25rem 1.5rem;
        /* PENTING: Padding dikurangi */
        background-color: #f7f7f7;
        border-bottom: 1px solid #eee;
        border-radius: 15px 15px 0 0;
        display: flex;
        align-items: center;
    }

    .card-header-custom h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        color: #343a40;
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

    /* Label & Input */
    .form-label {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 0.5rem;
        font-size: 1.05rem;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        /* PENTING: Padding vertikal dikurangi */
        padding: 0.65rem 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--color-primary-pink);
        box-shadow: 0 0 0 0.25rem rgba(255, 143, 171, 0.4);
    }

    /* Input Group */
    .input-group-text {
        border-radius: 10px 0 0 10px;
        background-color: #f8f8f8;
        color: var(--color-secondary);
        /* PENTING: Padding vertikal disamakan */
        padding: 0.65rem 1rem;
    }

    /* Button Simpan */
    .btn-primary {
        background-color: var(--color-primary-pink);
        border-color: var(--color-primary-pink);
        font-weight: 600;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        transition: background-color 0.2s;
    }

    .btn-primary:hover {
        background-color: #e07ca7;
        border-color: #e07ca7;
    }
</style>

<div class="card form-main-card mb-5">

    <div class="card-header-custom">
        <a href="articles.php" title="Kembali ke Daftar Artikel" class="icon-back-link">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="flex-grow-1"><i class="fas fa-plus-circle me-2" style="color: var(--color-primary-pink);"></i> Tambah Artikel Baru</h2>
    </div>

    <div class="card-body p-4">

        <?php if ($error_message): ?>
            <div class="alert alert-danger fade show" role="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="alert alert-success fade show" role="alert"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="article_create.php">

            <div class="mb-4">
                <label for="title" class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required
                    value="<?php echo htmlspecialchars($post_data['title'] ?? ''); ?>"
                    placeholder="Masukkan judul artikel yang jelas">
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"
                                <?php echo (($post_data['category_id'] ?? 0) == $cat['id'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="draft" <?php echo (($post_data['status'] ?? 'draft') == 'draft' ? 'selected' : ''); ?>>Draft (Belum Publik)</option>
                        <option value="published" <?php echo (($post_data['status'] ?? '') == 'published' ? 'selected' : ''); ?>>Published (Langsung Publikasikan)</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="author" class="form-label">Nama Penulis <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="author" name="author" required
                    value="<?php echo htmlspecialchars($post_data['author'] ?? $_SESSION['name'] ?? 'Admin'); ?>"
                    placeholder="Contoh: Dr. Maya">
                <small class="form-text text-muted">Nama yang akan ditampilkan sebagai penulis.</small>
            </div>

            <div class="mb-4">
                <label for="external_url" class="form-label">External URL (Sumber Asli) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                    <input type="url" class="form-control" id="external_url" name="external_url" required
                        value="<?php echo htmlspecialchars($post_data['external_url'] ?? ''); ?>"
                        placeholder="https://example.com/artikel-sumber">
                </div>
                <small class="form-text text-muted">Link ke sumber artikel eksternal.</small>
            </div>

            <div class="mb-4">
                <label for="short_description" class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                <textarea class="form-control" id="short_description" name="short_description" rows="4" required
                    placeholder="Tuliskan ringkasan singkat artikel (maksimal 2-3 baris)."><?php echo htmlspecialchars($post_data['short_description'] ?? ''); ?></textarea>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3">
                <button type="submit" class="btn btn-primary btn-lg align-items-center">
                    <i class="fas fa-save me-2"></i> Simpan Artikel
                </button>
            </div>

        </form>

    </div>
</div>

<?php include 'layout/footer_admin.php'; ?>