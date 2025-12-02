<?php
// File: users.php
// Menampilkan daftar pengguna dengan status kehamilan terakhir.

// 1. PANGGIL SECURITY CHECK (Menggantikan session_start(), cek role, dan include db)
include 'layout/security_check.php';

// 2. SET JUDUL HALAMAN
$pageTitle = "Users Management";

// Variabel untuk pesan sukses jika ada (setelah delete/update role)
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 'role_updated') {
    $success_message = 'Peran pengguna berhasil diubah! ✨';
} elseif (isset($_GET['success']) && $_GET['success'] == 'action_performed') {
    $success_message = 'Aksi berhasil dijalankan. 👍';
} elseif (isset($_GET['error']) && $_GET['error'] == 'block_placeholder') {
    // Menampilkan pesan placeholder untuk aksi blokir
    $success_message = 'Simulasi: Pengguna ini sudah diblokir.';
}


// --- Query untuk Mengambil Daftar Pengguna (role='user' atau 'blocked') ---
$stmt = $pdo->query("
    SELECT 
        u.id_users, 
        u.name, 
        u.email, 
        u.username,
        u.created_at,
        u.role,
        t.status AS pregnancy_status,
        t.minggu_kehamilan,
        t.hpl
    FROM users u
    -- Ambil status kehamilan yang aktif (t.aktif = 1)
    LEFT JOIN tracking_kehamilan t ON u.id_users = t.id_users AND t.aktif = 1 
    WHERE u.role = 'user' OR u.role = 'blocked' 
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();
?>

<?php include 'layout/header_admin.php'; ?>

<style>
    /* Variabel Warna */
    :root {
        --color-primary-pink: #ff8fab;
        --color-info-cyan: #17a2b8;
        --color-success: #28a745;
        --color-warning: #ffc107;
        --color-danger: #dc3545;
        --color-muted: #6c757d;
        --table-header-bg: #f8f9fa;
    }

    /* --- Data Grid Container (Card Table Style) --- */
    .data-grid-container {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    /* Header Kolom Tabel */
    .table-users th {
        background-color: var(--table-header-bg);
        color: var(--color-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        border-bottom: 1px solid #e9ecef;
        border-top: none !important;
        /* PENTING: Padding horizontal dikurangi */
        padding: 0.75rem 1rem;
        letter-spacing: 0.5px;
    }

    /* Sel Tabel */
    .table-users td {
        vertical-align: middle;
        border-top: 1px solid #f1f1f1;
        /* PENTING: Padding horizontal dikurangi */
        padding: 1rem 1rem;
        font-size: 0.9rem;
    }

    /* Hover Effect */
    .table-users tbody tr:hover {
        background-color: #fcfcfc;
        box-shadow: inset 3px 0 0 var(--color-primary-pink);
        cursor: default;
    }

    /* Style untuk Baris Diblokir */
    .row-blocked {
        background-color: #ffeef2 !important;
        opacity: 0.9;
    }

    .row-blocked:hover {
        box-shadow: inset 3px 0 0 var(--color-danger);
    }

    .table-users tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badge Status Kehamilan (Pil Penuh dengan Ikon) */
    .badge-status {
        padding: 0.6em 1em;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        text-transform: uppercase;
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
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Detail Teks */
    .user-detail-info strong {
        font-weight: 700;
        color: #343a40;
    }

    .user-detail-info small {
        color: var(--color-muted);
        display: block;
        margin-top: 3px;
        font-size: 0.8rem;
    }
</style>

<?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="data-grid-container">
    <div class="table-responsive">
        <table class="table table-users align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col" style="width: 5%;">#</th>
                    <th scope="col" style="width: 25%;">Nama / Username</th>
                    <th scope="col" style="width: 20%;">Email</th>
                    <th scope="col" style="width: 15%;">Status Kehamilan</th>
                    <th scope="col" style="width: 15%;">Tracking Info</th>
                    <th scope="col" style="width: 10%;">Bergabung</th>
                    <th scope="col" style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($users as $user):
                    $is_blocked = ($user['role'] == 'blocked');
                    $user_row_class = $is_blocked ? 'row-blocked' : '';
                ?>
                    <tr class="<?php echo $user_row_class; ?>">
                        <td><?php echo $no++; ?></td>
                        <td class="user-detail-info">
                            <strong class="text-dark"><?php echo htmlspecialchars($user['name']); ?></strong>
                            <small>@<?php echo htmlspecialchars($user['username']); ?></small>
                            <?php if ($is_blocked): ?>
                                <span class="badge bg-danger ms-2 fw-bold" style="font-size: 0.75rem;"><i class="fas fa-ban me-1"></i> BLOCKED</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php
                            $status = $user['pregnancy_status'] ?? 'Tidak Terdaftar';
                            $badge_color = 'secondary';
                            $icon = 'fas fa-user';

                            if ($status == 'hamil') {
                                $badge_color = 'info';
                                $icon = 'fas fa-heartbeat';
                            } else if ($status == 'lahir') {
                                $badge_color = 'success';
                                $icon = 'fas fa-baby';
                            } else if ($status == 'keguguran') {
                                $badge_color = 'danger';
                                $icon = 'fas fa-exclamation-triangle';
                            }
                            ?>
                            <span class="badge badge-status bg-<?php echo $badge_color; ?>">
                                <i class="<?php echo $icon; ?> me-1"></i> <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['hpl']): ?>
                                <small class="d-block text-dark fw-bold"><i class="fas fa-calendar-alt me-1"></i> HPL: <?php echo date('d M Y', strtotime($user['hpl'])); ?></small>
                                <small class="d-block text-muted"><i class="fas fa-calendar-check me-1"></i> Minggu ke-<?php echo $user['minggu_kehamilan'] ?? '?'; ?></small>
                            <?php else: ?>
                                <span class="text-muted small">- Tidak Ada Data -</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted"><i class="fas fa-user-plus me-1"></i> <?php echo date('d M Y', strtotime($user['created_at'])); ?></small>
                        </td>
                        <td>
                            <a href="user_detail.php?id=<?php echo $user['id_users']; ?>"
                                class="btn btn-sm btn-info text-white btn-action me-2"
                                title="Lihat Detail Profil & Tracking">
                                <i class="fas fa-user-circle"></i>
                            </a>

                            <?php if ($is_blocked): ?>
                                <a href="user_action.php?id=<?php echo $user['id_users']; ?>&action=unblock"
                                    class="btn btn-sm btn-success btn-action"
                                    title="Unblock Pengguna"
                                    onclick="return confirm('Apakah Anda yakin ingin mengaktifkan kembali akun pengguna ini?');">
                                    <i class="fas fa-unlock"></i>
                                </a>
                            <?php else: ?>
                                <a href="user_action.php?id=<?php echo $user['id_users']; ?>&action=block"
                                    class="btn btn-sm btn-danger btn-action"
                                    title="Blokir Pengguna"
                                    onclick="return confirm('Apakah Anda yakin ingin memblokir pengguna ini? Ini akan menonaktifkan akunnya.');">
                                    <i class="fas fa-lock"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-users-slash fa-3x text-light mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada pengguna terdaftar (role='user').</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout/footer_admin.php'; ?>