<?php
// File: forum.php
// Menampilkan daftar topik forum untuk moderasi, menggunakan layout modular.

// 1. PANGGIL SECURITY CHECK (Menggantikan session_start(), cek role, dan include db)
include 'layout/security_check.php';

// 2. SET JUDUL HALAMAN
$pageTitle = "Management Forum";
$admin_email = $_SESSION['email'] ?? 'admin@bloombelly.com';

// Variabel untuk pesan sukses jika ada (setelah delete, dll)
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 'topic_deleted') {
    $success_message = 'Topik forum berhasil dihapus! 🗑';
}

// --- Query untuk Mengambil Daftar Topik Forum ---
$stmt = $pdo->query("
    SELECT 
        t.id AS topic_id, 
        t.title AS topic_title, 
        t.created_at,
        u.name AS starter_name,
        s.name AS section_name,
        (SELECT COUNT(p.id) FROM forum_posts p WHERE p.topic_id = t.id) AS post_count
    FROM forum_topics t
    JOIN users u ON t.user_id = u.id_users
    LEFT JOIN forum_sections s ON t.section_id = s.id
    ORDER BY t.created_at DESC
");
$topics = $stmt->fetchAll();
?>

<?php include 'layout/header_admin.php'; ?>

<style>
    /* Variabel Warna */
    :root {
        --color-primary-pink: #ff8fab;
        --color-info: #17a2b8;
        /* Biru/Cyan untuk highlight */
        --color-secondary: #6c757d;
        --color-danger: #dc3545;
        --table-header-bg: #ffffff;
        /* Header putih bersih */
    }

    /* --- Styling Tabel Paling Rapi (Card Table) --- */
    .table-container {
        background: #ffffff;
        border-radius: 15px;
        /* Sama dengan kartu lainnya */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        /* Shadow yang konsisten */
        overflow: hidden;
    }

    /* Header Card Tabel */
    .table-container .card-header-custom {
        padding: 1.5rem 1.5rem 1rem 1.5rem;
        /* Padding yang luas */
        background-color: #ffffff;
        border-bottom: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header-custom h4 {
        font-weight: 700;
        color: #343a40;
    }

    /* Header Kolom Tabel */
    .table-forum th {
        background-color: var(--table-header-bg);
        color: var(--color-secondary);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        border-bottom: 1px solid #e9ecef;
        /* Garis tipis */
        border-top: none !important;
        padding: 0.75rem 1.5rem;
        /* Padding lebih kompak */
        letter-spacing: 0.5px;
    }

    /* Sel Tabel */
    .table-forum td {
        vertical-align: middle;
        border-top: 1px solid #f1f1f1;
        /* Garis pemisah yang sangat halus */
        padding: 1.25rem 1.5rem;
        /* Padding baris diperbesar */
    }

    /* Hover Effect */
    .table-forum tbody tr:hover {
        background-color: #fcfcfc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        /* Shadow halus saat hover */
        cursor: pointer;
        /* Memberikan indikasi bahwa baris dapat diklik */
    }

    /* Menghapus garis pada tabel terakhir */
    .table-forum tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badge Section (Lebih menonjol) */
    .badge-section {
        background-color: #f0f4f8;
        /* Latar belakang abu-abu terang */
        color: var(--color-secondary);
        padding: 0.6em 1em;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.85rem;
    }

    /* Badge Post Count (Menggunakan warna info/cyan) */
    .badge-post-count {
        background-color: var(--color-info) !important;
        color: white;
        padding: 0.5em 0.8em;
        border-radius: 50px;
        font-weight: 600;
        min-width: 30px;
        display: inline-block;
        text-align: center;
    }

    /* Tombol Aksi */
    .btn-action {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 8px;
        transition: background-color 0.2s;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .btn-action i {
        font-size: 0.9rem;
    }

    .btn-view {
        background-color: var(--color-info);
        /* Menggunakan warna info (cyan) */
        border-color: var(--color-info);
    }

    .btn-view:hover {
        background-color: #148ea3;
        border-color: #148ea3;
    }
</style>

<?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="table-container">
    <div class="card-header-custom">
        <div>
            <h4 class="mb-0 fw-semibold">Daftar Topik Diskusi</h4>
            <small class="text-muted">Kelola dan moderasi diskusi yang dibuat oleh pengguna.</small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-forum align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col" style="width: 30%;">Topik</th>
                    <th scope="col" style="width: 15%;">Section</th>
                    <th scope="col" style="width: 15%;">Dibuat Oleh</th>
                    <th scope="col" style="width: 10%;">Total Post</th>
                    <th scope="col" style="width: 15%;">Tanggal Dibuat</th>
                    <th scope="col" style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topics as $topic): ?>
                    <tr>
                        <td onclick="window.location='forum_view_posts.php?id=<?php echo $topic['topic_id']; ?>'">
                            <strong class="text-dark"><?php echo htmlspecialchars($topic['topic_title']); ?></strong>
                        </td>
                        <td>
                            <span class="badge badge-section">
                                <?php echo htmlspecialchars($topic['section_name'] ?? 'Umum'); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($topic['starter_name']); ?></td>
                        <td>
                            <span class="badge badge-post-count">
                                <?php echo $topic['post_count']; ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y', strtotime($topic['created_at'])); ?></td>
                        <td>
                            <a href="forum_view_posts.php?id=<?php echo $topic['topic_id']; ?>"
                                class="btn btn-sm text-white btn-action btn-view"
                                title="Lihat dan Moderasi Postingan"
                                onclick="event.stopPropagation();">
                                <i class="fas fa-eye"></i>
                            </a>

                            <form method="POST" action="forum_delete.php" style="display:inline-block;" onclick="event.stopPropagation();">
                                <input type="hidden" name="topic_id" value="<?php echo $topic['topic_id']; ?>">
                                <button type="submit"
                                    class="btn btn-sm btn-danger btn-action"
                                    title="Hapus Topik dan Semua Post"
                                    onclick="return confirm('PERINGATAN: Menghapus topik akan menghapus SEMUA postingan di dalamnya. Lanjutkan?');">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($topics)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-light mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada topik forum yang dibuat oleh pengguna.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout/footer_admin.php'; ?>