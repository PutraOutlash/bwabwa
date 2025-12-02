<?php
// HEADER INI HET
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../config/db_connect.php';

$username = $_SESSION['username'] ?? 'Guest';
$current_page = basename($_SERVER['PHP_SELF']);

// LOGIKA REDIRECT
$public_pages = [
    'index.php',
    'artikel.php',
    'artikel_detail.php',
    'forum.php',
    'view_topic.php',
    'view_section.php',
    'download.php',
    'login.php',
    'register.php',
    'proses_login.php',
    'proses_register.php',
    'logout.php'
];

if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header('Location: login.php?pesan=harus_login');
    exit;
}

// --- KONFIGURASI JALUR DASAR (PENTING!) ---
// Jika nama folder proyek Anda di htdocs BUKAN 'bloombelly', ubah baris di bawah ini!
$BASE_URL = '/bloombelly';
?>
<!DOCTYPE html>
<html lang="id" id="html-root">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : "BloomBelly"; ?></title>

    <!-- SKRIP TEMA -->
    <script>
        (function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>

    <!-- LINK CSS UTAMA (MENGGUNAKAN $BASE_URL AGAR PASTI KETEMU) -->
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>/css/footer_style.css">

    <!-- Font & Icon -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS TAMBAHAN PER HALAMAN -->
    <?php
    if (isset($pageCSS) && is_array($pageCSS)) {
        foreach ($pageCSS as $cssFile) {
            // Bersihkan path agar kita bisa pakai $BASE_URL
            // Mengubah "../css/namafile.css" menjadi "/css/namafile.css"
            $clean_path = str_replace('../', '/', $cssFile);
            if (substr($clean_path, 0, 1) !== '/') {
                $clean_path = '/' . $clean_path;
            }

            echo '<link rel="stylesheet" href="' . $BASE_URL . $clean_path . '">';
        }
    }
    ?>

    <style>
        /* SMOOTH SCROLLING */
        html {
            scroll-behavior: smooth;
        }

        /* STYLE NAVBAR STANDAR (JAGA-JAGA JIKA CSS GAGAL TOTAL) */
        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-user-greeting {
            font-weight: 500;
            color: #555;
            font-size: 14px;
            margin-right: 5px;
        }

        .btn-profil,
        .btn-primary {
            display: inline-flex;
            align-items: center;
            background-color: #ff8fab;
            color: white !important;
            padding: 8px 16px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-profil:hover,
        .btn-primary:hover {
            background-color: #f77096;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 143, 171, 0.4);
        }

        .btn-secondary {
            padding: 8px 16px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            color: #555;
            background-color: #f0f0f0;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
            transform: translateY(-3px);
        }

        .theme-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.3em;
            color: #555;
            padding: 8px;
            margin-left: 5px;
            transition: all 0.4s ease;
        }

        .theme-btn:hover {
            color: #ff8fab;
            transform: rotate(30deg) scale(1.1);
        }

        /* ANIMASI LOGO & SCROLL */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes elasticDrop {
            0% {
                opacity: 0;
                transform: translateY(-60px) scale(0.8);
            }

            70% {
                opacity: 1;
                transform: translateY(10px) scale(1.05);
            }

            100% {
                transform: translateY(0) scale(1);
            }
        }

        .logo-entrance {
            animation: elasticDrop 1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .logo a {
            display: inline-block;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }

        .logo a:hover {
            color: #ff8fab;
            transform: scale(1.1) rotate(-3deg);
        }

        /* DARK MODE BASIC COMPATIBILITY */
        html.dark-mode body {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
        }

        html.dark-mode header {
            background-color: #1e1e1e !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            border-bottom: 1px solid #333;
        }

        html.dark-mode .logo a,
        html.dark-mode nav ul li a.active,
        html.dark-mode .theme-btn:hover {
            color: #ff8fab !important;
        }

        html.dark-mode nav ul li a {
            color: #ccc !important;
        }

        html.dark-mode .nav-user-greeting {
            color: #aaa !important;
        }

        html.dark-mode .theme-btn {
            color: #f1c40f;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="logo"><a href="index.php" id="logo-link">LOGO</a></div>
            <nav>
                <ul>
                    <li><a href="index.php" class="<?php if ($current_page == 'index.php') echo 'active'; ?>">Home</a></li>
                    <li><a href="artikel.php" class="<?php if ($current_page == 'artikel.php') echo 'active'; ?>">Artikel</a></li>
                    <li><a href="forum.php" class="<?php if ($current_page == 'forum.php') echo 'active'; ?>">Forum</a></li>
                    <li><a href="download.php" class="<?php if ($current_page == 'download.php') echo 'active'; ?>">Download</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="nav-user-greeting">Hi, <?php echo htmlspecialchars($username); ?></span>
                    <a href="profil.php" class="btn-profil"><i class="fas fa-user-circle"></i> Profil</a>
                    <a href="logout.php" class="btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-secondary">Login</a>
                <?php endif; ?>
                <button id="darkModeToggle" class="theme-btn" aria-label="Ubah Tema"><i id="theme-icon" class="fas fa-sun"></i></button>
            </div>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Animasi Logo
            if (window.location.pathname.endsWith('index.php') || window.location.pathname.endsWith('/')) {
                const logo = document.getElementById('logo-link');
                if (logo && !sessionStorage.getItem('logo_shown')) {
                    logo.classList.add('logo-entrance');
                    // sessionStorage.setItem('logo_shown', 'true'); 
                }
            }
            // Dark Mode
            const toggle = document.getElementById('darkModeToggle');
            const icon = document.getElementById('theme-icon');
            const html = document.documentElement;
            const updateIcon = () => icon.className = html.classList.contains('dark-mode') ? 'fas fa-moon' : 'fas fa-sun';
            updateIcon();
            toggle.addEventListener('click', () => {
                html.classList.toggle('dark-mode');
                localStorage.setItem('theme', html.classList.contains('dark-mode') ? 'dark' : 'light');
                updateIcon();
            });
            // Scroll Animation
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1
            });

            // SELECTOR LENGKAP UNTUK SEMUA HALAMAN
            const selectors = [
                '.animate-on-scroll',
                '.hero-section', '.calculator-section', '.faq-section', // Index
                '.artikel-header-section', '.article-card', '.sidebar-widget', // Artikel
                '.forum-header-section', '.forum-category-card', '.stats-card', // Forum
                '.section-header-controls', '.section-title-card', '.topic-item', // View Section
                '.create-topic-wrapper', // Create Topic
                '.download-hero', '.feature-card', '.final-cta-section', '.features-section', // Download
                '.profil-container' // Profil
            ];
            setTimeout(() => {
                document.querySelectorAll(selectors.join(', ')).forEach(el => {
                    el.classList.add('animate-on-scroll');
                    observer.observe(el);
                });
            }, 100);
        });
    </script>