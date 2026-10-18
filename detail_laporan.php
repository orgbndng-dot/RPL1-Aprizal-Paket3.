<?php
include "auth_siswa.php";
include "conection.php";
include "laporan_helper.php";

$id = (int)($_GET['id'] ?? 0);

// Hanya boleh melihat laporan MILIK SENDIRI
$q = $conn->prepare("SELECT id_aspirasi, tanggal, kategori, isi_aspirasi, status FROM id_aspirasi WHERE id_aspirasi = ? AND id_siswa = ?");
$q->bind_param("ii", $id, $ID_SISWA);
$q->execute();
$lap = $q->get_result()->fetch_assoc();
if (!$lap) {
    http_response_code(404);
    die("Laporan tidak ditemukan. <a href='siswa_dashboard.php'>Kembali</a>");
}

// Riwayat penanganan (terlama -> terbaru)
$h = $conn->prepare("
    SELECT p.status, p.tanggapan, p.tanggal, u.nama_lengkap AS petugas
    FROM penanganan p
    LEFT JOIN users u ON u.id_user = p.id_user
    WHERE p.id_aspirasi = ?
    ORDER BY p.id_penanganan ASC");
$h->bind_param("i", $id);
$h->execute();
$riwayat = $h->get_result();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Detail Laporan</title>
<link rel="stylesheet" href="./data.css?v=spiderman2026">
<link rel="stylesheet" href="./laporan.css?v=spiderman2026">
</head>
<body>
<?php include "header_siswa.php"; ?>
<div class="page-wrap" style="max-width:800px">

  <div class="panel">
    <h1 style="margin-bottom:10px">Detail Laporan #<?= (int)$lap['id_aspirasi'] ?></h1>
    <dl class="meta-grid">
      <dt>Tanggal</dt><dd><?= tgl_indo($lap['tanggal']) ?></dd>
      <dt>Kategori</dt><dd><?= htmlspecialchars($lap['kategori']) ?></dd>
      <dt>Status</dt><dd><?= badge_status($lap['status']) ?></dd>
    </dl>
    <strong>Isi laporan</strong>
    <div class="isi-box"><?= htmlspecialchars($lap['isi_aspirasi']) ?></div>
  </div>

  <div class="panel">
    <h2>Penanganan Laporan</h2>
    <?php if ($riwayat->num_rows): ?>
      <ul class="timeline">
        <?php while ($r = $riwayat->fetch_assoc()): ?>
          <li>
            <?= badge_status($r['status']) ?>
            <div class="when"><?= tgl_indo($r['tanggal']) ?><?= $r['petugas'] ? ' • oleh ' . htmlspecialchars($r['petugas']) : '' ?></div>
            <div class="text"><?= htmlspecialchars($r['tanggapan']) ?></div>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p class="empty">Laporan kamu belum ditangani. Mohon menunggu.</p>
    <?php endif; ?>
  </div>

  <a class="btn btn-secondary" href="siswa_dashboard.php">← Kembali</a>
</div>
<div class="footer">Aplikasi Web Sarpras Sekolah • Muhamad Aprizal</div>
</body>
</html>
