<?php
// IKI JERE GAUSAH SEMENTARA OJO DIHAPUS

session_start();
// Kita butuh koneksi PDO dari db.php
include 'db.php';

// Cek apakah data dikirim via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ambil data dari form
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']); // <-- TAMBAHAN BARU
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // 2. Validasi Sederhana (Server-side)
    // Cek field baru 'full_name' juga
    if (empty($username) || empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) { // <-- DIUBAH
        header('Location: register.php?error=empty');
        exit;
    }

    if ($password !== $confirm_password) {
        header('Location: register.php?error=passwordmismatch');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: register.php?error=invalidemail');
        exit;
    }

    // 3. Cek apakah username atau email sudah ada (PDO)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        // User sudah ada
        header('Location: register.php?error=userexists');
        exit;
    }

    // 4. Hash Password (SANGAT PENTING!)
    // Jangan pernah simpan password sebagai plain text
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 5. Masukkan data pengguna baru ke database
    try {
        // Mulai transaksi (opsional tapi bagus)
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password_hash]);

        // 6. Buat juga profilnya DENGAN NAMA LENGKAP
        $user_id = $pdo->lastInsertId();
        // Query INSERT diubah untuk menyertakan 'full_name'
        $stmt_profile = $pdo->prepare("INSERT INTO user_profiles (user_id, full_name) VALUES (?, ?)"); // <-- DIUBAH
        // Masukkan $full_name ke dalam execute
        $stmt_profile->execute([$user_id, $full_name]); // <-- DIUBAH

        // Selesaikan transaksi
        $pdo->commit();

        // 7. Berhasil! Arahkan ke halaman login dengan pesan sukses
        header('Location: login.php?success=registered');
        exit;
    } catch (PDOException $e) {
        // Jika ada error, batalkan semua
        $pdo->rollBack();

        // Tangani error database
        // Di dunia nyata, log error ini, jangan tampilkan ke user
        // die("Error: " . $e->getMessage()); 
        header('Location: register.php?error=dberror');
        exit;
    }
} else {
    // Jika diakses langsung, tendang ke halaman register
    header('Location: register.php');
    exit;
}
