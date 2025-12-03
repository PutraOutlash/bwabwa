<?php
// File: user_detail.php
// Menampilkan detail profil, riwayat kehamilan, dan diary pengguna.

// --- PANGGIL SECURITY CHECK dan DB CONNECT ---
include 'layout/security_check.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = null;
$pregnancies = [];
$diaries = [];
$error_message = '';

if ($user_id > 0) {
    try {
        // --- 1. Ambil Detail Dasar Pengguna ---
        $stmt_user = $pdo->prepare("SELECT id_users, name, email, username, phone, bio, created_at, avatar_url, role FROM users WHERE id_users = ?");
        $stmt_user->execute([$user_id]);
        $user = $stmt_user->fetch();

        if (!$user) {
            $error_message = "Pengguna tidak ditemukan.";
        } else {
            // --- 2. Ambil Riwayat Tracking Kehamilan ---
            $stmt_preg = $pdo->prepare("SELECT * FROM tracking_kehamilan WHERE id_users = ? ORDER BY dibuat_pada DESC");
            $stmt_preg->execute([$user_id]);
            $pregnancies = $stmt_preg->fetchAll();

            // --- 3. Ambil Riwayat Diary ---
            $stmt_diary = $pdo->prepare("SELECT id_diary, judul, dibuat_pada FROM diary WHERE id_users = ? ORDER BY dibuat_pada DESC");
            $stmt_diary->execute([$user_id]);
            $diaries = $stmt_diary->fetchAll();
        }
    } catch (\PDOException $e) {
        $error_message = "Gagal mengambil data: " . $e->getMessage();
    }
} else {
    $error_message = "ID Pengguna tidak valid.";
}

$pageTitle = $user ? "Detail User: " . htmlspecialchars($user['name']) : "Detail Pengguna";
?>

<?php include 'layout/header_admin.php'; ?>

<style>
    /* Variabel Warna */
    :root {
        --color-primary-pink: #ff8fab;
        --color-secondary: #6c757d;
        --color-info-cyan: #0dcaf0;
        --color-success: #28a745;
        --color-danger: #dc3545;
        --card-bg-light: #f7f9fc;
        --table-header-bg: #e9ecef;
    }

    /* --- General Styling --- */
    .detail-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: none;
    }

    /* --- MODIFIKASI: Header di dalam Card (Sama seperti halaman lain) --- */
    .card-header-custom {
        padding: 1.25rem 1.5rem;
        background-color: #f7f7f7;
        border-bottom: 1px solid #eee;
        border-radius: 15px 15px 0 0;
        display: flex;
        align-items: center;
        /* Hapus justify-content-between karena tombol kembali sudah dihilangkan */
    }

    .card-header-custom h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        color: #343a40;
    }

    /* Ikon Kembali (BARU) */
    .icon-back-link {
        font-size: 1.5rem;
        color: var(--color-secondary);
        margin-right: 15px;
        transition: color 0.2s;
    }

    .icon-back-link:hover {
        color: var(--color-primary-pink);
    }

    /* --- END MODIFIKASI HEADER --- */

    /* --- Profile Card Styling --- */
    .profile-section {
        background-color: var(--card-bg-light);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .profile-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid var(--color-primary-pink);
    }

    .profile-info h4 {
        font-weight: 700;
    }

    .info-table th {
        width: 30%;
        font-weight: 600;
        color: var(--color-secondary);
        padding: 0.5rem 0.75rem;
    }

    .info-table td {
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        color: #343a40;
    }

    /* --- Tab Styling (Menggunakan warna pink) --- */
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
        margin-top: 2rem;
    }

    .nav-tabs .nav-link {
        font-weight: 600;
        color: var(--color-secondary);
        transition: color 0.3s;
        border: none;
        padding: 0.75rem 1.25rem;
    }

    .nav-tabs .nav-link.active {
        color: var(--color-primary-pink);
        border-color: #dee2e6 #dee2e6 var(--color-primary-pink);
        border-width: 0 0 3px 0;
        background-color: transparent;
    }

    .nav-tabs .nav-link:hover:not(.active) {
        color: var(--color-primary-pink);
    }

    .tab-content-card {
        background-color: white;
        border-radius: 0 0 15px 15px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        border: 1px solid #eee;
    }

    /* --- Riwayat Kehamilan Table --- */
    .table-pregnancy th {
        background-color: var(--table-header-bg);
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #495057;
        font-weight: 700;
        padding: 0.75rem 1rem;
    }

    .table-pregnancy td {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }

    .badge-status-pil {
        padding: 0.5em 1em;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.7rem;
    }

    /* --- Diary List Styling --- */
    .list-group-flush .list-group-item {
        border-color: #f1f1f1;
    }
</style>

<?php if ($error_message): ?>
    <div class="alert alert-danger fade show mb-4"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php if ($user): ?>

    <div class="card detail-card mb-5">
        <div class="card-header-custom">
            <a href="users.php" title="Kembali ke Daftar Pengguna" class="icon-back-link">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="flex-grow-1"><i class="fas fa-id-badge me-2" style="color: var(--color-primary-pink);"></i> Detail Pengguna: <?php echo htmlspecialchars($user['name']); ?></h2>
        </div>

        <div class="card-body">
            <div class="row profile-section">

                <div class="col-md-3 text-center border-end">
                    <?php
                    // Path avatar disesuaikan agar selalu aman
                    $avatar_path = (empty($user['avatar_url']) || $user['avatar_url'] == 'placeholder.jpg')
                        ? '../images/default-avatar.png'
                        : '../' . ltrim($user['avatar_url'], './');
                    ?>
                    <img src="<?php echo htmlspecialchars($avatar_path); ?>" alt="Avatar" class="profile-img">

                    <h4 class="mt-3 mb-1 text-dark profile-info"><?php echo htmlspecialchars($user['name']); ?></h4>
                    <p class="text-secondary mb-3">@<?php echo htmlspecialchars($user['username']); ?>
                        <span class="badge bg-dark ms-1"><?php echo ucfirst($user['role']); ?></span>
                    </p>

                    <?php if ($user['role'] == 'user'): ?>
                        <a href="user_action.php?id=<?php echo $user['id_users']; ?>&action=promote"
                            class="btn btn-sm btn-success d-flex align-items-center justify-content-center mx-auto mb-2"
                            style="width: 80%; border-radius: 8px;"
                            onclick="return confirm('Yakin ingin mempromosikan pengguna ini menjadi Admin?');">
                            <i class="fas fa-user-shield me-1"></i> Promosikan
                        </a>
                    <?php endif; ?>
                </div>

                <div class="col-md-9 ps-4">
                    <h5 class="mb-4 fw-bold" style="color: var(--color-primary-pink);"><i class="fas fa-info-circle me-2"></i> Informasi Kontak & Akun</h5>
                    <table class="table table-sm info-table">
                        <tr>
                            <th><i class="fas fa-fingerprint me-2"></i> ID User</th>
                            <td><?php echo $user['id_users']; ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-envelope me-2"></i> Email</th>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-phone me-2"></i> Telepon</th>
                            <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th><i class="far fa-calendar-alt me-2"></i> Bergabung Sejak</th>
                            <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-quote-left me-2"></i> Bio</th>
                            <td><?php echo htmlspecialchars($user['bio'] ?? '-'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pregnancy-tab" data-bs-toggle="tab" data-bs-target="#pregnancy" type="button" role="tab" aria-controls="pregnancy" aria-selected="true">
                <i class="fas fa-baby me-1"></i> Riwayat Kehamilan (<span class="fw-bold"><?php echo count($pregnancies); ?></span>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="diary-tab" data-bs-toggle="tab" data-bs-target="#diary" type="button" role="tab" aria-controls="diary" aria-selected="false">
                <i class="fas fa-book me-1"></i> Diary (<span class="fw-bold"><?php echo count($diaries); ?></span>)
            </button>
        </li>
    </ul>

    <div class="tab-content tab-content-card" id="myTabContent">

        <div class="tab-pane fade show active" id="pregnancy" role="tabpanel" aria-labelledby="pregnancy-tab">
            <h5 class="mb-4 text-secondary"><i class="fas fa-history me-2"></i> Riwayat Tracking Kehamilan</h5>
            <div class="table-responsive">
                <table class="table table-striped table-sm table-pregnancy mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-heartbeat me-1"></i> Status</th>
                            <th>HPHT</th>
                            <th>HPL</th>
                            <th>Minggu Ke-</th>
                            <th>Tanggal Kejadian</th>
                            <th>Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pregnancies as $p): ?>
                            <tr>
                                <td>
                                    <?php
                                    $status = ucfirst($p['status']);
                                    $badge_class = 'secondary';
                                    $icon = 'fas fa-question';
                                    if ($p['status'] == 'hamil') {
                                        $badge_class = 'info';
                                        $icon = 'fas fa-stethoscope';
                                    } elseif ($p['status'] == 'lahir') {
                                        $badge_class = 'success';
                                        $icon = 'fas fa-baby';
                                    } elseif ($p['status'] == 'keguguran') {
                                        $badge_class = 'danger';
                                        $icon = 'fas fa-times-circle';
                                    }
                                    ?>
                                    <span class="badge badge-status-pil bg-<?php echo $badge_class; ?>">
                                        <i class="<?php echo $icon; ?>"></i> <?php echo $status; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($p['hpht'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($p['hpl'])); ?></td>
                                <td><span class="badge bg-primary"><?php echo $p['minggu_kehamilan'] ?? '-'; ?></span></td>
                                <td>
                                    <?php if ($p['status'] == 'lahir'): ?>
                                        <small class="fw-bold text-success">Lahir:</small> <?php echo date('d M Y', strtotime($p['tanggal_lahir'])); ?>
                                    <?php elseif ($p['status'] == 'keguguran'): ?>
                                        <small class="fw-bold text-danger">Keguguran:</small> <?php echo date('d M Y', strtotime($p['tanggal_keguguran'])); ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <i class="fas fa-<?php echo $p['aktif'] ? 'check text-success' : 'times text-danger'; ?>"></i>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($pregnancies)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-clipboard-list fa-2x text-light mb-2"></i>
                                    <p class="mb-0">Belum ada riwayat kehamilan yang dicatat.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="diary" role="tabpanel" aria-labelledby="diary-tab">
            <h5 class="mb-4 text-secondary"><i class="fas fa-book-open me-2"></i> Daftar Judul Diary</h5>
            <ul class="list-group list-group-flush">
                <?php foreach ($diaries as $d): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-pen-alt me-2" style="color: var(--color-primary-pink);"></i> <?php echo htmlspecialchars($d['judul']); ?></span>
                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d M Y', strtotime($d['dibuat_pada'])); ?></small>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($diaries)): ?>
                    <li class="list-group-item text-center text-muted py-4">
                        <i class="fas fa-book-open fa-2x text-light mb-2"></i>
                        <p class="mb-0">Pengguna ini belum memiliki entri diary.</p>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

<?php endif; ?>

<?php include 'layout/footer_admin.php'; ?>