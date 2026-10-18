<?php
if (!isset($_SESSION)) session_start();
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
  <div class="brand">
    <div class="brand-icon">S</div>
    <div><h2>SARPRAS SEKOLAH</h2><small>Sistem Aspirasi & Fasilitas</small></div>
  </div>
  <div class="nav-title">Menu Utama</div>
  <div class="nav">
    <a class="<?= $current==='index.php'?'active':'' ?>" href="index.php">▣ Dashboard</a>
    <a class="<?= in_array($current,['tampil.php','tambah.php','edit.php'])?'active':'' ?>" href="tampil.php">☷ Data Aspirasi</a>
    <a class="<?= in_array($current,['monitoring.php','proses_laporan.php'])?'active':'' ?>" href="monitoring.php">◉ Monitoring Laporan</a>
    <a class="<?= in_array($current,['kategori.php','tambah_kategori.php','edit_kategori.php'])?'active':'' ?>" href="kategori.php">▦ Kategori</a>
    <a class="<?= in_array($current,['siswa.php','tambah_siswa.php','edit_siswa.php'])?'active':'' ?>" href="siswa.php">♙ Data Siswa</a>
    <a class="<?= in_array($current,['admin.php','tambah_admin.php','edit_admin.php'])?'active':'' ?>" href="admin.php">⚙ Pengguna</a>
  </div>
  <div class="nav-title">Akun</div>
  <div class="nav">
    <a href="logout.php" onclick="return confirm('Yakin ingin logout?')">↪ Logout</a>
  </div>
</div>
<div class="main">
