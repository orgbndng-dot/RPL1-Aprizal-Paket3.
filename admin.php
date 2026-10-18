<?php
require_once "auth.php";
require_once "conection.php";

// Pastikan tabel users tersedia sebelum menjalankan query.
$check = $conn->query("SHOW TABLES LIKE 'users'");
if (!$check) {
    die("Gagal memeriksa tabel users: " . htmlspecialchars($conn->error));
}

if ($check->num_rows === 0) {
    die("Tabel <b>users</b> belum ada. Import file <b>database.sql</b> ke database <b>12rpl1_aprizal_paket3</b> terlebih dahulu.");
}

// Ambil data pengguna.
$sql = "SELECT id_user, nama_lengkap, username, role
        FROM users
        ORDER BY id_user DESC";

$result = $conn->query($sql);

// Jangan menjalankan fetch_assoc() jika query gagal.
if ($result === false) {
    die("Query data pengguna gagal: " . htmlspecialchars($conn->error));
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengguna Sistem</title>
    <link rel="stylesheet" href="./data.css?v=spiderman2026">
</head>
<body>
<div class="app">
    <?php include "header.php"; ?>

    <div class="topbar">
        <div>
            <p class="eyebrow">PENGATURAN</p>
            <h1>Pengguna Sistem</h1>
            <p class="subtitle">Kelola akun yang dapat mengakses aplikasi Sarpras.</p>
        </div>
        <a class="btn btn-primary" href="tambah_admin.php">＋ Tambah Pengguna</a>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows === 0): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">Belum ada data pengguna.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php while ($r = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($r['nama_lengkap'] ?? '') ?></td>
                            <td><?= htmlspecialchars($r['username'] ?? '') ?></td>
                            <td><span class="badge"><?= htmlspecialchars($r['role'] ?? '') ?></span></td>
                            <td>
                                <a class="btn-edit" href="edit_admin.php?id=<?= (int)$r['id_user'] ?>">Edit</a>
                                <a class="btn-hapus" href="hapus_admin.php?id=<?= (int)$r['id_user'] ?>" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include "footer.php"; ?>
</div>
</body>
</html>
