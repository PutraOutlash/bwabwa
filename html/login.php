<?php
// FORM LOGIN DOANG
session_start();

// Cek jika user sudah login, arahkan ke index.php
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// 1. Definisikan variabel untuk header
$pageTitle = "Login - BloomBelly";
// Path CSS disesuaikan (asumsi '../' adalah path yang benar)
$pageCSS = ['../css/footer_style.css', '../css/login-style.css'];

// 2. Panggil "Kepala" Halaman
// (Kita tidak panggil header.php karena halaman ini punya logika khusus)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <?php
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

                <div class="login-illustration-container">
                    <img src="../images/komunitas_ibu_bayi.png" alt="Komunitas Ibu dan Bayi BloomBelly">
                </div>

            </div>
        </div>

        <div class="login-right">
            <div class="login-form-box">

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
                    <button type="submit" class="btn-login" data-key="login_button">Login</button>


                </form>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>

</html>