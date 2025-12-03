<?php

/**
 * File: layout/security_check.php
 * * Bertanggung jawab untuk:
 * 1. Memastikan sesi dimulai HANYA SEKALI.
 * 2. Melakukan pengecekan apakah pengguna adalah admin (authorization).
 * 3. Menghubungkan ke database (asumsi path db_connect sudah benar).
 */

// 1. Memulai Sesi (Mencegah Notice: session_start() sudah aktif)
// Periksa apakah sesi belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Pengecekan Keamanan (Authorization Check)
// Jika sesi 'role' tidak diset atau bukan 'admin', redirect ke halaman login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Sesuaikan path ke halaman login Anda
    header("Location: ../html/login.php");
    exit;
}

// 3. Include Koneksi Database
// Asumsi: File db_connect.php berada di '../config/' relatif terhadap file ini
// File db_connect.php harus memiliki variabel $pdo atau $conn
include '../config/db_connect.php';

// Variabel default untuk digunakan di header_admin.php (opsional, tapi disarankan)
$admin_name = $_SESSION['name'] ?? 'Admin';
$admin_email = $_SESSION['email'] ?? 'admin@bloombelly.com';
