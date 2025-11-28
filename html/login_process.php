<?php
// File: login_process.php
// LOGIKA MASUK 

session_start();
include 'db.php'; // Koneksi harus sudah tersedia di $pdo

// Cek apakah data dikirim via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ambil data dari form
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 2. Validasi sederhana
    if (empty($username) || empty($password)) {
        header('Location: login.php?error=1');
        exit;
    }

    // 3. Cek ke database (PDO)
    try {
        // KOREKSI DI SINI: Ganti 'id' menjadi 'id_users'
        $stmt = $pdo->prepare("SELECT id_users, username, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        // 4. Verifikasi (SANGAT TIDAK AMAN - PERBANDINGAN TEKS BIASA)
        if ($user && ($password === $user['password'])) {

            // === JIKA BERHASIL ===
            session_regenerate_id(true);

            // KOREKSI DI SINI: Ganti $user['id'] menjadi $user['id_users']
            $_SESSION['user_id'] = $user['id_users'];
            $_SESSION['username'] = $user['username'];

            // Arahkan ke halaman utama
            header('Location: index.php');
            exit;
        } else {
            // === JIKA GAGAL ===
            header('Location: login.php?error=2'); // Username/pass salah
            exit;
        }
    } catch (\PDOException $e) {
        // Tampilkan error query untuk debugging
        die("Query Error: SQLSTATE[{$e->getCode()}]: " . $e->getMessage());
    }
} else {
    header('Location: login.php');
    exit;
}
