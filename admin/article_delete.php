<?php
// File: article_delete.php
// Fungsionalitas: Menghapus artikel berdasarkan ID

session_start();
// --- Pengecekan Keamanan (Wajib!) ---
// 1. Pastikan user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../html/login.php");
    exit;
}

// 2. Pastikan request datang dari form POST (mencegah penghapusan via URL)
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['id'])) {
    header("Location: articles.php");
    exit;
}

// Pastikan include path ini benar, menuju ke folder config/db_connect.php
include '../config/db_connect.php';

$article_id = (int)$_POST['id'];

if ($article_id > 0) {
    try {
        // Query untuk menghapus artikel berdasarkan id_articles
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id_articles = ?");
        $stmt->execute([$article_id]);

        // Cek apakah ada baris yang terpengaruh (apakah penghapusan berhasil)
        if ($stmt->rowCount() > 0) {
            // Redirect kembali ke halaman daftar artikel dengan pesan sukses
            header("Location: articles.php?success=deleted");
            exit;
        } else {
            // Jika ID tidak ditemukan
            header("Location: articles.php?error=not_found");
            exit;
        }
    } catch (\PDOException $e) {
        // Jika terjadi error database (misal karena Foreign Key Constraint)
        // Dalam kasus ini, Anda mungkin ingin mencatat error daripada menampilkannya ke user
        // Untuk tujuan debugging: die("Error Database: " . $e->getMessage());
        header("Location: articles.php?error=db_fail");
        exit;
    }
} else {
    // Jika ID artikel tidak valid
    header("Location: articles.php?error=invalid_id");
    exit;
}
