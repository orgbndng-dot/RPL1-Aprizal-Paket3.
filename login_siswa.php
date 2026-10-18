<?php
session_start();
require_once "conection.php";

if (!empty($_SESSION['login']) && ($_SESSION['role'] ?? '') === 'Siswa') {
    header("Location: siswa_dashboard.php");
    exit;
}

$error = '';
$nisn = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = trim($_POST['nisn'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nisn === '' || $password === '') {
        $error = "NISN dan password wajib diisi.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id_siswa, nisn, nama_siswa, kelas, jurusan, password
             FROM siswa
             WHERE nisn = ?
             LIMIT 1"
        );

        if (!$stmt) {
            $error = "Terjadi kesalahan database: " . $conn->error;
        } else {
            $stmt->bind_param("s", $nisn);
            $stmt->execute();
            $siswa = $stmt->get_result()->fetch_assoc();

            if ($siswa && !empty($siswa['password']) && password_verify($password, $siswa['password'])) {
                session_regenerate_id(true);

                $_SESSION['login'] = true;
                $_SESSION['role'] = 'Siswa';
                $_SESSION['id_siswa'] = (int)$siswa['id_siswa'];
                $_SESSION['nisn'] = $siswa['nisn'];
                $_SESSION['nama_lengkap'] = $siswa['nama_siswa'];
                $_SESSION['nama_siswa'] = $siswa['nama_siswa'];
                $_SESSION['kelas'] = $siswa['kelas'];
                $_SESSION['jurusan'] = $siswa['jurusan'];

                header("Location: siswa_dashboard.php");
                exit;
            }

            $error = "NISN atau password salah.";
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Siswa - Sarpras Sekolah</title>
<link rel="stylesheet" href="./data.css?v=spiderman2026">
<style>
.login-container{max-width:420px;margin:70px auto;padding:28px;background:#fff;border-radius:14px;box-shadow:0 8px 30px rgba(0,0,0,.08)}
.login-container h1{text-align:center;margin:0 0 8px}
.login-container .subtitle{text-align:center;margin-bottom:24px}
.form-group{margin-bottom:16px}
.form-group label{display:block;margin-bottom:7px;font-weight:600}
.form-group input{width:100%;padding:12px;box-sizing:border-box}
.btn-login{width:100%;padding:12px;border:0;border-radius:8px;cursor:pointer}
.error{padding:10px;border-radius:8px;margin-bottom:16px}
.login-links{text-align:center;margin-top:18px;font-size:14px}
</style>
</head>
<body>
<div class="login-container">
    <h1>Login Siswa</h1>
    <p class="subtitle">Masuk untuk membuat laporan dan memantau penanganannya.</p>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="on">
        <div class="form-group">
            <label for="nisn">NISN</label>
            <input
                type="text"
                id="nisn"
                name="nisn"
                value="<?= htmlspecialchars($nisn) ?>"
                inputmode="numeric"
                autocomplete="username"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary btn-login">Login Siswa</button>
    </form>

    <div class="login-links">
        <a href="login.php">Login Admin/Petugas</a>
    </div>
</div>
</body>
</html>
