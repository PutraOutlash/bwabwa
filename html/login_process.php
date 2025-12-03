<?php
// File: login_process.php
// LOKASI: bloombelly/html/login_process.php

session_start();

// PERBAIKAN PATH 1: db.php SEKARANG BERADA DI FOLDER YANG SAMA
include '../config/db_connect.php';

// Cek apakah data dikirim via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        header('Location: login.php?error=1');
        exit;
    }

    try {
        // SELECT query harus menyertakan kolom 'role'
        $stmt = $pdo->prepare("SELECT id_users, username, password, role, avatar_url FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && ($password === $user['password'])) {

            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id_users'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['avatar_url'] = $user['avatar_url'];

            // === PENGECEKAN ROLE & PENGARAHAN ===
            if ($user['role'] === 'admin') {

                // PERBAIKAN PATH 2: PENGARAHAN ADMIN
                // Keluar dari 'html/' (yaitu '../') lalu masuk ke 'admin/index.php'
                header("Location: ../admin/dashboard.php");
                exit;
            } else {

                // User biasa diarahkan ke index.php di folder html/ (relative path)
                header('Location: index.php');
                exit;
            }
        } else {
            header('Location: login.php?error=2');
            exit;
        }
    } catch (\PDOException $e) {
        // Tampilkan error query untuk debugging
        die("Query Error: " . $e->getMessage());
    }
} else {
    header('Location: login.php');
    exit;
}
