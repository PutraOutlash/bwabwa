<?php
// IKI HALAMAN DOWNLOAD PHP JIRR
$pageTitle = "Download Aplikasi - BloomBelly";
// Panggil CSS khusus untuk halaman download
$pageCSS = ["../css/download-style.css"];
include 'header.php';
?>

<main class="download-page">

    <section class="download-hero animate-on-scroll" id="download-hero">
        <div class="container hero-content">
            <div class="hero-text-side">
                <h1>Dapatkan Aplikasi <br><span class="highlight-pink">BloomBelly</span> Sekarang</h1>
                <p class="hero-subtitle">
                    Pantau kehamilan Bunda dengan lebih mudah dan praktis langsung dari genggaman. Unduh aplikasi resmi kami secara langsung di sini.
                </p>

                <div class="download-action-area">
                    <a href="files/bloombelly.apk" class="btn-download-direct" download>
                        <div class="btn-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <div class="btn-text">
                            <span class="main-text">Download Aplikasi</span>
                            <span class="sub-text">Gratis untuk Android (.apk)</span>
                        </div>
                    </a>
                    <p class="compatibility-note"><i class="fas fa-check-circle"></i> Kompatibel dengan Android 8.0+</p>
                </div>
            </div>

            <div class="hero-image-side">
                <img src="../images/app-mockup.png" alt="Tampilan Aplikasi BloomBelly" class="app-mockup-img">
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <h2>Fitur Unggulan Aplikasi</h2>
                <p>Semua yang Bunda butuhkan dalam satu aplikasi.</p>
            </div>

            <div class="features-grid">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon"><i class="fas fa-calculator"></i></div>
                    <h3>Kalkulator Kehamilan</h3>
                    <p>Hitung perkiraan HPL dengan akurat dan pantau usia kehamilan minggu demi minggu.</p>
                </div>

                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon"><i class="fas fa-baby"></i></div>
                    <h3>Pelacak Perkembangan</h3>
                    <p>Lihat ilustrasi ukuran bayi Anda setiap minggu dan ketahui perkembangan organnya.</p>
                </div>

                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3>Komunitas Bunda</h3>
                    <p>Jangan merasa sendirian. Bertanya, berbagi cerita, dan saling menguatkan dengan Bunda lainnya.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="final-cta-section animate-on-scroll">
        <div class="container cta-box">
            <h2>Siap Memulai Perjalanan yang Lebih Tenang?</h2>
            <p>Unduh BloomBelly sekarang, GRATIS untuk semua Bunda.</p>
            <a href="#download-hero" class="btn-primary-large">
                <i class="fas fa-arrow-up"></i> Download Sekarang
            </a>
        </div>
    </section>

</main>

<?php include 'footer.php'; ?>