<?php
include "auth_siswa.php";
include "conection.php";
include "laporan_helper.php";

// Hitung laporan milik siswa per status
$hitung = array_fill_keys(STATUS_LAPORAN, 0);
$st = $conn->prepare("SELECT status, COUNT(*) c FROM id_aspirasi WHERE id_siswa = ? GROUP BY status");
$st->bind_param("i", $ID_SISWA);
$st->execute();
$rs = $st->get_result();
while ($r = $rs->fetch_assoc()) $hitung[$r['status']] = (int)$r['c'];
$total = array_sum($hitung);

// Daftar laporan milik siswa
$q = $conn->prepare("SELECT id_aspirasi, tanggal, kategori, isi_aspirasi, status FROM id_aspirasi WHERE id_siswa = ? ORDER BY id_aspirasi DESC");
$q->bind_param("i", $ID_SISWA);
$q->execute();
$laporan = $q->get_result();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Laporan Saya</title>
<link rel="stylesheet" href="./data.css?v=spiderman2026">
<link rel="stylesheet" href="./laporan.css?v=spiderman2026">
</head>
<body>
<?php include "header_siswa.php"; ?>
<div class="page-wrap">

  <?php if (($_GET['status'] ?? '') === 'sukses'): ?>
    <div class="alert success">Laporan berhasil dikirim. Silakan pantau status penanganannya di sini.</div>
  <?php endif; ?>

  <h1>Laporan Saya</h1>

  <div class="stat-row">
    <div class="stat-box"><div class="label">Total</div><div class="num"><?= $total ?></div></div>
    <?php foreach (STATUS_LAPORAN as $s): ?>
      <div class="stat-box <?= strtolower($s) ?>"><div class="label"><?= $s ?></div><div class="num"><?= $hitung[$s] ?></div></div>
    <?php endforeach; ?>
  </div>

  <div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
      <h2 style="margin:0">Riwayat Laporan</h2>
      <a class="btn" href="buat_laporan.php">＋ Buat Laporan</a>
    </div>
    <table>
      <thead><tr><th>Tanggal</th><th>Kategori</th><th>Isi Laporan</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php if ($laporan->num_rows): while ($r = $laporan->fetch_assoc()): ?>
        <tr>
          <td><?= tgl_indo($r['tanggal']) ?></td>
          <td><?= htmlspecialchars($r['kategori']) ?></td>
          <td><?= htmlspecialchars(mb_strimwidth($r['isi_aspirasi'], 0, 80, '...')) ?></td>
          <td><?= badge_status($r['status']) ?></td>
          <td><a href="detail_laporan.php?id=<?= (int)$r['id_aspirasi'] ?>">Lihat penanganan</a></td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="5" class="empty">Kamu belum membuat laporan.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="footer">Aplikasi Web Sarpras Sekolah • Muhamad Aprizal</div>
</body>
</html>
