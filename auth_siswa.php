<?php
session_start();

// Halaman khusus siswa: harus login sebagai siswa
if (empty($_SESSION['login']) || ($_SESSION['role'] ?? '') !== 'Siswa' || empty($_SESSION['id_siswa'])) {
    header("Location: login_siswa.php");
    exit;
}
$ID_SISWA = (int)$_SESSION['id_siswa'];
?>
