<?php
// File: forum_delete_post.php
// Fungsionalitas: Menghapus satu postingan/komentar dari sebuah topik.

session_start();
// --- Pengecekan Keamanan (Wajib!) ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../html/login.php");
    exit;
}

// 1. Pastikan data POST yang dibutuhkan tersedia
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['post_id']) || !isset($_POST['topic_id'])) {
    header("Location: forum.php"); // Kembali ke daftar topik jika request tidak valid
    exit;
}

include '../config/db_connect.php';

$post_id = (int)$_POST['post_id'];
$topic_id = (int)$_POST['topic_id']; // Kita butuh ID topik untuk redirect kembali

if ($post_id > 0) {
    try {
        // Query untuk menghapus postingan berdasarkan post_id
        $stmt = $pdo->prepare("DELETE FROM forum_posts WHERE id = ?");
        $stmt->execute([$post_id]);

        // Cek apakah ada baris yang terpengaruh
        if ($stmt->rowCount() > 0) {
            // Redirect kembali ke halaman moderasi postingan topik tersebut dengan pesan sukses
            header("Location: forum_view_posts.php?id={$topic_id}&success=post_deleted");
            exit;
        } else {
            // Jika post ID tidak ditemukan
            header("Location: forum_view_posts.php?id={$topic_id}&error=not_found");
            exit;
        }
    } catch (\PDOException $e) {
        // Jika terjadi error database
        // Untuk debugging: die("Error Database: " . $e->getMessage());
        header("Location: forum_view_posts.php?id={$topic_id}&error=db_fail");
        exit;
    }
} else {
    // Jika ID postingan tidak valid
    header("Location: forum.php?error=invalid_id");
    exit;
}
