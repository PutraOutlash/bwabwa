<?php
// File: data_management.php
// Menampilkan daftar Nutrisi Ibu Hamil.

// --- PANGGIL SECURITY CHECK dan DB CONNECT ---
include 'layout/security_check.php';

$pageTitle = "Management Data";

$search_term = $_GET['search'] ?? '';
$minggu_filter = $_GET['minggu'] ?? '';
$success_message = '';

// Tentukan tab aktif dari URL, default ke 'nutrisi'
$active_tab = $_GET['tab'] ?? 'nutrisi';

// Handling Success Messages
if (isset($_GET['success']) && $_GET['success'] == 'nutrisi_added') {
    $success_message = 'Data Nutrisi berhasil ditambahkan. 🥗';
} elseif (isset($_GET['success']) && $_GET['success'] == 'nutrisi_updated') {
    $success_message = 'Data Nutrisi berhasil diperbarui. ✨';
} elseif (isset($_GET['success']) && $_GET['success'] == 'nutrisi_deleted') {
    $success_message = 'Data Nutrisi berhasil dihapus. 🗑️';
}
// Tambahkan handling success message untuk Perkembangan
elseif (isset($_GET['success']) && $_GET['success'] == 'perkembangan_added') {
    $success_message = 'Data Perkembangan Janin berhasil ditambahkan. 👶';
}
// Anda perlu menambahkan logika untuk 'perkembangan_updated' dan 'perkembangan_deleted' di file delete/edit masing-masing.


// --- Query untuk Mengambil Data Nutrisi ---
$where_clauses = [];
$params = [];

// Filter Pencarian (Contoh: mencari berdasarkan kandungan nutrisi)
if (!empty($search_term)) {
    $where_clauses[] = "(kandungan_nutrisi LIKE ? OR makanan_rekomendasi LIKE ?)";
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
}

// Filter Minggu
if (!empty($minggu_filter)) {
    $where_clauses[] = "minggu_ke = ?";
    $params[] = $minggu_filter;
}

$sql = "SELECT id_nutrisi, minggu_ke, kandungan_nutrisi, makanan_rekomendasi, manfaat, makanan_dihindari FROM nutrisi";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY minggu_ke ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$nutrisi_list = $stmt->fetchAll();

// Ambil semua minggu yang unik untuk dropdown filter
$all_minggu = $pdo->query("SELECT DISTINCT minggu_ke FROM nutrisi ORDER BY minggu_ke ASC")->fetchAll(PDO::FETCH_COLUMN);

// --- Query untuk Perkembangan Janin (diperlukan untuk konten tab) ---
try {
    // === PERBAIKAN 1: NAMA TABEL HARUS 'perkembangan' ===
    // === PERBAIKAN 2: NAMA KOLOM HARUS 'ukuran_bayi' & 'perkembangan_terbaru' ===
    // Catatan: Karena `ukuran_bayi` diisi gabungan Berat dan Panjang di file create, kita tampilkan saja satu kolom ini.
    // Jika Anda butuh Berat dan Panjang terpisah di tabel ini, struktur DB harus diubah.
    $sql_perkembangan = "SELECT id_perkembangan, minggu_ke, ukuran_bayi, perkembangan_terbaru, tips, hal_dihindari FROM perkembangan ORDER BY minggu_ke ASC";
    $stmt_perkembangan = $pdo->query($sql_perkembangan);
    $perkembangan_list = $stmt_perkembangan->fetchAll();
} catch (\PDOException $e) {
    // Jika tabel belum ada (misalnya), berikan array kosong
    $perkembangan_list = [];
}

?>

<?php include 'layout/header_admin.php'; ?>

<style>
    /* Variabel Warna & Font */
    :root {
        --color-primary-pink: #ff8fab;
        /* Pink Theme */
        --color-secondary: #6c757d;
        --color-info-cyan: #0dcaf0;
        /* Cyan untuk highlight Perkembangan */
        --color-success: #28a745;
        --color-danger: #dc3545;
        --table-header-bg: #f8f9fa;
        /* Header terang */
    }

    /* --- General Styling --- */
    .data-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
    }

    /* --- Tab Styling (Fokus di Sini) --- */
    .data-nav-list .nav-link {
        font-weight: 600;
        color: var(--color-secondary);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px 10px 0 0;
        transition: all 0.2s;
        margin-right: 0.5rem;
        background-color: #e9ecef;
    }

    .data-nav-list .nav-link.active {
        color: white;
        background-color: var(--color-primary-pink);
        /* Warna Aktif Pink */
        box-shadow: 0 3px 10px rgba(255, 143, 171, 0.3);
    }

    .data-nav-list .nav-link:hover {
        color: #343a40;
    }

    .tab-content {
        margin-top: 0;
        padding: 1.5rem;
        background-color: white;
        border-radius: 0 15px 15px 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .tab-content h5 {
        font-weight: 700;
        color: var(--color-secondary);
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }


    /* --- Filter & Search --- */
    .filter-group .form-control,
    .filter-group .form-select {
        border-radius: 8px;
        padding: 0.65rem 1rem;
    }

    .filter-group .input-group-text {
        border-radius: 8px 0 0 8px;
        background-color: #f8f9fa;
        color: var(--color-secondary);
    }

    .btn-info {
        background-color: var(--color-info-cyan);
        border-color: var(--color-info-cyan);
    }

    .btn-primary {
        background-color: var(--color-primary-pink);
        border-color: var(--color-primary-pink);
        font-weight: 600;
        border-radius: 8px;
    }


    /* --- Table Styling --- */
    .table-data th {
        background-color: var(--table-header-bg);
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 0.75rem 1rem;
        /* Padding lebih kompak */
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e9ecef;
        border-top: none !important;
    }

    .table-data td {
        vertical-align: middle;
        padding: 1rem 1rem;
        font-size: 0.9rem;
        border-top: 1px solid #f1f1f1;
        white-space: normal;
        /* Izinkan wrap pada konten panjang */
    }

    .table-data tbody tr:hover {
        background-color: #fcfcfc;
        box-shadow: inset 3px 0 0 var(--color-primary-pink);
    }

    .table-data tbody tr:last-child td {
        border-bottom: none;
    }


    /* Badge Minggu (Nutrisi) */
    .badge-minggu {
        background-color: var(--color-primary-pink) !important;
        color: white;
        padding: 0.5em 0.8em;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
    }

    /* Badge Minggu (Perkembangan) */
    .badge-minggu-info {
        background-color: var(--color-info-cyan) !important;
        color: white;
        padding: 0.5em 0.8em;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
    }

    /* Tombol Aksi */
    .btn-action {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        /* Tombol bulat */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>

<ul class="nav data-nav-list mb-0" id="dataTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_tab == 'nutrisi') ? 'active' : ''; ?>"
            id="nutrisi-tab"
            href="?tab=nutrisi"
            role="tab"
            aria-controls="nutrisi"
            aria-selected="<?php echo ($active_tab == 'nutrisi') ? 'true' : 'false'; ?>">
            <i class="fas fa-utensils me-2"></i> Nutrisi Ibu Hamil
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_tab == 'perkembangan') ? 'active' : ''; ?>"
            id="perkembangan-tab"
            href="?tab=perkembangan"
            role="tab"
            aria-controls="perkembangan"
            aria-selected="<?php echo ($active_tab == 'perkembangan') ? 'true' : 'false'; ?>">
            <i class="fas fa-chart-line me-2"></i> Perkembangan Janin
        </a>
    </li>
</ul>

<div class="tab-content" id="dataTabContent">

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="tab-pane fade <?php echo ($active_tab == 'nutrisi') ? 'show active' : ''; ?>" id="nutrisi" role="tabpanel" aria-labelledby="nutrisi-tab">
        <h5 class="fw-bold text-secondary mb-4"><i class="fas fa-list me-2"></i> Daftar Kebutuhan Nutrisi Mingguan</h5>

        <form method="GET" action="data_management.php" class="row g-3 align-items-center mb-5 filter-group">
            <input type="hidden" name="tab" value="nutrisi">
            <div class="col-lg-4 col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari kandungan, makanan..." value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
                <select name="minggu" class="form-select">
                    <option value="">Semua Minggu</option>
                    <?php foreach ($all_minggu as $minggu): ?>
                        <option value="<?php echo $minggu; ?>" <?php echo ($minggu == $minggu_filter) ? 'selected' : ''; ?>>
                            Minggu Ke-<?php echo $minggu; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-5 col-md-5 text-end">
                <button type="submit" class="btn btn-info text-white me-3 d-inline-flex align-items-center">
                    <i class="fas fa-filter me-2"></i> Filter
                </button>
                <a href="nutrisi_create.php" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="fas fa-plus me-2"></i> Tambah Nutrisi
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-data align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 8%;">Minggu</th>
                        <th style="width: 20%;">Kandungan Nutrisi</th>
                        <th style="width: 20%;">Makanan Rekomendasi</th>
                        <th style="width: 20%;">Manfaat</th>
                        <th style="width: 17%;">Makanan Dihindari</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nutrisi_list as $nutrisi): ?>
                        <tr>
                            <td><span class="badge badge-minggu"><?php echo $nutrisi['minggu_ke']; ?></span></td>
                            <td><?php echo nl2br(htmlspecialchars(substr($nutrisi['kandungan_nutrisi'], 0, 60) . (strlen($nutrisi['kandungan_nutrisi']) > 60 ? '...' : ''))); ?></td>
                            <td><?php echo nl2br(htmlspecialchars(substr($nutrisi['makanan_rekomendasi'], 0, 60) . (strlen($nutrisi['makanan_rekomendasi']) > 60 ? '...' : ''))); ?></td>
                            <td><?php echo nl2br(htmlspecialchars(substr($nutrisi['manfaat'], 0, 60) . (strlen($nutrisi['manfaat']) > 60 ? '...' : ''))); ?></td>
                            <td><small class="text-danger"><?php echo nl2br(htmlspecialchars(substr($nutrisi['makanan_dihindari'], 0, 60) . (strlen($nutrisi['makanan_dihindari']) > 60 ? '...' : ''))); ?></small></td>
                            <td>
                                <a href="nutrisi_edit.php?id=<?php echo $nutrisi['id_nutrisi']; ?>" class="btn btn-sm btn-info text-white btn-action me-2" title="Edit Data">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form method="POST" action="nutrisi_delete.php" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $nutrisi['id_nutrisi']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger btn-action" title="Hapus Data" onclick="return confirm('Hapus data Nutrisi Minggu <?php echo $nutrisi['minggu_ke']; ?>?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($nutrisi_list)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-sad-cry fa-2x text-muted mb-2"></i>
                                <p class="mb-0 text-muted">Tidak ada data nutrisi yang ditemukan untuk filter ini.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-pane fade <?php echo ($active_tab == 'perkembangan') ? 'show active' : ''; ?>" id="perkembangan" role="tabpanel" aria-labelledby="perkembangan-tab">
        <h5 class="fw-bold text-secondary mb-4"><i class="fas fa-chart-bar me-2"></i> Daftar Data Perkembangan Janin Mingguan</h5>

        <div class="text-end mb-5">
            <a href="perkembangan_create.php" class="btn btn-primary d-inline-flex align-items-center">
                <i class="fas fa-plus me-2"></i> Tambah Data Perkembangan
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-data align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 10%;">Minggu</th>
                        <th style="width: 25%;">Ukuran Bayi (Berat & Panjang)</th>
                        <th style="width: 35%;">Deskripsi Perkembangan</th>
                        <th style="width: 15%;">Tips Ibu</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($perkembangan_list as $data): ?>
                        <tr>
                            <td>
                                <span class="badge badge-minggu-info"><?php echo $data['minggu_ke']; ?></span>
                            </td>
                            <td>
                                <i class="fas fa-ruler-vertical me-1 text-secondary"></i>
                                <?php echo htmlspecialchars($data['ukuran_bayi']); ?>
                            </td>
                            <td>
                                <?php echo nl2br(htmlspecialchars(substr($data['perkembangan_terbaru'], 0, 90) . (strlen($data['perkembangan_terbaru']) > 90 ? '...' : ''))); ?>
                            </td>
                            <td>
                                <small class="text-success"><?php echo nl2br(htmlspecialchars(substr($data['tips'], 0, 60) . (strlen($data['tips']) > 60 ? '...' : ''))); ?></small>
                            </td>
                            <td>
                                <a href="perkembangan_edit.php?id=<?php echo $data['id_perkembangan']; ?>"
                                    class="btn btn-sm btn-info text-white btn-action me-2"
                                    title="Edit Data">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form method="POST" action="perkembangan_delete.php" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $data['id_perkembangan']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger btn-action" title="Hapus Data" onclick="return confirm('Hapus data Perkembangan Minggu <?php echo $data['minggu_ke']; ?>?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($perkembangan_list)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-baby-carriage fa-2x text-muted mb-2"></i>
                                <p class="mb-0 text-muted">Belum ada data perkembangan janin yang tersedia.</p>
                                <a href="perkembangan_create.php" class="btn btn-outline-primary btn-sm mt-2"><i class="fas fa-plus me-1"></i> Tambah Data Perkembangan</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'layout/footer_admin.php'; ?>