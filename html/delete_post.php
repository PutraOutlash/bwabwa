<?php
session_start();
include 'db.php'; // Pastikan koneksi DB dipanggil

// 1. Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. Validasi input
if (!isset($_GET['id']) || !isset($_GET['topic']) || !is_numeric($_GET['id']) || !is_numeric($_GET['topic'])) {
    header('Location: forum.php');
    exit;
}

$post_id = $_GET['id'];
$topic_id = $_GET['topic'];
$user_id = $_SESSION['user_id'];

try {
    // 3. Cek kepemilikan post
    $stmt_check = $pdo->prepare("SELECT user_id FROM forum_posts WHERE id = ?");
    $stmt_check->execute([$post_id]);
    $post = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$post || $post['user_id'] != $user_id) {
        // Post tidak ditemukan atau user bukan pemilik
        // Bisa tambahkan pesan error atau redirect kembali
        header('Location: view_topic.php?id=' . $topic_id . '&error=unauthorized');
        exit;
    }

    // 4. Lakukan penghapusan
    $stmt_delete = $pdo->prepare("DELETE FROM forum_posts WHERE id = ?");
    $stmt_delete->execute([$post_id]);

    // 5. Redirect kembali ke topik dengan pesan sukses
    header('Location: view_topic.php?id=' . $topic_id . '&status=deleted');
    exit;
} catch (Exception $e) {
    // Handle error database
    header('Location: view_topic.php?id=' . $topic_id . '&error=db_error');
    exit;
}
