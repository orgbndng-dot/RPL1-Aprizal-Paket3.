<?php
require_once "auth.php";
require_once "conection.php";
require_once "laporan_helper.php";

$q = trim($_GET['q'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$params = [];
$types = '';
$where = [];

$sql = "
    SELECT
        a.id_aspirasi,
        a.tanggal,
        a.kategori,
        a.isi_aspirasi,
        a.status,
        s.id_siswa,
        s.nisn,
        s.nama_siswa,
        s.kelas,
        s.jurusan
    FROM id_aspirasi a
    INNER JOIN siswa s ON s.id_siswa = a.id_siswa
";

if ($q !== '') {
    $where[] = "(s.nama_siswa LIKE ? OR s.nisn LIKE ? OR s.kelas LIKE ? OR a.kategori LIKE ? OR a.isi_aspirasi LIKE ?)";
    $like = "%{$q}%";
    for ($i = 0; $i < 5; $i++) {
        $params[] = $like;
        $types .= 's';
    }
}

if (in_array($statusFilter, STATUS_LAPORAN, true)) {
    $where[] = "a.status = ?";
    $params[] = $statusFilter;
    $types .= 's';
} else {
    $statusFilter = '';
}

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY a.id_aspirasi DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Query monitoring gagal: " . htmlspecialchars($conn->error));
}

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$count = array_fill_keys(STATUS_LAPORAN, 0);
$countStmt = $conn->query("SELECT status, COUNT(*) AS jumlah FROM id_aspirasi GROUP BY status");
if ($countStmt) {
    while ($row = $countStmt->fetch_assoc()) {
        if (isset($count[$row['status']])) {
            $count[$row['status']] = (int)$row['jumlah'];
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Monitoring Laporan</title>
<link rel="stylesheet" href="./data.css?v=spiderman2026">
<link rel="stylesheet" href="./laporan.css?v=spiderman2026">
</head>
<body>
<div class="app">
<?php include "header.php"; ?>

<div class="topbar">
    <div>
        <p class="eyebrow">PEMANTAUAN</p>
        <h1>Monitoring Laporan</h1>
        <p class="subtitle">Pantau laporan siswa dan proses penanganannya.</p>
    </div>
</div>

<div class="stat-row">
    <?php foreach (STATUS_LAPORAN as $s): ?>
        <div class="stat-box <?= strtolower($s) ?>">
            <div class="label"><?= htmlspecialchars($s) ?></div>
            <div class="num"><?= $count[$s] ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="panel">
    <div class="card-head">
        <div>
            <h2>Daftar Laporan</h2>
            <p class="subtitle">Klik proses untuk memperbarui status dan memberikan tanggapan.</p>
        </div>
    </div>

    <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px">
        <input
            class="input-search"
            type="search"
            name="q"
            value="<?= htmlspecialchars($q) ?>"
            placeholder="Cari nama, NISN, kelas, kategori, isi..."
        >
        <select name="status">
            <option value="">Semua Status</option>
            <?php foreach (STATUS_LAPORAN as $s): ?>
                <option value="<?= htmlspecialchars($s) ?>" <?= $statusFilter === $s ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary" type="submit">Filter</button>
        <a class="btn btn-secondary" href="monitoring.php">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Pelapor</th>
                    <th>Kelas</th>
                    <th>Kategori</th>
                    <th>Isi Laporan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows): ?>
                <?php $no = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars(tgl_indo($row['tanggal'])) ?></td>
                        <td>
                            <b><?= htmlspecialchars($row['nama_siswa']) ?></b><br>
                            <small><?= htmlspecialchars($row['nisn']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($row['kelas']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($row['kategori']) ?></span></td>
                        <td><?= nl2br(htmlspecialchars($row['isi_aspirasi'])) ?></td>
                        <td><?= badge_status($row['status']) ?></td>
                        <td>
                            <a class="btn-edit" href="proses_laporan.php?id=<?= (int)$row['id_aspirasi'] ?>">Proses</a>
                            <a href="detail_laporan.php?id=<?= (int)$row['id_aspirasi'] ?>">Detail</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="empty">Belum ada laporan yang sesuai.</td>
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
