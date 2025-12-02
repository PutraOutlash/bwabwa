<?php
/**
 * File: db_connect.php
 * Deskripsi: Skrip PHP untuk membuat koneksi ke database 'bloombelly' menggunakan PDO.
 */

// --- Konfigurasi Database ---
$host = 'localhost'; // Server database
$db   = 'bloombelly'; // Nama database (Sesuai dengan nama yang Anda impor)
$user = 'root';      // Username database (default XAMPP)
$pass = '';          // Password database (default XAMPP adalah kosong)
$charset = 'utf8mb4';

// Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    // Aktifkan mode error untuk exceptions, mempermudah debugging
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Mengambil data sebagai associative array (contoh: $row['name'])
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Nonaktifkan emulasi prepared statement (memastikan keamanan)
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     // Membuat objek koneksi PDO
     $pdo = new PDO($dsn, $user, $pass, $options);
     // echo "Koneksi berhasil!"; // Hanya untuk pengujian, hapus ini setelah berhasil
} catch (\PDOException $e) {
     // Jika koneksi gagal, tampilkan pesan error
     // Error ini tidak boleh ditampilkan ke user biasa, hanya untuk developer.
     die("Koneksi Database Gagal: " . $e->getMessage());
}

?>