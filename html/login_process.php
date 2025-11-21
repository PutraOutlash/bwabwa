<?php
// File: login_process.php
// LOGIKA MASUK 

session_start();
// Kita butuh koneksi PDO dari db_config.php
// Path-nya '../' karena file ini ada di 'html'
include 'db.php';

// Cek apakah data dikirim via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ambil data dari form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 2. Validasi sederhana
    if (empty($username) || empty($password)) {
        // Jika kosong, kembalikan ke login dengan pesan error
        header('Location: login.php?error=1');
        exit;
    }

    // 3. Cek ke database (PDO)
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // 4. Verifikasi
        // Cek apakah $user ditemukan DAN password-nya cocok
        if ($user && password_verify($password, $user['password_hash'])) {

            // === JIKA BERHASIL ===
            // Password cocok!

            // 1. Regenerasi session ID untuk keamanan
            session_regenerate_id(true);

            // 2. Simpan "Tiket Masuk" ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // 3. Arahkan ke halaman utama
            header('Location: index.php');
            exit;
        } else {
            // === JIKA GAGAL ===
            // Username tidak ditemukan ATAU password salah

            // Kembalikan ke login.php dengan pesan error
            header('Location: login.php?error=1');
            exit;
        }
    } catch (PDOException $e) {
        // Tangani error database
        // die("Error: " . $e->getMessage()); 
        header('Location: login.php?error=1');
        exit;
    }
} else {
    // Jika diakses langsung, tendang ke halaman login
    header('Location: login.php');
    exit;
}
