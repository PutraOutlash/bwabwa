<?php
// File: db.php
// DIUBAH: Dikonversi ke PDO agar bisa dipakai oleh login_process.php, header.php, dll.

// --- Pengaturan Database Anda ---
$host = '127.0.0.1'; // atau 'localhost'
$db   = 'bloombelly_db'; // Nama database Anda
$user = 'root'; // User default XAMPP
$pass = ''; // Password default XAMPP (kosong)
$charset = 'utf8mb4';
// ---------------------------------

// Opsi untuk koneksi PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Tampilkan error sebagai exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Kembalikan data sebagai array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false, // Gunakan native prepared statements
];

// Buat DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // Buat koneksi PDO dan simpan di variabel $pdo
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Jika gagal, tampilkan error
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// PENTING: session_start() sengaja dihapus dari file ini
// untuk mencegah error 'headers already sent'.
// Kita sudah memanggilnya di file-file lain (header.php, login_process.php, dll).
