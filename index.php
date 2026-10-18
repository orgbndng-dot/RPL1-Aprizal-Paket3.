<?php
include "auth.php"; 
include "conection.php";

// Fungsi untuk menghitung total baris dalam tabel
function total($conn,$table) {
    $r =$conn->query("SELECT COUNT(*) c FROM `$table`"); 
    return $r ? (int)$r->fetch_assoc()['c'] : 0;
}

// Mengambil data statistik
$asp = total($conn, 'id_aspirasi'); 
$sis = total($conn, 'siswa'); 
$kat = total($conn, 'kategori');

$today_query =$conn->query("SELECT COUNT(*) c FROM id_aspirasi WHERE DATE(tanggal) = CURDATE()");
$today =$today_query->fetch_assoc()['c'];

// Mengambil data aspirasi terbaru (6 data terakhir)
$recent =$conn->query("
    SELECT a.id_aspirasi, a.tanggal, a.kategori, a.isi_aspirasi, s.nama_siswa, s.kelas 
    FROM id_aspirasi a 
    JOIN siswa s ON s.id_siswa = a.id_siswa 
    ORDER BY a.id_aspirasi DESC 
    LIMIT 6
");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Sarpras</title>
    <link rel="stylesheet" href="./data.css?v=spiderman2026">
</head>
<body>
    <div class="app">
        <?php include "header.php"; ?>

        <div class="topbar">
            <div>
                <p class="eyebrow">SISTEM INFORMASI SARANA & PRASARANA</p>
                <h1>Dashboard</h1>
                <p class="subtitle">Pantau aspirasi dan data pendukung sekolah dalam satu halaman.</p>
            </div>
            <div class="userbox">
                👤 <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Admin') ?>
            </div>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <div class="alert success">
                <?php 
                    if ($_GET['status'] === 'hapus') {                         echo 'Data berhasil dihapus.';                     } elseif ($_GET['status'] === 'edit') {
                        echo 'Data berhasil diperbarui.';
                    } else {
                        echo 'Data berhasil disimpan.';
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="grid">
            <div class="stat">
                <div class="label">Total Aspirasi</div>
                <div class="num"><?= $asp ?></div>
            </div>
            <div class="stat">
                <div class="label">Data Siswa</div>
                <div class="num"><?= $sis ?></div>
            </div>
            <div class="stat">
                <div class="label">Kategori</div>
                <div class="num"><?= $kat ?></div>
            </div>
            <div class="stat">
                <div class="label">Aspirasi Hari Ini</div>
                <div class="num"><?= $today ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Aspirasi Terbaru</h2>
                    <p class="subtitle">Data terbaru yang masuk ke sistem.</p>
                </div>
                <a class="btn btn-primary" href="tambah.php">＋ Tambah Aspirasi</a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Kategori</th>
                            <th>Aspirasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent &&$recent->num_rows): ?>
                            <?php while ($r =$recent->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['tanggal']) ?></td>
                                    <td><b><?= htmlspecialchars($r['nama_siswa']) ?></b></td>
                                    <td><?= htmlspecialchars($r['kelas']) ?></td>
                                    <td><span class="badge"><?= htmlspecialchars($r['kategori']) ?></span></td>
                                    <td><?= nl2br(htmlspecialchars($r['isi_aspirasi'])) ?></td>
                                    <td>
                                        <a class="btn-edit" href="edit.php?id=<?= $r['id_aspirasi'] ?>">Edit</a>
                                        <a class="btn-hapus" href="hapus.php?id=<?= $r['id_aspirasi'] ?>" onclick="return confirm('Hapus aspirasi ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty">Belum ada aspirasi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php include "footer.php"; ?>
    </div>
</body>
</html>
