<?php
session_start();
// --- Pengecekan Keamanan ---
<<<<<<< HEAD
=======
// Gunakan security_check.php yang sudah kita perbaiki
// Pastikan path ke security_check.php sudah benar
// include 'layout/security_check.php'; 
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../html/login.php");
    exit;
}

// Pastikan include path ini benar
include '../config/db_connect.php';

$pageTitle = "Tambah Artikel Baru";

$error_message = '';
$success_message = '';
<<<<<<< HEAD
$user_id = $_SESSION['user_id'] ?? 1;
=======
$user_id = $_SESSION['user_id'] ?? 1; // ID admin yang sedang login (Gunakan default jika user_id tidak diset)
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2

// --- 1. Ambil Data Kategori ---
$stmt_cat = $pdo->query("SELECT id, name FROM article_categories ORDER BY name ASC");
$categories = $stmt_cat->fetchAll();

// --- 2. Proses Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
<<<<<<< HEAD
    // Ambil data dengan trim
    $title = trim($_POST['title'] ?? '');
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $short_description = trim($_POST['short_description'] ?? '');
    $external_url = trim($_POST['external_url'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $featured_image_url = trim($_POST['featured_image_url'] ?? '');

    // Debug: Tampilkan data yang diterima
    error_log("DEBUG - Data Received:");
    error_log("Title: " . $title);
    error_log("Category ID: " . $category_id);
    error_log("Short Desc: " . (empty($short_description) ? 'EMPTY' : 'FILLED'));
    error_log("External URL: " . $external_url);
    error_log("Author: " . $author);
    error_log("Featured Image URL: " . $featured_image_url);
    error_log("Status: " . $status);

    // Slug otomatis dari title
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));

    // Simpan data POST untuk form jika error
    $post_data = $_POST;

    // Periksa apakah semua field wajib terisi
    $validation_errors = [];
    
    if (empty($title)) {
        $validation_errors[] = "Judul artikel harus diisi";
    }
    
    if ($category_id == 0) {
        $validation_errors[] = "Kategori harus dipilih";
    }
    
    if (empty($short_description)) {
        $validation_errors[] = "Deskripsi singkat harus diisi";
    }
    
    if (empty($external_url)) {
        $validation_errors[] = "External URL harus diisi";
    } elseif (!filter_var($external_url, FILTER_VALIDATE_URL)) {
        $validation_errors[] = "External URL tidak valid";
    }
    
    if (empty($author)) {
        $validation_errors[] = "Nama penulis harus diisi";
    }
    
    if (empty($featured_image_url)) {
        $validation_errors[] = "URL gambar utama harus diisi";
    } elseif (!filter_var($featured_image_url, FILTER_VALIDATE_URL)) {
        $validation_errors[] = "URL gambar utama tidak valid. Pastikan format URL benar (contoh: https://example.com/image.jpg)";
    }
    
    if (empty($status)) {
        $validation_errors[] = "Status harus dipilih";
    }

    // Jika ada error validasi
    if (!empty($validation_errors)) {
        $error_message = implode("<br>", $validation_errors);
    } else {
        // Semua validasi OK, simpan ke database
=======
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
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
        try {
            // Tangani tanggal publikasi
            $published_at = ($status == 'published') ? date('Y-m-d H:i:s') : NULL;

            // Query untuk memasukkan data artikel baru
<<<<<<< HEAD
            $sql = "INSERT INTO articles (user_id, category_id, title, slug, short_description, 
                    external_url, author, featured_image_url, status, created_at, published_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
=======
            $sql = "INSERT INTO articles (user_id, category_id, title, slug, short_description, external_url, author, status, created_at, published_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
                $user_id,
                $category_id,
                $title,
                $slug,
                $short_description,
                $external_url,
                $author,
<<<<<<< HEAD
                $featured_image_url,
=======
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
                $status,
                $published_at
            ]);

<<<<<<< HEAD
            if ($result) {
                $success_message = "Artikel **{$title}** berhasil ditambahkan!";
                // Redirect ke halaman daftar artikel setelah berhasil
                header("Location: articles.php?success=created");
                exit;
            } else {
                $error_message = "Gagal menyimpan data artikel.";
            }
            
        } catch (\PDOException $e) {
            $error_message = "Gagal menyimpan data: " . $e->getMessage();
            error_log("Database Error: " . $e->getMessage());
=======
            $success_message = "Artikel **{$title}** berhasil ditambahkan!";
            // Redirect ke halaman daftar artikel setelah berhasil
            header("Location: articles.php?success=created");
            exit;
        } catch (\PDOException $e) {
            $error_message = "Gagal menyimpan data: " . $e->getMessage();
            // Logging error detail jika perlu: error_log($e->getMessage());
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
        }
    }
} else {
    // Inisialisasi data POST kosong jika bukan submission
    $post_data = [];
}
?>

<?php include 'layout/header_admin.php'; ?>

<style>
<<<<<<< HEAD
    :root {
        --color-primary-pink: #ff8fab;
        --color-secondary: #6c757d;
    }

=======
    /* Variabel Warna */
    :root {
        /* Menggunakan primary pink dari tema BloomBelly */
        --color-primary-pink: #ff8fab;
        --color-secondary: #6c757d;
        --color-info: #17a2b8;
    }

    /* MODIFIKASI: Styling Card Formulir Utama */
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
    .form-main-card {
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
    }

<<<<<<< HEAD
    .card-header-custom {
        padding: 1.25rem 1.5rem;
=======
    /* MODIFIKASI: Header di dalam Card */
    .card-header-custom {
        padding: 1.25rem 1.5rem;
        /* PENTING: Padding dikurangi */
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
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

<<<<<<< HEAD
=======
    /* Ikon Kembali (Aesthetic) */
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
    .icon-back-link {
        font-size: 1.5rem;
        color: var(--color-secondary);
        margin-right: 15px;
        transition: color 0.2s;
    }

    .icon-back-link:hover {
        color: var(--color-primary-pink);
    }

<<<<<<< HEAD
=======
    /* Label & Input */
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
    .form-label {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 0.5rem;
        font-size: 1.05rem;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
<<<<<<< HEAD
        padding: 0.65rem 1rem;
        border: 1px solid #ced4da;
=======
        /* PENTING: Padding vertikal dikurangi */
        padding: 0.65rem 1rem;
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--color-primary-pink);
        box-shadow: 0 0 0 0.25rem rgba(255, 143, 171, 0.4);
    }

<<<<<<< HEAD
=======
    /* Input Group */
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
    .input-group-text {
        border-radius: 10px 0 0 10px;
        background-color: #f8f8f8;
        color: var(--color-secondary);
<<<<<<< HEAD
        padding: 0.65rem 1rem;
        border: 1px solid #ced4da;
        border-right: none;
    }

=======
        /* PENTING: Padding vertikal disamakan */
        padding: 0.65rem 1rem;
    }

    /* Button Simpan */
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
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
<<<<<<< HEAD

    /* Styling untuk alert error yang lebih jelas */
    .alert-danger {
        background-color: #fff5f5;
        border-color: #feb2b2;
        color: #c53030;
        border-radius: 10px;
        padding: 1rem 1.25rem;
    }

    /* Highlight field yang error */
    .field-error {
        border-color: #fc8181 !important;
        background-color: #fff5f5 !important;
    }

    .field-error:focus {
        border-color: #fc8181 !important;
        box-shadow: 0 0 0 0.25rem rgba(252, 129, 129, 0.25) !important;
    }

    /* Image preview */
    .image-preview-container {
        border: 2px dashed #e2e8f0;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        background-color: #f8fafc;
        margin-top: 10px;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .image-preview-container.has-image {
        border-color: #48bb78;
        background-color: #f0fff4;
    }

    .image-preview {
        max-width: 100%;
        max-height: 120px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: none;
    }

    .no-image-preview {
        color: #718096;
        font-size: 0.9rem;
    }

    .image-source-links {
        background: #edf2f7;
        border-radius: 8px;
        padding: 12px 15px;
        margin-top: 10px;
        border-left: 4px solid var(--color-primary-pink);
    }

    .image-source-links h6 {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #2d3748;
    }

    .image-source-links ul {
        margin: 0;
        padding-left: 18px;
        font-size: 0.85rem;
        color: #4a5568;
    }

    .image-source-links li {
        margin-bottom: 4px;
    }

    /* Required field indicator */
    .required-field::after {
        content: " *";
        color: #e53e3e;
    }
=======
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
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
<<<<<<< HEAD
            <div class="alert alert-danger fade show mb-4" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-triangle me-3 mt-1" style="font-size: 1.2rem;"></i>
                    <div>
                        <h6 class="alert-heading mb-2">Perhatian!</h6>
                        <?php echo $error_message; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="article_create.php" id="articleForm">

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="title" class="form-label required-field">Judul Artikel</label>
                    <input type="text" class="form-control <?php echo (isset($validation_errors) && empty($title) ? 'field-error' : ''); ?>" 
                           id="title" name="title" required
                           value="<?php echo htmlspecialchars($post_data['title'] ?? ''); ?>"
                           placeholder="Masukkan judul artikel yang jelas">
                </div>
                <div class="col-md-6 mb-4">
                    <label for="category_id" class="form-label required-field">Kategori</label>
                    <select class="form-select <?php echo (isset($validation_errors) && $category_id == 0 ? 'field-error' : ''); ?>" 
                            id="category_id" name="category_id" required>
=======
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
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"
                                <?php echo (($post_data['category_id'] ?? 0) == $cat['id'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
<<<<<<< HEAD
            </div>

            <!-- Featured Image URL -->
            <div class="mb-4">
                <label for="featured_image_url" class="form-label required-field">URL Gambar Utama (Featured)</label>
                <div class="input-group mb-2">
                    <span class="input-group-text"><i class="fas fa-image"></i></span>
                    <input type="url" class="form-control <?php echo (isset($validation_errors) && empty($featured_image_url) ? 'field-error' : ''); ?>" 
                           id="featured_image_url" name="featured_image_url" required
                           value="<?php echo htmlspecialchars($post_data['featured_image_url'] ?? ''); ?>"
                           placeholder="https://example.com/gambar-utama.jpg"
                           oninput="previewImageFromUrl(this.value)">
                </div>
                <small class="form-text text-muted d-block mb-2">
                    Masukkan URL lengkap gambar utama/featured image (contoh: https://example.com/gambar.jpg)
                </small>
                
                <!-- Image Preview -->
                <div class="image-preview-container" id="imagePreviewContainer">
                    <div class="no-image-preview" id="noImagePreview">
                        <i class="fas fa-image fa-2x mb-2" style="color: #a0aec0;"></i><br>
                        <span class="small">Preview gambar akan muncul di sini</span>
                    </div>
                    <img src="" class="image-preview" id="imagePreview" alt="Preview Gambar">
                </div>

                <!-- Sumber Gambar Gratis -->
                <div class="image-source-links">
                    <h6><i class="fas fa-lightbulb me-1"></i> Sumber Gambar Gratis:</h6>
                    <ul>
                        <li><a href="https://unsplash.com" target="_blank">Unsplash.com</a> - Foto berkualitas tinggi</li>
                        <li><a href="https://pixabay.com" target="_blank">Pixabay.com</a> - Gambar gratis bebas royalti</li>
                        <li><a href="https://pexels.com" target="_blank">Pexels.com</a> - Stok foto gratis</li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="status" class="form-label required-field">Status</label>
                    <select class="form-select <?php echo (isset($validation_errors) && empty($status) ? 'field-error' : ''); ?>" 
                            id="status" name="status" required>
=======
                <div class="col-md-6 mb-4">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
                        <option value="draft" <?php echo (($post_data['status'] ?? 'draft') == 'draft' ? 'selected' : ''); ?>>Draft (Belum Publik)</option>
                        <option value="published" <?php echo (($post_data['status'] ?? '') == 'published' ? 'selected' : ''); ?>>Published (Langsung Publikasikan)</option>
                    </select>
                </div>
<<<<<<< HEAD
                <div class="col-md-6 mb-4">
                    <label for="author" class="form-label required-field">Nama Penulis</label>
                    <input type="text" class="form-control <?php echo (isset($validation_errors) && empty($author) ? 'field-error' : ''); ?>" 
                           id="author" name="author" required
                           value="<?php echo htmlspecialchars($post_data['author'] ?? $_SESSION['name'] ?? 'Admin'); ?>"
                           placeholder="Contoh: Dr. Maya">
                    <small class="form-text text-muted">Nama yang akan ditampilkan sebagai penulis.</small>
                </div>
            </div>

            <div class="mb-4">
                <label for="external_url" class="form-label required-field">External URL (Sumber Asli)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                    <input type="url" class="form-control <?php echo (isset($validation_errors) && (empty($external_url) || !filter_var($external_url, FILTER_VALIDATE_URL)) ? 'field-error' : ''); ?>" 
                           id="external_url" name="external_url" required
                           value="<?php echo htmlspecialchars($post_data['external_url'] ?? ''); ?>"
                           placeholder="https://example.com/artikel-sumber">
=======
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
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
                </div>
                <small class="form-text text-muted">Link ke sumber artikel eksternal.</small>
            </div>

            <div class="mb-4">
<<<<<<< HEAD
                <label for="short_description" class="form-label required-field">Deskripsi Singkat</label>
                <textarea class="form-control <?php echo (isset($validation_errors) && empty($short_description) ? 'field-error' : ''); ?>" 
                          id="short_description" name="short_description" rows="4" required
                          placeholder="Tuliskan ringkasan singkat artikel (maksimal 2-3 baris)."><?php echo htmlspecialchars($post_data['short_description'] ?? ''); ?></textarea>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3 border-top">
                <a href="articles.php" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-times me-2"></i> Batal
                </a>
=======
                <label for="short_description" class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                <textarea class="form-control" id="short_description" name="short_description" rows="4" required
                    placeholder="Tuliskan ringkasan singkat artikel (maksimal 2-3 baris)."><?php echo htmlspecialchars($post_data['short_description'] ?? ''); ?></textarea>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3">
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
                <button type="submit" class="btn btn-primary btn-lg align-items-center">
                    <i class="fas fa-save me-2"></i> Simpan Artikel
                </button>
            </div>

        </form>

    </div>
</div>

<<<<<<< HEAD
<script>
function previewImageFromUrl(url) {
    const previewContainer = document.getElementById('imagePreviewContainer');
    const preview = document.getElementById('imagePreview');
    const noPreview = document.getElementById('noImagePreview');
    
    // Reset preview container
    previewContainer.classList.remove('has-image');
    
    if (url && url.trim() !== '') {
        // Tampilkan loading
        noPreview.innerHTML = '<i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br><span class="small">Memuat gambar...</span>';
        noPreview.style.display = 'block';
        preview.style.display = 'none';
        
        // Coba load gambar
        const img = new Image();
        img.src = url;
        
        img.onload = function() {
            preview.src = url;
            preview.style.display = 'block';
            noPreview.style.display = 'none';
            previewContainer.classList.add('has-image');
        };
        
        img.onerror = function() {
            preview.style.display = 'none';
            noPreview.style.display = 'block';
            previewContainer.classList.remove('has-image');
            noPreview.innerHTML = '<i class="fas fa-exclamation-triangle fa-2x mb-2" style="color: #e53e3e;"></i><br><span class="small">Gagal memuat gambar</span>';
        };
    } else {
        preview.style.display = 'none';
        noPreview.style.display = 'block';
        noPreview.innerHTML = '<i class="fas fa-image fa-2x mb-2" style="color: #a0aec0;"></i><br><span class="small">Preview gambar akan muncul di sini</span>';
    }
}

// Preview gambar jika sudah ada data
document.addEventListener('DOMContentLoaded', function() {
    const imageUrlInput = document.getElementById('featured_image_url');
    if (imageUrlInput.value) {
        previewImageFromUrl(imageUrlInput.value);
    }
    
    // Highlight fields yang error
    const form = document.getElementById('articleForm');
    form.addEventListener('submit', function(e) {
        let hasError = false;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('field-error');
                hasError = true;
                
                // Scroll ke field pertama yang error
                if (!form.querySelector('.field-error:first-child')) {
                    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                field.classList.remove('field-error');
            }
        });
        
        // Validasi URL gambar khusus
        const featuredImageUrl = document.getElementById('featured_image_url').value;
        if (featuredImageUrl && !isValidUrl(featuredImageUrl)) {
            alert('URL gambar utama tidak valid. Pastikan format URL benar (contoh: https://example.com/image.jpg)');
            e.preventDefault();
            return false;
        }
        
        if (hasError) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi.');
            return false;
        }
    });
});

function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

// Real-time validation untuk URL
document.getElementById('featured_image_url').addEventListener('blur', function() {
    const url = this.value.trim();
    if (url && !isValidUrl(url)) {
        this.classList.add('field-error');
    } else {
        this.classList.remove('field-error');
    }
});
</script>

=======
>>>>>>> 04496df43217f219ce325033a6ce208b8ee5c1f2
<?php include 'layout/footer_admin.php'; ?>