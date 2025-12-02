<?php
session_start();
// --- Pengecekan Keamanan ---
// Pastikan path ke security_check dan db_connect sudah benar
include 'layout/security_check.php';
include '../config/db_connect.php';

$pageTitle = "Edit Data Nutrisi";
$error_message = '';
$success_message = '';
$nutrisi = null;
$max_minggu = 40;
$nutrisi_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- 1. Proses Form Submission (UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nutrisi_id_post = (int)$_POST['nutrisi_id'];
    $minggu_ke = (int)$_POST['minggu_ke'];
    $kandungan_nutrisi = trim($_POST['kandungan_nutrisi']);
    $makanan_rekomendasi = trim($_POST['makanan_rekomendasi']);
    $manfaat = trim($_POST['manfaat']);
    $makanan_dihindari = trim($_POST['makanan_dihindari']);

    // Validasi sederhana
    if ($minggu_ke < 1 || $minggu_ke > $max_minggu || empty($kandungan_nutrisi)) {
        $error_message = "Minggu kehamilan harus antara 1-{$max_minggu} dan Kandungan Nutrisi wajib diisi.";
    } else {
        try {
            // Cek duplikasi: Pastikan minggu_ke yang di-update tidak bertabrakan dengan data lain
            $stmt_check = $pdo->prepare("SELECT COUNT(id_nutrisi) FROM nutrisi WHERE minggu_ke = ? AND id_nutrisi != ?");
            $stmt_check->execute([$minggu_ke, $nutrisi_id_post]);
            if ($stmt_check->fetchColumn() > 0) {
                $error_message = "Data nutrisi untuk Minggu Ke-{$minggu_ke} sudah ada di record lain.";
            } else {
                // Query untuk memperbarui data
                $sql = "UPDATE nutrisi SET 
                            minggu_ke = ?, 
                            kandungan_nutrisi = ?, 
                            makanan_rekomendasi = ?, 
                            manfaat = ?, 
                            makanan_dihindari = ?
                        WHERE id_nutrisi = ?";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $minggu_ke,
                    $kandungan_nutrisi,
                    $makanan_rekomendasi,
                    $manfaat,
                    $makanan_dihindari,
                    $nutrisi_id_post
                ]);

                $success_message = "Data Nutrisi Minggu Ke-{$minggu_ke} berhasil diperbarui!";
                // Redirect ke halaman daftar data management setelah berhasil
                header("Location: data_management.php?success=nutrisi_updated");
                exit;
            }
        } catch (\PDOException $e) {
            $error_message = "Gagal memperbarui data: " . $e->getMessage();
        }
    }
}

// --- 3. Ambil Data yang Akan Diedit ---
if ($nutrisi_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM nutrisi WHERE id_nutrisi = ?");
    $stmt->execute([$nutrisi_id]);
    $nutrisi = $stmt->fetch();

    if (!$nutrisi) {
        $error_message = "Data nutrisi tidak ditemukan.";
    }
} else if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // Jika tidak ada ID dan bukan post request, redirect
    header("Location: data_management.php");
    exit;
}

// Gunakan data dari database atau dari POST jika ada error saat submit
$data_to_display = $nutrisi;
if ($_SERVER["REQUEST_METHOD"] == "POST" && $nutrisi) {
    $data_to_display = $_POST;
    $data_to_display['id_nutrisi'] = $nutrisi_id;
}
?>

<?php
// PENTING: Mengganti struktur HTML lama dengan layout modular
include 'layout/header_admin.php';
?>

<style>
    /* Variabel Warna */
    :root {
        --color-primary-pink: #ff8fab;
        --color-secondary: #6c757d;
        --color-info-cyan: #0dcaf0;
    }

    /* --- Struktur Formulir --- */
    .form-main-card {
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
    }

    .card-header-custom {
        padding: 1.25rem 1.5rem;
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
        padding: 0.65rem 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--color-primary-pink);
        box-shadow: 0 0 0 0.25rem rgba(255, 143, 171, 0.4);
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
        <a href="data_management.php" title="Kembali ke Manajemen Data" class="icon-back-link">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="flex-grow-1"><i class="fas fa-utensils me-2" style="color: var(--color-primary-pink);"></i> Edit Data Nutrisi (Minggu ke-<?php echo htmlspecialchars($data_to_display['minggu_ke'] ?? 'N/A'); ?>)</h2>
    </div>

    <div class="card-body p-4">

        <?php if ($error_message): ?>
            <div class="alert alert-danger fade show" role="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="alert alert-success fade show" role="alert"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (!$nutrisi && $_SERVER["REQUEST_METHOD"] != "POST"): ?>
            <div class="alert alert-warning">Data Nutrisi tidak ditemukan.</div>
        <?php else: ?>

            <form method="POST" action="nutrisi_edit.php?id=<?php echo $data_to_display['id_nutrisi']; ?>">
                <input type="hidden" name="nutrisi_id" value="<?php echo $data_to_display['id_nutrisi']; ?>">

                <div class="mb-4">
                    <label for="minggu_ke" class="form-label">Minggu Kehamilan *</label>
                    <input type="number" class="form-control" id="minggu_ke" name="minggu_ke" min="1" max="<?php echo $max_minggu; ?>" required
                        value="<?php echo htmlspecialchars($data_to_display['minggu_ke'] ?? ''); ?>"
                        placeholder="Masukkan angka minggu kehamilan (1 sampai <?php echo $max_minggu; ?>)">
                    <small class="form-text text-muted">Data nutrisi hanya dapat diinput satu kali per minggu.</small>
                </div>

                <div class="mb-4">
                    <label for="kandungan_nutrisi" class="form-label">Kandungan Nutrisi Utama (Contoh: Asam Folat, Zat Besi) *</label>
                    <textarea class="form-control" id="kandungan_nutrisi" name="kandungan_nutrisi" rows="3" required
                        placeholder="Sebutkan beberapa kandungan nutrisi utama yang dibutuhkan minggu ini."><?php echo htmlspecialchars($data_to_display['kandungan_nutrisi'] ?? ''); ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="makanan_rekomendasi" class="form-label">Makanan Rekomendasi</label>
                    <textarea class="form-control" id="makanan_rekomendasi" name="makanan_rekomendasi" rows="3"
                        placeholder="Daftar makanan yang disarankan untuk mendapatkan nutrisi di atas."><?php echo htmlspecialchars($data_to_display['makanan_rekomendasi'] ?? ''); ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="manfaat" class="form-label">Manfaat Nutrisi</label>
                    <textarea class="form-control" id="manfaat" name="manfaat" rows="3"
                        placeholder="Jelaskan manfaat utama dari nutrisi yang direkomendasikan."><?php echo htmlspecialchars($data_to_display['manfaat'] ?? ''); ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="makanan_dihindari" class="form-label">Makanan yang Harus Dihindari</label>
                    <textarea class="form-control" id="makanan_dihindari" name="makanan_dihindari" rows="3"
                        placeholder="Daftar makanan yang harus dibatasi atau dihindari minggu ini."><?php echo htmlspecialchars($data_to_display['makanan_dihindari'] ?? ''); ?></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3">
                    <button type="submit" class="btn btn-primary btn-lg align-items-center">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan Nutrisi
                    </button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</div>

<?php
// PENTING: Asumsi 'layout/footer_admin.php' ada
include 'layout/footer_admin.php';
?>