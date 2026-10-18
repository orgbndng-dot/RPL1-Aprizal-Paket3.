<?php
require_once "auth.php";
require_once "conection.php";
require_once "laporan_helper.php";

$id = (int)($_GET['id'] ?? $_POST['id_aspirasi'] ?? 0);

if ($id < 1) {
    header("Location: monitoring.php");
    exit;
}

$q = $conn->prepare("
    SELECT
        a.id_aspirasi,
        a.tanggal,
        a.kategori,
        a.isi_aspirasi,
        a.status,
        s.nama_siswa,
        s.nisn,
        s.kelas,
        s.jurusan
    FROM id_aspirasi a
    INNER JOIN siswa s ON s.id_siswa = a.id_siswa
    WHERE a.id_aspirasi = ?
    LIMIT 1
");
$q->bind_param("i", $id);
$q->execute();
$lap = $q->get_result()->fetch_assoc();

if (!$lap) {
    http_response_code(404);
    die("Laporan tidak ditemukan. <a href='monitoring.php'>Kembali</a>");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = trim($_POST['status'] ?? '');
    $tanggapan = trim($_POST['tanggapan'] ?? '');
    $idUser = (int)($_SESSION['id_user'] ?? 0);

    if (!in_array($status, STATUS_LAPORAN, true)) {
        $error = "Status tidak valid.";
    } elseif ($tanggapan === '') {
        $error = "Tanggapan wajib diisi.";
    } elseif (mb_strlen($tanggapan) < 5) {
        $error = "Tanggapan terlalu singkat.";
    } else {
        $tgl = date('Y-m-d H:i:s');

        $conn->begin_transaction();

        try {
            $i = $conn->prepare("
                INSERT INTO penanganan
                    (id_aspirasi, id_user, status, tanggapan, tanggal)
                VALUES
                    (?, NULLIF(?, 0), ?, ?, ?)
            ");

            if (!$i) {
                throw new Exception($conn->error);
            }

            $i->bind_param("iisss", $id, $idUser, $status, $tanggapan, $tgl);

            if (!$i->execute()) {
                throw new Exception($i->error);
            }

            $u = $conn->prepare("
                UPDATE id_aspirasi
                SET status = ?
                WHERE id_aspirasi = ?
            ");

            if (!$u) {
                throw new Exception($conn->error);
            }

            $u->bind_param("si", $status, $id);

            if (!$u->execute()) {
                throw new Exception($u->error);
            }

            $conn->commit();

            header("Location: proses_laporan.php?id=" . $id . "&saved=1");
            exit;
        } catch (Throwable $e) {
            $conn->rollback();
            $error = "Gagal menyimpan penanganan: " . $e->getMessage();
        }
    }
}

$h = $conn->prepare("
    SELECT
        p.status,
        p.tanggapan,
        p.tanggal,
        u.nama_lengkap AS petugas
    FROM penanganan p
    LEFT JOIN users u ON u.id_user = p.id_user
    WHERE p.id_aspirasi = ?
    ORDER BY p.id_penanganan ASC
");
$h->bind_param("i", $id);
$h->execute();
$riwayat = $h->get_result();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Proses Laporan #<?= (int)$lap['id_aspirasi'] ?></title>
<link rel="stylesheet" href="./data.css?v=spiderman2026">
<link rel="stylesheet" href="./laporan.css?v=spiderman2026">
</head>
<body>
<div class="app">
<?php include "header.php"; ?>

<div class="topbar">
    <div>
        <p class="eyebrow">PEMANTAUAN</p>
        <h1>Proses Laporan #<?= (int)$lap['id_aspirasi'] ?></h1>
        <p class="subtitle">Perbarui status dan simpan catatan penanganan.</p>
    </div>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert success">Penanganan berhasil disimpan dan status laporan telah diperbarui.</div>
<?php endif; ?>

<div class="panel">
    <h2>Data Laporan</h2>

    <dl class="meta-grid">
        <dt>Tanggal</dt>
        <dd><?= htmlspecialchars(tgl_indo($lap['tanggal'])) ?></dd>

        <dt>Siswa</dt>
        <dd><?= htmlspecialchars($lap['nama_siswa']) ?> (<?= htmlspecialchars($lap['nisn']) ?>)</dd>

        <dt>Kelas</dt>
        <dd><?= htmlspecialchars($lap['kelas']) ?> - <?= htmlspecialchars($lap['jurusan']) ?></dd>

        <dt>Kategori</dt>
        <dd><?= htmlspecialchars($lap['kategori']) ?></dd>

        <dt>Status</dt>
        <dd><?= badge_status($lap['status']) ?></dd>
    </dl>

    <strong>Isi laporan</strong>
    <div class="isi-box"><?= nl2br(htmlspecialchars($lap['isi_aspirasi'])) ?></div>
</div>

<div class="panel">
    <h2>Update Penanganan</h2>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id_aspirasi" value="<?= (int)$lap['id_aspirasi'] ?>">

        <label for="status">Status</label>
        <select id="status" name="status" required>
            <?php foreach (STATUS_LAPORAN as $s): ?>
                <?php $selected = ($_POST['status'] ?? $lap['status']) === $s; ?>
                <option value="<?= htmlspecialchars($s) ?>" <?= $selected ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="tanggapan">Tanggapan / Catatan</label>
        <textarea
            id="tanggapan"
            name="tanggapan"
            rows="6"
            required
            placeholder="Contoh: Laporan diterima. Perbaikan dijadwalkan hari Kamis."
        ><?= htmlspecialchars($_POST['tanggapan'] ?? '') ?></textarea>

        <a class="btn btn-secondary" href="monitoring.php">Kembali</a>
        <button class="btn btn-primary" type="submit">Simpan Penanganan</button>
    </form>
</div>

<div class="panel">
    <h2>Riwayat Penanganan</h2>

    <?php if ($riwayat->num_rows): ?>
        <ul class="timeline">
            <?php while ($r = $riwayat->fetch_assoc()): ?>
                <li>
                    <?= badge_status($r['status']) ?>
                    <div class="when">
                        <?= htmlspecialchars(tgl_indo($r['tanggal'])) ?>
                        <?php if ($r['petugas']): ?>
                            • oleh <?= htmlspecialchars($r['petugas']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="text"><?= nl2br(htmlspecialchars($r['tanggapan'])) ?></div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="empty">Belum ada penanganan untuk laporan ini.</p>
    <?php endif; ?>
</div>

<?php include "footer.php"; ?>
</div>
</body>
</html>
