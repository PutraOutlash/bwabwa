<?php
// IKI HALAMAN UTAMA RAYA AMANN
$pageTitle = "BloomBelly - Teman Setia Kehamilan Bunda";
// Kita gunakan CSS khusus agar gaya halaman ini tidak mengganggu yang lain
$pageCSS = ["../css/home-style.css"];
try {
    include_once '../config/db_connect.php';
} catch (Exception $e) {
}
include 'header.php';
?>

<main class="home-page">

    <section class="hero-section animate-on-scroll">
        <div class="container hero-content">
            <div class="hero-text">
                <?php
                // Logika BARU: Tampilkan nama user jika login, atau string kosong jika Guest.
                // Diasumsikan variabel $username ada dari header.php (isinya bisa 'Guest' atau nama user)
                $isLoggedIn = (isset($username) && $username !== '' && $username !== 'Guest');

                // Jika sudah login, tambahkan koma dan namanya dengan highlight. Jika tidak, string kosong.
                $greetingSuffix = $isLoggedIn ? ', <span class="highlight">' . htmlspecialchars($username) . '</span>' : '';
                ?>
                <h1>Halo Bunda<?php echo $greetingSuffix; ?>!</h1>
                <p class="hero-subtitle">
                    Selamat datang di BloomBelly. Kami siap menemani setiap langkah indah perjalanan kehamilan Bunda dengan informasi terpercaya dan komunitas yang suportif.
                </p>
                <div class="hero-actions">
                    <a href="#calculator" class="btn-hero-primary">
                        <i class="fas fa-calculator"></i> Hitung HPL
                    </a>
                    <a href="forum.php" class="btn-hero-secondary">
                        Gabung Komunitas <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="hero-illustration">
                <img src="../images/bunda-bahagia.png" alt="Ilustrasi Bunda Bahagia">
            </div>
        </div>
        <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </section>

    <section id="calculator" class="calculator-section">
        <div class="container">
            <div class="calculator-card animate-on-scroll">
                <div class="calc-header">
                    <div class="icon-wrapper"><i class="fas fa-baby-carriage"></i></div>
                    <h2>Kalkulator HPL</h2>
                    <p>Perkirakan kapan si kecil akan lahir ke dunia.</p>
                </div>

                <div class="calc-body">
                    <form id="hplForm" class="calc-form">
                        <div class="form-group">
                            <label>Hari Pertama Haid Terakhir (HPHT)</label>
                            <div class="input-icon-wrap">
                                <i class="far fa-calendar-alt"></i>
                                <input type="date" id="hpht" class="form-input" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-calculate">Hitung Sekarang</button>
                    </form>

                    <div id="calcResult" class="calc-result" style="display: none;">
                        <h4>Perkiraan Lahir:</h4>
                        <div class="result-date" id="hplDate">-</div>
                        <p class="disclaimer">*Hasil ini hanya perkiraan. Selalu konsultasikan dengan dokter atau bidan.</p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="faq-section animate-on-scroll">
        <div class="container">
            <div class="section-title">
                <h2>Pertanyaan Populer</h2>
                <p>Temukan jawaban cepat seputar BloomBelly di sini.</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Apa saja fitur utama BloomBelly?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>BloomBelly menyediakan Kalkulator Kehamilan, Artikel Kesehatan terpercaya dari para ahli, Forum Diskusi untuk berbagi pengalaman dengan Bunda lain, dan fitur pelacak perkembangan janin.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Apakah aplikasi ini berbayar?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Saat ini seluruh fitur dasar BloomBelly dapat diakses secara GRATIS untuk membantu sebanyak mungkin ibu hamil di Indonesia.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Bagaimana cara bergabung di Forum?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Sangat mudah! Anda hanya perlu membuat akun dan login. Setelah itu, Anda bebas membuat topik baru atau membalas diskusi di menu Forum.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
    // Tidak ada lagi variabel isLoggedIn karena kalkulator kini bebas diakses

    document.addEventListener('DOMContentLoaded', () => {
        // --- LOGIKA KALKULATOR ---
        const hplForm = document.getElementById('hplForm');
        const calcResult = document.getElementById('calcResult');
        const hplDateElem = document.getElementById('hplDate');
        // loginPrompt tidak lagi digunakan

        if (hplForm) {
            hplForm.addEventListener('submit', (e) => {
                e.preventDefault();

                // Sembunyikan pesan hasil sebelumnya
                calcResult.style.display = 'none';

                // JALANKAN PERHITUNGAN UNTUK SEMUA ORANG
                const hpht = new Date(document.getElementById('hpht').value);

                if (isNaN(hpht.getTime())) return;

                // Rumus Naegele (diasumsikan standar 28 hari): HPHT + 7 hari - 3 bulan + 1 tahun
                let hpl = new Date(hpht);
                hpl.setDate(hpl.getDate() + 7);
                hpl.setMonth(hpl.getMonth() - 3);
                hpl.setFullYear(hpl.getFullYear() + 1);

                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                hplDateElem.textContent = hpl.toLocaleDateString('id-ID', options);

                calcResult.style.display = 'block';
                calcResult.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            });
        }

        // --- LOGIKA FAQ ACCORDION ---
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            item.querySelector('.faq-question').addEventListener('click', () => {
                // Tutup yang lain
                faqItems.forEach(i => {
                    if (i !== item) i.classList.remove('active');
                });
                // Toggle yang diklik
                item.classList.toggle('active');
            });
        });
    });
</script>

<?php include 'footer.php'; ?>