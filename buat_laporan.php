<?php
include "auth_siswa.php";
include "conection.php";

$error = '';
$kategori = '';
$isi = '';

// Daftar kategori untuk dropdown
$daftarKat = [];
$rk = $conn->query("SELECT nama_kategori FROM kategori ORDER BY nama_kategori");
while ($r = $rk->fetch_assoc()) $daftarKat[] = $r['nama_kategori'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori = trim($_POST['kategori'] ?? '');
    $isi      = trim($_POST['isi_aspirasi'] ?? '');

    if ($kategori === '' || $isi === '') {
        $error = "Kategori dan isi laporan wajib diisi.";
    } elseif (!in_array($kategori, $daftarKat, true)) {
        $error = "Kategori tidak valid.";
    } elseif (mb_strlen($isi) < 10) {
        $error = "Isi laporan terlalu singkat. Jelaskan kondisi dan lokasinya (minimal 10 karakter).";
    } else {
        $tgl = date('Y-m-d H:i:s');
        // id_siswa diambil dari session, bukan dari form -> siswa tidak bisa memalsukan
        $s = $conn->prepare("INSERT INTO id_aspirasi (tanggal, id_siswa, kategori, isi_aspirasi, status) VALUES (?, ?, ?, ?, 'Menunggu')");
        $s->bind_param("siss", $tgl, $ID_SISWA, $kategori, $isi);
        if ($s->execute()) {
            header("Location: siswa_dashboard.php?status=sukses");
            exit;
        }
        $error = "Gagal menyimpan laporan: " . $s->error;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Buat Laporan</title>
<link rel="stylesheet" href="./data.css?v=spiderman2026">
<link rel="stylesheet" href="./laporan.css?v=spiderman2026">
</head>
<body>
<?php include "header_siswa.php"; ?>
<div class="page-wrap" style="max-width:720px">
  <div class="panel">
    <h1>Buat Laporan</h1>
    <p style="color:#7f8c8d;margin-bottom:14px">Sampaikan masalah atau masukan tentang sarana dan prasarana sekolah.</p>

    <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post">
      <label>Kategori</label>
      <select name="kategori" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach ($daftarKat as $k): ?>
          <option value="<?= htmlspecialchars($k) ?>" <?= $k === $kategori ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Isi Laporan</label>
      <textarea name="isi_aspirasi" rows="6" required
        placeholder="Tuliskan kondisi, lokasi, dan usulan perbaikan secara jelas..."><?= htmlspecialchars($isi) ?></textarea>

      <a class="btn btn-secondary" href="siswa_dashboard.php">Batal</a>
      <button class="btn">Kirim Laporan</button>
    </form>
  </div>
</div>
<div class="footer">Aplikasi Web Sarpras Sekolah • Muhamad Aprizal</div>
</body>
</html>
