<?php
session_start();
include '../config/db_connect.php'; // Pastikan koneksi DB dipanggil

// 1. Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. Validasi dan ambil input
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['post_id']) || !isset($_POST['content']) || !isset($_POST['topic_id'])) {
    header('Location: forum.php');
    exit;
}

$post_id = $_POST['post_id'];
$topic_id = $_POST['topic_id'];
$new_content = trim($_POST['content']);
$user_id = $_SESSION['user_id'];

// 3. Cek konten tidak boleh kosong
if (empty($new_content)) {
    header('Location: view_topic.php?id=' . $topic_id . '&error=content_empty');
    exit;
}

try {
    // 4. Cek kepemilikan post sebelum mengupdate
    // Catatan: Asumsi kolom user_id di forum_posts dan $_SESSION['user_id'] adalah id_users
    $stmt_check = $pdo->prepare("SELECT user_id FROM forum_posts WHERE id = ?");
    $stmt_check->execute([$post_id]);
    $post = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$post || $post['user_id'] != $user_id) {
        // Post tidak ditemukan atau user bukan pemilik
        header('Location: view_topic.php?id=' . $topic_id . '&error=unauthorized_edit');
        exit;
    }

    // 5. Lakukan update
    // KOREKSI UTAMA: Hapus 'updated_at = NOW()' karena kolom ini tidak ada di tabel forum_posts
    $stmt_update = $pdo->prepare("UPDATE forum_posts SET content = ? WHERE id = ?");
    $stmt_update->execute([$new_content, $post_id]);

    // 6. Redirect kembali ke topik
    header('Location: view_topic.php?id=' . $topic_id . '&status=edited#' . $post_id); // Redirect ke postingan yang di-edit
    exit;
} catch (Exception $e) {
    // Tampilkan error detail untuk debugging saat ini
    error_log("Edit Post Error: " . $e->getMessage());
    // Handle error database
    header('Location: view_topic.php?id=' . $topic_id . '&error=db_update_error');
    exit;
}
