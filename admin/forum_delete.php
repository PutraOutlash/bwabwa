<?php
// File: forum_delete.php
// Fungsionalitas: Menghapus topik forum dan semua postingan terkait (CASCADE).

session_start();
// --- Pengecekan Keamanan (Wajib!) ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../html/login.php");
    exit;
}

// 1. Pastikan request datang dari form POST
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['topic_id'])) {
    header("Location: forum.php"); // Kembali ke daftar topik jika request tidak valid
    exit;
}

include '../config/db_connect.php';

$topic_id = (int)$_POST['topic_id'];

if ($topic_id > 0) {
    try {
        // Query untuk menghapus topik berdasarkan topic_id
        // Karena ada FOREIGN KEY ON DELETE CASCADE, semua postingan di forum_posts
        // yang terhubung dengan topik ini akan otomatis terhapus.
        $stmt = $pdo->prepare("DELETE FROM forum_topics WHERE id = ?");
        $stmt->execute([$topic_id]);

        if ($stmt->rowCount() > 0) {
            // Redirect kembali ke halaman daftar topik dengan pesan sukses
            header("Location: forum.php?success=topic_deleted");
            exit;
        } else {
            // Jika ID tidak ditemukan
            header("Location: forum.php?error=not_found");
            exit;
        }
    } catch (\PDOException $e) {
        // Untuk debugging: die("Error Database: " . $e->getMessage());
        header("Location: forum.php?error=db_fail");
        exit;
    }
} else {
    // Jika ID topik tidak valid
    header("Location: forum.php?error=invalid_id");
    exit;
}
