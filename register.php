<?php
// IKI PISAN OJO DIHAPUS

session_start();

// Jika pengguna sudah login, langsung lempar ke index.php
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// 1. Definisikan variabel
$pageTitle = "Register - BloomBelly";
// Kita panggil CSS yang sama dengan login.php
$pageCSS = ['../css/footer_style.css', '../css/login-style.css'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <?php
    // Memuat semua CSS
    if (isset($pageCSS) && is_array($pageCSS)) {
        foreach ($pageCSS as $css_file) {
            echo '<link rel="stylesheet" href="' . htmlspecialchars($css_file) . '">';
        }
    }
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>

<body>

    <div class="login-container">
        <div class="login-left">
            <div class="login-header">
                BloomBelly
            </div>
            <div class="login-intro">
                <h1 data-key="hero_title">SUPPORT POWERED <br> BY <span>MOTHERS</span> <br> AROUND THE WORLD.</h1>
                <p class="subtitle" data-key="hero_subtitle">sahabat perjalanan kehamilan anda</p>

            </div>
            <div class="about-us-card">
                <img src="images/Gemini_Generated_Image_rz4ishrz4ishrz4i 1.png" alt="About us background" onerror="this.src='https://placehold.co/300x150/FFF0F5/FF69B4?text=BloomBelly'">
                <div class="about-us-text">
                    <h3 data-key="about_us_title">About us</h3>
                    <p data-key="about_us_desc">"Guiding your journey with expert knowledge, curated products, and a warm community of
                        mothers..."</p>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-form-box">

                <form action="register_process.php" method="POST">

                    <h2 data-key="register_title">Buat Akun Baru</h2>

                    <?php
                    // Tampilkan pesan error jika registrasi gagal
                    if (isset($_GET['error'])):
                        $error_msg = 'Terjadi kesalahan. Silakan coba lagi.';
                        if ($_GET['error'] == 'empty') {
                            $error_msg = 'Semua field wajib diisi.';
                        } else if ($_GET['error'] == 'passwordmismatch') {
                            $error_msg = 'Konfirmasi password tidak cocok.';
                        } else if ($_GET['error'] == 'invalidemail') {
                            $error_msg = 'Format email tidak valid.';
                        } else if ($_GET['error'] == 'userexists') {
                            $error_msg = 'Username atau email sudah terdaftar.';
                        }
                    ?>
                        <div class="login-error-message" style="color: #D8000C; background-color: #FFD2D2; padding: 12px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #D8000C;">
                            <?php echo $error_msg; ?>
                        </div>
                    <?php endif; ?>

                    <label for="username" data-key="login_username">Username</label>
                    <input type="text" id="username" name="username" required>

                    <label for="full_name" data-key="register_fullname">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" required>

                    <label for="email" data-key="register_email">Email</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password" data-key="login_password">Password</label>
                    <input type="password" id="password" name="password" required>

                    <label for="confirm_password" data-key="register_confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>


                    <button type="submit" class="btn-login">Konfirmasi</button>
                    <div class="create-account-link" style="text-align: center; margin-top: 20px;">
                        <p>Sudah punya akun? <a href="login.php">halaman login &rarr;</a></p>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>

</html>