<?php
// File: perkembangan_edit.php
// Mengedit data Perkembangan Janin berdasarkan ID

// Panggil Security Check dan Koneksi DB
include 'layout/security_check.php';

$pageTitle = "Edit Data Perkembangan Janin";
$error_message = '';
$success_message = '';
$max_minggu = 40;

// ==============================================================================
// 1. AMBIL ID DAN DATA LAMA
// ==============================================================================

// Pastikan ID perkembangan ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: data_management.php?tab=perkembangan&error=no_id");
    exit;
}

$id_perkembangan = (int)$_GET['id'];

try {
    // Ambil data lama untuk diisi ke form
    // CATATAN: Asumsi tabel masih bernama 'perkembangan' seperti di kode lama
    $stmt_fetch = $pdo->prepare("SELECT * FROM perkembangan WHERE id_perkembangan = ?");
    $stmt_fetch->execute([$id_perkembangan]);
    $data = $stmt_fetch->fetch();

    // Jika data tidak ditemukan
    if (!$data) {
        header("Location: data_management.php?tab=perkembangan&error=not_found");
        exit;
    }
} catch (\PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}


// ==============================================================================
// 2. HANDLE POST (UPDATE DATA)
// ==============================================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $minggu_ke = (int)$_POST['minggu_ke'];
    $ukuran_bayi = trim($_POST['ukuran_bayi']);
    $perkembangan_terbaru = trim($_POST['perkembangan_terbaru']);
    $tips = trim($_POST['tips']);
    $hal_dihindari = trim($_POST['hal_dihindari']);

    // Validasi sederhana
    if ($minggu_ke < 1 || $minggu_ke > $max_minggu || empty($ukuran_bayi) || empty($perkembangan_terbaru)) {
        $error_message = "Minggu, Ukuran, dan Perkembangan wajib diisi.";
    } else {
        // Cek apakah 'minggu_ke' yang baru sudah digunakan oleh data lain
        $stmt_check = $pdo->prepare("SELECT COUNT(id_perkembangan) FROM perkembangan WHERE minggu_ke = ? AND id_perkembangan != ?");
        $stmt_check->execute([$minggu_ke, $id_perkembangan]);

        if ($stmt_check->fetchColumn() > 0) {
            $error_message = "Data perkembangan untuk Minggu Ke-{$minggu_ke} sudah ada pada data lain.";
        } else {
            try {
                // Query UPDATE
                $sql = "UPDATE perkembangan SET 
                            minggu_ke = ?, 
                            ukuran_bayi = ?, 
                            perkembangan_terbaru = ?, 
                            tips = ?, 
                            hal_dihindari = ?
                        WHERE id_perkembangan = ?";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$minggu_ke, $ukuran_bayi, $perkembangan_terbaru, $tips, $hal_dihindari, $id_perkembangan]);

                // Redirect ke halaman data management setelah berhasil
                header("Location: data_management.php?tab=perkembangan&success=perkembangan_updated");
                exit;
            } catch (\PDOException $e) {
                $error_message = "Gagal memperbarui data: " . $e->getMessage();

                // Jika gagal, pastikan $data diisi ulang dari $_POST agar input tidak hilang
                $data = [
                    'minggu_ke' => $minggu_ke,
                    'ukuran_bayi' => $ukuran_bayi,
                    'perkembangan_terbaru' => $perkembangan_terbaru,
                    'tips' => $tips,
                    'hal_dihindari' => $hal_dihindari
                ];
            }
        }
    }
}
?>

<?php include 'layout/header_admin.php'; ?>

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
        <a href="data_management.php?tab=perkembangan" title="Kembali ke Manajemen Data" class="icon-back-link">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="flex-grow-1"><i class="fas fa-chart-bar me-2" style="color: var(--color-info-cyan);"></i> <?php echo $pageTitle; ?> (Minggu Ke-<?php echo htmlspecialchars($data['minggu_ke']); ?>)</h2>
    </div>

    <div class="card-body p-4">

        <?php if ($error_message): ?>
            <div class="alert alert-danger fade show" role="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success fade show" role="alert"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="perkembangan_edit.php?id=<?php echo $id_perkembangan; ?>">

            <div class="mb-4">
                <label for="minggu_ke" class="form-label">Minggu Kehamilan *</label>
                <input type="number" class="form-control" id="minggu_ke" name="minggu_ke" min="1" max="<?php echo $max_minggu; ?>" required
                    value="<?php echo htmlspecialchars($data['minggu_ke']); ?>"
                    placeholder="Contoh: 12">
            </div>

            <div class="mb-4">
                <label for="ukuran_bayi" class="form-label">Ukuran Janin (Contoh: Sebesar Lemon, 70 gram) *</label>
                <input type="text" class="form-control" id="ukuran_bayi" name="ukuran_bayi" required
                    value="<?php echo htmlspecialchars($data['ukuran_bayi']); ?>"
                    placeholder="Contoh: Sebesar buah plum atau 70 gram">
            </div>

            <div class="mb-4">
                <label for="perkembangan_terbaru" class="form-label">Deskripsi Perkembangan Janin *</label>
                <textarea class="form-control" id="perkembangan_terbaru" name="perkembangan_terbaru" rows="5" required
                    placeholder="Jelaskan perkembangan organ dan sistem utama janin pada minggu ini."><?php echo htmlspecialchars($data['perkembangan_terbaru']); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="tips" class="form-label">Tips untuk Ibu (Opsional)</label>
                <textarea class="form-control" id="tips" name="tips" rows="3"
                    placeholder="Saran aktivitas, nutrisi, atau kesehatan untuk ibu hamil."><?php echo htmlspecialchars($data['tips']); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="hal_dihindari" class="form-label">Hal yang Harus Dihindari (Opsional)</label>
                <textarea class="form-control" id="hal_dihindari" name="hal_dihindari" rows="3"
                    placeholder="Daftar makanan, aktivitas, atau kebiasaan buruk yang harus dihindari."><?php echo htmlspecialchars($data['hal_dihindari']); ?></textarea>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3">
                <button type="submit" class="btn btn-primary btn-lg align-items-center">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

<?php include 'layout/footer_admin.php'; ?>