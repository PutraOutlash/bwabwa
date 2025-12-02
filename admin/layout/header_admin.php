<?php
// File: layout/header_admin.php

// Pastikan variabel $pageTitle diatur oleh file yang memanggil
$pageTitle = $pageTitle ?? "Dashboard Admin";

// Mulai session jika belum dimulai (Diperlukan untuk $_SESSION['email'] dan $_SESSION['username'])
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tentukan nama tampilan (prioritaskan username, fallback ke email)
$adminDisplayName = htmlspecialchars($_SESSION['username'] ?? 'Admin Bloombelly');

// Tentukan email untuk tooltip atau info (fallback ke default)
$adminEmail = htmlspecialchars($_SESSION['email'] ?? 'admin@bloombelly.com');

// Ambil URL Avatar dari sesi. Jika tidak ada, gunakan string kosong.
$avatarUrl = htmlspecialchars($_SESSION['avatar_url'] ?? '');

// Anda mungkin perlu menyesuaikan base URL jika avatar_url hanya menyimpan path relatif (misalnya: /uploads/avatars/...)
// Contoh menyesuaikan path:
// $baseAvatarPath = 'http://localhost/bloombelly/'; // Sesuaikan dengan base URL Anda
// $fullAvatarUrl = $avatarUrl ? $baseAvatarPath . $avatarUrl : '';
// Untuk saat ini kita asumsikan $avatarUrl sudah relatif/absolut yang benar.
$fullAvatarUrl = $avatarUrl;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - BloomBelly Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        /* Desain Bersih, Elegan, dan Proporsional (Fixed Overflow) */
        :root {
            --primary-pink: #ff8fab;
            --primary-light: #fff0f5;
            /* Background sangat terang (Active BG) */
            --primary-dark: #dc3545;
            --sidebar-bg: #ffffff;
            --sidebar-link-color: #343a40;
            --main-bg: #f5f5f5;
            --text-dark: #343a40;
            /* NILAI BARU: Memperluas lebar sidebar menjadi 280px */
            --sidebar-width: 280px;

            --clean-shadow: 0 1px 10px rgba(0, 0, 0, 0.04);
            --transition-speed: 0.25s;
        }

        body {
            background-color: var(--main-bg);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* 1. KONTEN UTAMA: Flexbox */
        .page-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* --- 2. Sidebar Styling --- */
        .sidebar {
            background-color: var(--sidebar-bg);
            color: var(--sidebar-link-color);
            width: var(--sidebar-width);
            /* Menggunakan lebar 280px */
            flex-shrink: 0;
            box-shadow: 1px 0 5px rgba(0, 0, 0, 0.05);
            border-right: 1px solid #f0f0f0;
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 2.5rem 0 1.5rem 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar .admin-title {
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--primary-pink);
            padding: 0 1.5rem 1.5rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #eee;
            margin-bottom: 1.5rem;
        }

        /* Navigasi Container */
        .sidebar .nav {
            padding: 0;
        }

        /* Navigasi Link (Capsule/Pill Estetik) */
        .sidebar .nav-link {
            color: var(--sidebar-link-color);
            padding: 0.8rem 1.5rem;
            /* MODIFIKASI: Margin Horizontal diperbesar agar lebih berjarak di lebar 280px */
            margin: 0.3rem 1rem;
            border-radius: 8px;
            transition: all var(--transition-speed) ease;
            white-space: nowrap;
            font-weight: 500;
            border: 1px solid transparent;
        }

        /* Animasi Hover (Halus) */
        .sidebar .nav-link:hover:not(.active) {
            background-color: #fcfcfc;
            color: var(--primary-pink);
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border: 1px solid #ffedf1;
        }

        /* Link Aktif (Proporsional) */
        .sidebar .nav-link.active {
            color: var(--text-dark);
            background-color: var(--primary-light);
            font-weight: 600;
            border: 1px solid var(--primary-pink);
            box-shadow: 0 0 10px rgba(255, 143, 171, 0.1);
            transform: none;
            border-left: 1px solid var(--primary-pink);
        }

        /* Icon pada Link Aktif */
        .sidebar .nav-link.active i {
            color: var(--primary-pink);
        }

        /* Icon default */
        .sidebar .nav-link i {
            font-size: 1.1em;
            width: 30px;
            text-align: center;
            transition: color var(--transition-speed);
            color: #6c757d;
        }

        /* Saat hover, icon default berubah warna */
        .sidebar .nav-link:hover i:not(.active) {
            color: var(--primary-pink);
        }


        /* Logout Link Styling */
        .sidebar .logout-link {
            color: var(--primary-dark) !important;
            margin-top: auto;
            border-top: 1px solid #eee;
            padding: 1.5rem 1.5rem 0.5rem 1.5rem;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
        }

        .sidebar .logout-link:hover {
            background-color: #ffe8ec;
            border-radius: 0;
        }


        /* --- 3. Main Content Styling (Padding standar) --- */
        .main-content {
            flex-grow: 1;
            padding: 1rem 2.5rem 2.5rem 2.5rem;
        }

        /* Perubahan Header Konten (Padding standar) */
        .header-section {
            background-color: #ffffff;
            padding: 1.75rem 2.5rem;
            border-radius: 12px;
            box-shadow: var(--clean-shadow);
            margin-bottom: 2rem;
            border: none;
            transition: box-shadow var(--transition-speed) ease;
        }

        /* Animasi Hover */
        .header-section:hover {
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        }

        .header-section h2 {
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        /* === 4. Styling Admin Profile (BARU) === */

        .admin-profile-info {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 20px;
            transition: background-color var(--transition-speed);
        }

        .admin-profile-info:hover {
            background-color: #f7f7f7;
        }

        .admin-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 1rem;
        }

        /* Style untuk Placeholder Ikon */
        .admin-avatar i {
            font-size: 1.8rem;
            color: var(--primary-pink);
            vertical-align: middle;
        }

        /* Style untuk Foto Avatar dari Database */
        .admin-avatar img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-pink);
            vertical-align: middle;
        }

        /* Menyembunyikan span text-muted lama yang berisi email */
        .header-section .text-muted {
            display: none;
        }
    </style>
</head>

<body>

    <div class="page-wrapper">

        <div class="sidebar">
            <h4 class="admin-title">BloomBelly Admin</h4>
            <nav class="nav nav-pills flex-column">
                <?php
                // Logika untuk menentukan link aktif
                $currentPage = basename($_SERVER['PHP_SELF']);
                ?>

                <a class="nav-link <?php echo ($currentPage == 'dashboard.php' ? 'active' : ''); ?>" href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>

                <a class="nav-link <?php echo ($currentPage == 'articles.php' || $currentPage == 'article_create.php' || $currentPage == 'article_edit.php' ? 'active' : ''); ?>" href="articles.php"><i class="fas fa-newspaper me-2"></i> Articles Management</a>

                <a class="nav-link <?php echo ($currentPage == 'forum.php' || $currentPage == 'forum_view_posts.php' ? 'active' : ''); ?>" href="forum.php"><i class="fas fa-comments me-2"></i> Management Forum</a>

                <a class="nav-link <?php echo ($currentPage == 'users.php' || $currentPage == 'user_detail.php' ? 'active' : ''); ?>" href="users.php"><i class="fas fa-users me-2"></i> Users Management</a>

                <a class="nav-link <?php echo ($currentPage == 'data_management.php' || $currentPage == 'nutrisi_create.php' || $currentPage == 'nutrisi_edit.php' ? 'active' : ''); ?>" href="data_management.php"><i class="fas fa-database me-2"></i> Management Data</a>

            </nav>
            <a class="mt-auto nav-link logout-link" href="../html/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
        </div>

        <div class="main-content">
            <div class="header-section d-flex justify-content-between align-items-center">
                <h2><?php echo htmlspecialchars($pageTitle); ?></h2>

                <div class="admin-profile-info" title="Login sebagai: <?php echo $adminEmail; ?>">
                    <span class="admin-name me-3 d-none d-sm-inline"><?php echo $adminDisplayName; ?></span>
                    <span class="admin-avatar">
                        <?php if (!empty($fullAvatarUrl)): ?>
                            <img src="<?php echo $fullAvatarUrl; ?>" alt="Admin Avatar">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                    </span>
                </div>
            </div>