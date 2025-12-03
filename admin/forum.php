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
    $success_message = 'Topik forum berhasil dihapus! 🗑️';
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

// --- Query untuk Mengambil Daftar Kategori (Sections) ---
$sections_stmt = $pdo->query("
    SELECT 
        s.*,
        COUNT(t.id) as topic_count
    FROM forum_sections s
    LEFT JOIN forum_topics t ON s.id = t.section_id
    GROUP BY s.id
    ORDER BY s.order_position ASC, s.name ASC
");
$sections = $sections_stmt->fetchAll();
?>

<?php include 'layout/header_admin.php'; ?>

<style>
    /* Variabel Warna */
    :root {
        --color-primary-pink: #ff8fab;
        --color-info: #17a2b8;
        --color-secondary: #6c757d;
        --color-danger: #dc3545;
        --color-success: #28a745;
        --color-warning: #ffc107;
        --table-header-bg: #ffffff;
    }

    /* Tab Navigation */
    .nav-tabs-forum {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 1.5rem;
    }
    
    .nav-tabs-forum .nav-link {
        border: none;
        color: var(--color-secondary);
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        margin-right: 0.5rem;
        border-radius: 8px 8px 0 0;
        transition: all 0.3s;
    }
    
    .nav-tabs-forum .nav-link:hover {
        color: var(--color-primary-pink);
        background-color: rgba(255, 143, 171, 0.1);
    }
    
    .nav-tabs-forum .nav-link.active {
        color: var(--color-primary-pink);
        background-color: #fff;
        border-bottom: 2px solid var(--color-primary-pink);
        font-weight: 600;
    }

    /* --- Styling Container --- */
    .table-container {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    /* Header Card */
    .table-container .card-header-custom {
        padding: 1.5rem 1.5rem 1rem 1.5rem;
        background-color: #ffffff;
        border-bottom: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .card-header-custom h4 {
        font-weight: 700;
        color: #343a40;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    /* Tombol Tambah */
    .btn-add-section {
        background: linear-gradient(135deg, var(--color-primary-pink), #ff6b9d);
        border: none;
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-add-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 143, 171, 0.3);
        color: white;
    }

    /* Tabel Styles */
    .table-forum th {
        background-color: var(--table-header-bg);
        color: var(--color-secondary);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        border-bottom: 1px solid #e9ecef;
        border-top: none !important;
        padding: 0.75rem 1.5rem;
        letter-spacing: 0.5px;
    }

    .table-forum td {
        vertical-align: middle;
        border-top: 1px solid #f1f1f1;
        padding: 1.25rem 1.5rem;
    }

    .table-forum tbody tr:hover {
        background-color: #fcfcfc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        cursor: pointer;
    }

    .table-forum tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badge Styles */
    .badge-section {
        background-color: #f0f4f8;
        color: var(--color-secondary);
        padding: 0.6em 1em;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .badge-topic-count {
        background-color: var(--color-info) !important;
        color: white;
        padding: 0.5em 0.8em;
        border-radius: 50px;
        font-weight: 600;
        min-width: 30px;
        display: inline-block;
        text-align: center;
    }

    .badge-post-count {
        background-color: var(--color-primary-pink) !important;
        color: white;
        padding: 0.5em 0.8em;
        border-radius: 50px;
        font-weight: 600;
        min-width: 30px;
        display: inline-block;
        text-align: center;
    }

    /* Status Badge */
    .badge-status {
        padding: 0.4em 0.8em;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-active {
        background-color: rgba(40, 167, 69, 0.1);
        color: var(--color-success);
        border: 1px solid var(--color-success);
    }
    
    .badge-inactive {
        background-color: rgba(108, 117, 125, 0.1);
        color: var(--color-secondary);
        border: 1px solid var(--color-secondary);
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
        transition: all 0.2s;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        margin-right: 0.25rem;
    }

    .btn-view {
        background-color: var(--color-info);
        border-color: var(--color-info);
        color: white;
    }

    .btn-view:hover {
        background-color: #148ea3;
        border-color: #148ea3;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-edit {
        background-color: var(--color-warning);
        border-color: var(--color-warning);
        color: #212529;
    }
    
    .btn-edit:hover {
        background-color: #e0a800;
        border-color: #e0a800;
        color: #212529;
        transform: translateY(-2px);
    }
    
    .btn-delete {
        background-color: var(--color-danger);
        border-color: var(--color-danger);
        color: white;
    }
    
    .btn-delete:hover {
        background-color: #c82333;
        border-color: #c82333;
        color: white;
        transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .card-header-custom {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .header-actions {
            width: 100%;
            justify-content: flex-start;
        }
        
        .nav-tabs-forum .nav-link {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
    }
</style>

<!-- Tab Navigation -->
<ul class="nav nav-tabs nav-tabs-forum" id="forumTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="topics-tab" data-bs-toggle="tab" data-bs-target="#topics" type="button" role="tab">
            <i class="fas fa-comments me-2"></i>Topik Diskusi
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="sections-tab" data-bs-toggle="tab" data-bs-target="#sections" type="button" role="tab">
            <i class="fas fa-tags me-2"></i>Kategori Forum
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="forumTabsContent">
    
    <!-- Tab 1: Topik Diskusi -->
    <div class="tab-pane fade show active" id="topics" role="tabpanel">
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
                            <th scope="col" style="width: 15%;">Kategori</th>
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
                                        class="btn btn-action btn-view"
                                        title="Lihat dan Moderasi Postingan"
                                        onclick="event.stopPropagation();">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <form method="POST" action="forum_delete.php" style="display:inline-block;" onclick="event.stopPropagation();">
                                        <input type="hidden" name="topic_id" value="<?php echo $topic['topic_id']; ?>">
                                        <button type="submit"
                                            class="btn btn-action btn-delete"
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
    </div>

    <!-- Tab 2: Kategori Forum -->
    <div class="tab-pane fade" id="sections" role="tabpanel">
        <div class="table-container">
            <div class="card-header-custom">
                <div>
                    <h4 class="mb-0 fw-semibold">Daftar Kategori Forum</h4>
                    <small class="text-muted">Kelola kategori untuk mengorganisir topik diskusi.</small>
                </div>
                <div class="header-actions">
                    <button type="button" class="btn btn-add-section" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                        <i class="fas fa-plus"></i> Tambah Kategori
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-forum align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 25%;">Nama Kategori</th>
                            <th scope="col" style="width: 30%;">Deskripsi</th>
                            <th scope="col" style="width: 10%;">Jumlah Topik</th>
                            <th scope="col" style="width: 10%;">Status</th>
                            <th scope="col" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sections as $section): ?>
                            <tr>
                                <td><?php echo $section['order_position'] ?? '-'; ?></td>
                                <td>
                                    <strong class="text-dark"><?php echo htmlspecialchars($section['name']); ?></strong>
                                    <?php if (!empty($section['color'])): ?>
                                        <span class="badge ms-2" style="background-color: <?php echo $section['color']; ?>; color: white;">•</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($section['description'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge badge-topic-count">
                                        <?php echo $section['topic_count']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-status <?php echo ($section['is_active'] == 1) ? 'badge-active' : 'badge-inactive'; ?>">
                                        <?php echo ($section['is_active'] == 1) ? 'Aktif' : 'Nonaktif'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-action btn-edit edit-section-btn"
                                        data-section-id="<?php echo $section['id']; ?>"
                                        data-section-name="<?php echo htmlspecialchars($section['name']); ?>"
                                        data-section-desc="<?php echo htmlspecialchars($section['description'] ?? ''); ?>"
                                        data-section-color="<?php echo $section['color'] ?? ''; ?>"
                                        data-section-order="<?php echo $section['order_position'] ?? ''; ?>"
                                        data-section-active="<?php echo $section['is_active']; ?>"
                                        title="Edit Kategori">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <a href="forum_view_section.php?id=<?php echo $section['id']; ?>"
                                        class="btn btn-action btn-view"
                                        title="Lihat Topik di Kategori Ini">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <form method="POST" action="forum_section_delete.php" style="display:inline-block;" 
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                        <input type="hidden" name="section_id" value="<?php echo $section['id']; ?>">
                                        <button type="submit"
                                            class="btn btn-action btn-delete"
                                            title="Hapus Kategori"
                                            <?php echo ($section['topic_count'] > 0) ? 'disabled' : ''; ?>>
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($sections)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-tags fa-3x text-light mb-3"></i>
                                    <p class="mb-0 text-muted">Belum ada kategori forum. Klik "Tambah Kategori" untuk membuat yang pertama.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="forum_section_add.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSectionModalLabel">Tambah Kategori Forum Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="section-form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="section_name" class="form-label">Nama Kategori *</label>
                                <input type="text" class="form-control" id="section_name" name="name" required 
                                    placeholder="Misal: Kesehatan, Resep, Tips">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="section_color" class="form-label">Warna Kategori</label>
                                <input type="color" class="form-control form-control-color" id="section_color" name="color" 
                                    value="#ff8fab" title="Pilih warna untuk kategori">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="section_description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="section_description" name="description" rows="3" 
                                placeholder="Deskripsi singkat tentang kategori ini"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="section_order" class="form-label">Urutan Tampilan</label>
                                <input type="number" class="form-control" id="section_order" name="order_position" 
                                    min="1" max="100" value="1" placeholder="1">
                                <small class="form-text text-muted">Angka kecil akan ditampilkan lebih dulu</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="section_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="section_active">
                                        Kategori Aktif
                                    </label>
                                </div>
                                <small class="form-text text-muted">Nonaktifkan untuk menyembunyikan kategori dari pengguna</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-labelledby="editSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="forum_section_edit.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSectionModalLabel">Edit Kategori Forum</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="section-form">
                        <input type="hidden" id="edit_section_id" name="section_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_section_name" class="form-label">Nama Kategori *</label>
                                <input type="text" class="form-control" id="edit_section_name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_section_color" class="form-label">Warna Kategori</label>
                                <input type="color" class="form-control form-control-color" id="edit_section_color" name="color">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_section_description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_section_description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_section_order" class="form-label">Urutan Tampilan</label>
                                <input type="number" class="form-control" id="edit_section_order" name="order_position" min="1" max="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="edit_section_active" name="is_active" value="1">
                                    <label class="form-check-label" for="edit_section_active">
                                        Kategori Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script untuk modal edit kategori
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-section-btn');
    const editModal = new bootstrap.Modal(document.getElementById('editSectionModal'));
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const sectionId = this.getAttribute('data-section-id');
            const sectionName = this.getAttribute('data-section-name');
            const sectionDesc = this.getAttribute('data-section-desc');
            const sectionColor = this.getAttribute('data-section-color') || '#ff8fab';
            const sectionOrder = this.getAttribute('data-section-order') || '1';
            const sectionActive = this.getAttribute('data-section-active');
            
            document.getElementById('edit_section_id').value = sectionId;
            document.getElementById('edit_section_name').value = sectionName;
            document.getElementById('edit_section_description').value = sectionDesc;
            document.getElementById('edit_section_color').value = sectionColor;
            document.getElementById('edit_section_order').value = sectionOrder;
            document.getElementById('edit_section_active').checked = (sectionActive === '1');
            
            editModal.show();
        });
    });
    
    // Aktifkan tab yang dipilih sebelumnya (jika ada di URL)
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam === 'sections') {
        const sectionsTab = document.getElementById('sections-tab');
        if (sectionsTab) {
            sectionsTab.click();
        }
    }
    
    // Simpan tab aktif ke URL saat berpindah tab
    const forumTabs = document.querySelectorAll('#forumTabs button[data-bs-toggle="tab"]');
    forumTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(event) {
            const activeTab = event.target.getAttribute('data-bs-target').replace('#', '');
            const url = new URL(window.location);
            url.searchParams.set('tab', activeTab);
            window.history.replaceState({}, '', url);
        });
    });
});
</script>

<?php include 'layout/footer_admin.php'; ?>