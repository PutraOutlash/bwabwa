<?php
//FORM LOGIN DOANG
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// 1. Definisikan variabel untuk header
$pageTitle = "Login - BloomBelly";
// DIUBAH: Ditambahkan '../' untuk mundur satu folder
// 'css/footer_style.css' -> '../css/footer_style.css'
// 'css/login-style.css' -> '../css/login-style.css'
$pageCSS = ['../css/footer_style.css', '../css/login-style.css'];

// 2. Panggil "Kepala" Halaman
// Kita tidak panggil header.php karena halaman ini punya logika khusus
// (Kita akan buat mini-header)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>

    <!-- Link Font Awesome (Untuk ikon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Memuat semua CSS yang didefinisikan di $pageCSS -->
    <?php
    if (isset($pageCSS) && is_array($pageCSS)) {
        foreach ($pageCSS as $css_file) {
            echo '<link rel="stylesheet" href="' . htmlspecialchars($css_file) . '">';
        }
    }
    ?>
    <!-- Font Google -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>

<body>

    <!-- Mengambil struktur HTML dari login.html lama Anda -->
    <div class="login-container">
        <div class="login-left">
            <div class="login-header">
                BloomBelly
            </div>
            <div class="login-intro">
                <!-- Ditambah data-key -->
                <h1 data-key="hero_title">SUPPORT POWERED <br> BY <span>MOTHERS</span> <br> AROUND THE WORLD.</h1>
                <p class="subtitle" data-key="hero_subtitle">sahabat perjalanan kehamilan anda</p>

                <!-- 
                  DIHAPUS: Blok <div class="create-account">...</div> 
                  yang ada di kiri bawah sudah dihapus sesuai permintaan.
                -->

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

                <!-- PENTING: action="login_process.php" -->
                <form action="login_process.php" method="POST">
                    <h2 data-key="login_title">Login to your account</h2>

                    <?php
                    // Tampilkan pesan error jika login gagal
                    // Ini dikirim dari login_process.php
                    if (isset($_GET['error'])):
                    ?>
                        <div class="login-error-message" style="color: #D8000C; background-color: #FFD2D2; padding: 12px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #D8000C;">
                            Username atau password salah. Silakan coba lagi.
                        </div>
                    <?php endif; ?>

                    <label for="username" data-key="login_username">Username</label>
                    <input type="text" id="username" name="username" required>

                    <label for="password" data-key="login_password">Password</label>
                    <input type="password" id="password" name="password" required>

                    <a href="#" class="forgot-password" data-key="login_forgot">lupa Password?</a>
                    <button type="submit" class="btn-login" data-key="login_button">Login</button>

                    <!-- Link "Belum punya akun?" di bawah form login TETAP ADA -->
                    <div class="create-account-link" style="text-align: center; margin-top: 20px;">
                        <p data-key="login_no_account">Belum punya akun? <a href="register.php" data-key="login_create_account">buat akun &rarr;</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 
      Path app.js ini sudah benar (asumsi app.js ada di folder 'html'
      bersama dengan 'login.php')
    -->
    <script src="app.js"></script>
</body>

</html>