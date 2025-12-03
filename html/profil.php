<?php
// PROFILEE
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ===============================================

// 1. Setup Halaman & Keamanan
$pageTitle = "Profil Saya - BloomBelly";
$pageCSS = ["../css/profil-style.css"];
include 'header.php';

// 2. Koneksi Database
// Menggunakan require_once 'db.php'; karena file ini di folder yang sama
require_once '../config/db_connect.php';

// 3. Keamanan & Variabel
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$pesan_sukses = "";
$pesan_error = "";

// -- [LOGIKA UPDATE PROFIL DIHAPUS SESUAI REQUEST] --

// 5. LOGIKA UPLOAD AVATAR (TETAP ADA)
if (isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar_image']) && $_FILES['avatar_image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB

        $file_tmp = $_FILES['avatar_image']['tmp_name'];
        $file_type = mime_content_type($file_tmp);
        $file_size = $_FILES['avatar_image']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $pesan_error = "Hanya file JPG, PNG, atau GIF yang diperbolehkan.";
        } elseif ($file_size > $max_size) {
            $pesan_error = "Ukuran file terlalu besar (Maksimal 2MB).";
        } else {
            $upload_dir = '../uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = pathinfo($_FILES['avatar_image']['name'], PATHINFO_EXTENSION);
            $new_file_name = "avatar_" . $user_id . "_" . time() . "." . $file_ext;
            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                $db_avatar_path = '../uploads/avatars/' . $new_file_name;
                try {
                    // KOREKSI 1: Mengubah WHERE id = ? menjadi WHERE id_users = ?
                    $stmt = $pdo->prepare("UPDATE users SET avatar_url = ? WHERE id_users = ?");
                    $stmt->execute([$db_avatar_path, $user_id]);
                    $pesan_sukses = "Foto profil berhasil diperbarui!";
                } catch (Exception $e) {
                    $pesan_error = "Gagal menyimpan ke database. Detail: " . $e->getMessage();
                    unlink($destination);
                }
            } else {
                $pesan_error = "Gagal mengunggah file.";
            }
        }
    } else {
        $pesan_error = "Silakan pilih file gambar terlebih dahulu.";
    }
}

// 6. AMBIL DATA USER
$user = [];
$avatar_display = 'path/to/default_avatar.png';

// KOREKSI 2: Mengubah SELECT id menjadi SELECT id_users (dan WHERE id menjadi id_users)
$stmt = $pdo->prepare("
    SELECT id_users, username, email, created_at, avatar_url
    FROM users 
    WHERE id_users = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Error: Data user tidak ditemukan. Silakan login kembali.";
    // Mungkin perlu ditambahkan logout di sini
    exit;
}

// Cek avatar
// $user['avatar_url'] seharusnya berisi path avatar
if (!empty($user['avatar_url']) && file_exists($user['avatar_url'])) {
    $avatar_display = $user['avatar_url'];
} elseif (!empty($user['avatar_url'])) {
    // Jika path ada di DB tapi file tidak ada di server, tetap tampilkan path
    $avatar_display = $user['avatar_url'];
}
?>

<main class="profil-page">
    <div class="container">

        <?php if ($pesan_sukses): ?>
            <div class="alert alert-success animate-on-scroll"><i class="fas fa-check-circle"></i> <?php echo $pesan_sukses; ?></div>
        <?php endif; ?>
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger animate-on-scroll"><i class="fas fa-exclamation-circle"></i> <?php echo $pesan_error; ?></div>
        <?php endif; ?>

        <div class="profil-card animate-on-scroll">
            <div class="profil-banner"></div>

            <div class="profil-content-wrapper">
                <div class="profil-sidebar">
                    <div class="avatar-wrapper">
                        <img src="<?php echo htmlspecialchars($avatar_display); ?>" alt="Foto Profil" class="avatar-img">
                    </div>

                    <h2 class="user-name-display">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </h2>
                    <p class="user-role-display">Member BloomBelly</p>

                    <div class="upload-section">
                        <h3>Ganti Foto Profil</h3>
                        <form action="profil.php" method="POST" enctype="multipart/form-data" class="upload-form">
                            <label for="avatar_image" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i> Pilih Foto
                            </label>
                            <input type="file" name="avatar_image" id="avatar_image" accept="image/*" required hidden>
                            <span id="file-chosen">Belum ada file dipilih</span>
                            <button type="submit" name="upload_avatar" class="btn-upload-save">
                                Simpan Foto Baru
                            </button>
                        </form>
                        <small class="upload-hint">Maks. 2MB (JPG, PNG, GIF)</small>
                    </div>
                </div>

                <div class="profil-info">
                    <div class="info-header">
                        <h3><i class="fas fa-user-cog"></i> Informasi Akun</h3>
                    </div>

                    <div class="profile-info-form">
                        <div class="info-list">

                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-user"></i> Username</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="far fa-calendar-alt"></i> Bergabung Sejak</div>
                                <div class="info-value">
                                    <?php echo date('d F Y', strtotime($user['created_at'])); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
    const actualBtn = document.getElementById('avatar_image');
    const fileChosen = document.getElementById('file-chosen');
    if (actualBtn) {
        actualBtn.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileChosen.textContent = this.files[0].name;
            } else {
                fileChosen.textContent = 'Belum ada file dipilih';
            }
        })
    }
</script>

<?php include 'footer.php'; ?>