<?php
// File: db.php
// Konfigurasi koneksi database menggunakan PDO

// --- Pengaturan Database Anda ---
$host    = '127.0.0.1'; // atau 'localhost'
$db      = 'bloombelly'; // Nama database Anda (Sesuai phpMyAdmin)
$user    = 'root'; // User default XAMPP
$pass    = ''; // Password default XAMPP (kosong)
$charset = 'utf8mb4';   
// ---------------------------------

// Opsi untuk koneksi PDO
$options = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION, // Tampilkan error sebagai exceptions
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, // Kembalikan data sebagai array asosiatif
    \PDO::ATTR_EMULATE_PREPARES   => false, // Nonaktifkan emulasi prepared statements
];

// Buat DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // Buat koneksi PDO dan simpan di variabel $pdo
    $pdo = new \PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Tampilkan detail error database (Hanya untuk debugging)
    die("<h1>Gagal terhubung ke database.</h1><p>Detail Error: " . $e->getMessage() . "</p>");
}

// Variabel $pdo sekarang berisi objek koneksi database yang siap digunakan.
