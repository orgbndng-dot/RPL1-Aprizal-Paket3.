<?php $cur = basename($_SERVER['PHP_SELF']); ?>
<div class="topnav">
  <div>
    <a class="<?= $cur==='siswa_dashboard.php'?'active':'' ?>" href="siswa_dashboard.php">Laporan Saya</a>
    <a class="<?= $cur==='buat_laporan.php'?'active':'' ?>" href="buat_laporan.php">＋ Buat Laporan</a>
  </div>
  <div class="who">
    👤 <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?>
    &nbsp;|&nbsp;
    <a href="logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a>
  </div>
</div>
