<?php
// File: user_action.php
// Fungsionalitas: Mengubah peran pengguna (misal: Promosi menjadi Admin).

session_start();
// --- Pengecekan Keamanan (Wajib!) ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../html/login.php");
    exit;
}

// 1. Pastikan ID dan Aksi tersedia
if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: users.php");
    exit;
}

include '../config/db_connect.php'; 

$user_id = (int)$_GET['id'];
$action = $_GET['action'];
$success_redirect = "Location: users.php?success=action_performed";

if ($user_id > 0) {
    try {
        if ($action === 'promote') {
            // Aksi: Promosikan user menjadi Admin
            $new_role = 'admin';
            
            // Query untuk update role
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id_users = ?");
            $stmt->execute([$new_role, $user_id]);
            
            if ($stmt->rowCount() > 0) {
                // Beri pesan sukses, dan redirect
                header("Location: users.php?success=role_updated");
                exit;
            } else {
                // Jika tidak ada perubahan
                header("Location: users.php?error=no_change");
                exit;
            }

        } elseif ($action === 'block') {
            // Aksi: BLOKIR/NONAKTIFKAN PENGGUNA
            // Karena tidak ada kolom 'is_active', kita akan menggunakan placeholder
            // Di sistem nyata, Anda akan menggunakan UPDATE users SET is_active = 0
            
            // Untuk sementara, kita berikan pesan error untuk simulasi
            header("Location: users.php?error=block_placeholder");
            exit;

        } else {
            header("Location: users.php?error=invalid_action");
            exit;
        }

    } catch (\PDOException $e) {
        // Jika terjadi error database
        // Untuk debugging: die("Error Database: " . $e->getMessage());
        header("Location: users.php?error=db_fail");
        exit;
    }
} else {
    // Jika ID user tidak valid
    header("Location: users.php?error=invalid_id");
    exit;
}
?>