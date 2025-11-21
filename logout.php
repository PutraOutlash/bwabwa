<?php
// LOGOUT

session_start(); // Mulai sesi
session_unset(); // Hapus semua variabel sesi
session_destroy(); // Hancurkan sesi

// Kembalikan pengguna ke halaman login
header("Location: login.php");
exit;
