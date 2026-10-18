<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

// Siswa tidak boleh membuka halaman admin
if (($_SESSION['role'] ?? '') === 'Siswa') {
    header("Location: siswa_dashboard.php");
    exit;
}
?>
