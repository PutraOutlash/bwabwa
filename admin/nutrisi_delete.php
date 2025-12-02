<?php
// File: nutrisi_delete.php
// Fungsionalitas: Menghapus data nutrisi berdasarkan ID.

session_start();
// --- Pengecekan Keamanan ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../html/login.php");
    exit;
}

// 1. Pastikan request datang dari form POST
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['id'])) {
    header("Location: data_management.php");
    exit;
}

include '../config/db_connect.php';

$nutrisi_id = (int)$_POST['id'];

if ($nutrisi_id > 0) {
    try {
        // Query untuk menghapus data nutrisi berdasarkan id_nutrisi
        $stmt = $pdo->prepare("DELETE FROM nutrisi WHERE id_nutrisi = ?");
        $stmt->execute([$nutrisi_id]);

        if ($stmt->rowCount() > 0) {
            // Redirect kembali ke halaman daftar data management dengan pesan sukses
            header("Location: data_management.php?success=nutrisi_deleted");
            exit;
        } else {
            header("Location: data_management.php?error=not_found");
            exit;
        }
    } catch (\PDOException $e) {
        // Untuk debugging: die("Error Database: " . $e->getMessage());
        header("Location: data_management.php?error=db_fail");
        exit;
    }
} else {
    header("Location: data_management.php?error=invalid_id");
    exit;
}
