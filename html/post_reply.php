<?php
// SEPERTI JUDUL

// 1. Mulai session dan panggil koneksi database
session_start();
include 'db.php';

// 2. Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, tendang ke halaman login
    header('Location: login.php');
    exit;
}

// 3. Pastikan data dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4. Ambil data dari formulir
    $content = $_POST['content'];
    $topic_id = $_POST['topic_id'];
    $user_id = $_SESSION['user_id']; // Ambil ID pengguna yang sedang login

    // 5. Validasi: Pastikan balasan tidak kosong
    if (!empty(trim($content)) && !empty($topic_id)) {

        try {
            // 6. Siapkan dan jalankan kueri INSERT
            $sql = "INSERT INTO forum_posts (topic_id, user_id, content, created_at) 
                    VALUES (?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$topic_id, $user_id, $content]);

            // ==========================================================
            // BARIS YANG ERROR SUDAH SAYA HAPUS DARI SINI
            // ==========================================================

            // 8. Redirect (arahkan) pengguna KEMBALI ke halaman topik
            header('Location: view_topic.php?id=' . $topic_id);
            exit;
        } catch (Exception $e) {
            // Jika ada error database
            die("Error menyimpan balasan: " . $e->getMessage());
        }
    } else {
        // Jika balasan kosong, kirim kembali ke topik
        header('Location: view_topic.php?id=' . $topic_id . '&error=empty');
        exit;
    }
} else {
    // Jika file diakses langsung (bukan via POST), kirim ke forum
    header('Location: forum.php');
    exit;
}
