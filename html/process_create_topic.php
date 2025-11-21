<?php
// GAE CREATE

// 1. Mulai session dan panggil koneksi database
session_start();
include 'db.php';

// 2. Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 3. Pastikan data dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4. Ambil data dari formulir
    $title = $_POST['title'];
    $content = $_POST['content'];
    $section_id = $_POST['section_id'];
    $user_id = $_SESSION['user_id']; // Ambil ID pengguna yang sedang login

    // 5. Validasi: Pastikan data tidak kosong
    if (empty(trim($title)) || empty(trim($content)) || empty($section_id)) {
        // Jika ada yang kosong, kirim kembali ke formulir
        header('Location: create_topic.php?section_id=' . $section_id . '&error=empty');
        exit;
    }

    // ==========================================================
    // INI BAGIAN PENTING: TRANSAKSI DATABASE
    // ==========================================================

    // Kita 'try' (coba) jalankan kedua kueri.
    // Jika salah satu gagal, kita 'catch' (tangkap) errornya
    // dan 'rollBack' (batalkan) semua perubahan.

    try {
        // Mulai Transaksi
        $pdo->beginTransaction();

        // **LANGKAH A: INSERT KE TABEL 'forum_topics'**
        $sql_topic = "INSERT INTO forum_topics (section_id, user_id, title, created_at) 
                      VALUES (?, ?, ?, NOW())";
        $stmt_topic = $pdo->prepare($sql_topic);
        $stmt_topic->execute([$section_id, $user_id, $title]);

        // **LANGKAH B: AMBIL ID TOPIK YANG BARU SAJA DIBUAT**
        $new_topic_id = $pdo->lastInsertId();

        // **LANGKAH C: INSERT KE TABEL 'forum_posts'**
        // (Ini adalah postingan pertama dari topik tersebut)
        $sql_post = "INSERT INTO forum_posts (topic_id, user_id, content, created_at) 
                     VALUES (?, ?, ?, NOW())";
        $stmt_post = $pdo->prepare($sql_post);
        $stmt_post->execute([$new_topic_id, $user_id, $content]);

        // **LANGKAH D: SELESAI!**
        // Jika kedua kueri berhasil, 'commit' (simpan permanen) perubahan
        $pdo->commit();

        // 9. Redirect (arahkan) pengguna ke halaman topik yang baru dibuat
        header('Location: view_topic.php?id=' . $new_topic_id);
        exit;
    } catch (Exception $e) {
        // 10. JIKA GAGAL: Batalkan semua perubahan
        $pdo->rollBack();

        // Tampilkan pesan error
        die("Error membuat topik baru: " . $e->getMessage());
    }
} else {
    // Jika file diakses langsung (bukan via POST), kirim ke forum
    header('Location: forum.php');
    exit;
}
