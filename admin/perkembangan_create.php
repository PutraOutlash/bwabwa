<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../html/login.php");
    exit;
}

include '../config/db_connect.php';

$pageTitle = "Tambah Data Perkembangan Janin";
$error_message = '';
$max_minggu = 40;

// Ambil data POST untuk mempertahankan input jika terjadi error
$post_data = $_POST;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $minggu_ke = (int)($post_data['minggu_ke'] ?? 0);
    // CATATAN: Field POST sudah benar.
    $deskripsi_janin = trim($post_data['deskripsi_janin'] ?? '');
    $ukuran_berat = trim($post_data['ukuran_berat'] ?? '');
    $ukuran_panjang = trim($post_data['ukuran_panjang'] ?? '');
    $tips = trim($post_data['tips'] ?? '');
    $hal_dihindari = trim($post_data['hal_dihindari'] ?? '');

    // Perhatikan perbedaan nama kolom di database Anda:
    // Tabel `perkembangan` memiliki kolom: `minggu_ke`, `ukuran_bayi`, `perkembangan_terbaru`, `tips`, `hal_dihindari`.
    // Kita harus memetakan input form ke kolom ini:
    // - ukuran_berat & ukuran_panjang harus digabung/disesuaikan ke `ukuran_bayi`
    // - deskripsi_janin harus dipetakan ke `perkembangan_terbaru`

    // Jika Anda ingin mempertahankan 3 kolom input, Anda bisa menggabungkannya ke kolom `ukuran_bayi` (VARCHAR)
    $ukuran_gabungan = "Berat: {$ukuran_berat}, Panjang: {$ukuran_panjang}";

    // Validasi
    if ($minggu_ke < 1 || $minggu_ke > $max_minggu || empty($deskripsi_janin) || empty($ukuran_berat) || empty($ukuran_panjang)) {
        $error_message = "Minggu, Deskripsi Janin, Berat, dan Panjang wajib diisi.";
    } else {
        try {
            // === PERBAIKAN 1: GANTI perkembangan_janin menjadi perkembangan ===
            $stmt_check = $pdo->prepare("SELECT COUNT(id_perkembangan) FROM perkembangan WHERE minggu_ke = ?");
            $stmt_check->execute([$minggu_ke]);
            if ($stmt_check->fetchColumn() > 0) {
                $error_message = "Data perkembangan untuk Minggu Ke-{$minggu_ke} sudah ada. Silakan edit.";
            } else {
                // === PERBAIKAN 2: GANTI NAMA KOLOM SQL SESUAI TABEL `perkembangan` ===
                // Kolom di DB: `minggu_ke`, `ukuran_bayi`, `perkembangan_terbaru`, `tips`, `hal_dihindari`
                // Input:     $minggu_ke, $ukuran_gabungan, $deskripsi_janin, $tips, $hal_dihindari
                $sql = "INSERT INTO perkembangan (minggu_ke, ukuran_bayi, perkembangan_terbaru, tips, hal_dihindari) 
                        VALUES (?, ?, ?, ?, ?)";

                $stmt = $pdo->prepare($sql);
                // Eksekusi dengan variabel yang sudah dipetakan
                $stmt->execute([$minggu_ke, $ukuran_gabungan, $deskripsi_janin, $tips, $hal_dihindari]);

                // Redirect dengan penanda tab aktif 'perkembangan'
                header("Location: data_management.php?tab=perkembangan&success=perkembangan_added");
                exit;
            }
        } catch (\PDOException $e) {
            $error_message = "Gagal menyimpan data: " . $e->getMessage();
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
        <h2 class="flex-grow-1"><i class="fas fa-chart-bar me-2" style="color: var(--color-info-cyan);"></i> <?php echo $pageTitle; ?></h2>
    </div>

    <div class="card-body p-4">

        <?php if ($error_message): ?>
            <div class="alert alert-danger fade show" role="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="perkembangan_create.php">

            <div class="mb-4">
                <label for="minggu_ke" class="form-label">Minggu Kehamilan *</label>
                <input type="number" class="form-control" id="minggu_ke" name="minggu_ke" min="1" max="<?php echo $max_minggu; ?>" required
                    value="<?php echo htmlspecialchars($post_data['minggu_ke'] ?? ''); ?>"
                    placeholder="Contoh: 12">
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="ukuran_berat" class="form-label">Ukuran Berat Janin (Contoh: 70 gram) *</label>
                    <input type="text" class="form-control" id="ukuran_berat" name="ukuran_berat" required
                        value="<?php echo htmlspecialchars($post_data['ukuran_berat'] ?? ''); ?>"
                        placeholder="Contoh: 70 gram atau Seberat buah plum">
                </div>

                <div class="col-md-6 mb-4">
                    <label for="ukuran_panjang" class="form-label">Ukuran Panjang Janin (Contoh: 10 cm) *</label>
                    <input type="text" class="form-control" id="ukuran_panjang" name="ukuran_panjang" required
                        value="<?php echo htmlspecialchars($post_data['ukuran_panjang'] ?? ''); ?>"
                        placeholder="Contoh: 10 cm atau Seukuran lemon">
                </div>
            </div>

            <div class="mb-4">
                <label for="deskripsi_janin" class="form-label">Deskripsi Perkembangan Janin *</label>
                <textarea class="form-control" id="deskripsi_janin" name="deskripsi_janin" rows="5" required
                    placeholder="Jelaskan perkembangan organ dan sistem utama janin pada minggu ini."><?php echo htmlspecialchars($post_data['deskripsi_janin'] ?? ''); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="tips" class="form-label">Tips untuk Ibu (Opsional)</label>
                <textarea class="form-control" id="tips" name="tips" rows="3"
                    placeholder="Saran aktivitas, nutrisi, atau kesehatan untuk ibu hamil."><?php echo htmlspecialchars($post_data['tips'] ?? ''); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="hal_dihindari" class="form-label">Hal yang Harus Dihindari (Opsional)</label>
                <textarea class="form-control" id="hal_dihindari" name="hal_dihindari" rows="3"
                    placeholder="Daftar makanan, aktivitas, atau kebiasaan buruk yang harus dihindari."><?php echo htmlspecialchars($post_data['hal_dihindari'] ?? ''); ?></textarea>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3">
                <button type="submit" class="btn btn-primary btn-lg align-items-center">
                    <i class="fas fa-save me-2"></i> Simpan Data Perkembangan
                </button>
            </div>

        </form>
    </div>
</div>

<?php include 'layout/footer_admin.php'; ?>