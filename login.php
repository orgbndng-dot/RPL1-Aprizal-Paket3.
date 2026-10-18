<?php 
session_start();

if (!empty($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include "conection.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';

    $s = $conn->prepare("SELECT id_user, nama_lengkap, username, password, role FROM users WHERE username=? LIMIT 1");
    $s->bind_param("s", $u);
    $s->execute();
    $user = $s->get_result()->fetch_assoc();

    $valid = $user && (password_verify($p, $user['password']) || hash_equals((string)$user['password'], (string)$p));

    if ($valid) {
        session_regenerate_id(true);
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = (int)$user['id_user'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: index.php");
        exit;
    }

    $error = "Username atau password salah.";

    if ($u === '' || $p === '') {
        $error = "Username dan password wajib diisi."; 
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login • Sarpras Sekolah</title>
    <link rel="stylesheet" href="./data.css?v=spiderman2026">
</head>
<body>
    <div class="login-wrap">
        <div class="form-card login-card">
            <div class="login-logo">
                <div class="brand-icon" style="margin: auto; color: #fff;">S</div>
                <p class="eyebrow" style="margin-top: 15px;">SISTEM INFORMASI SARPRAS</p>
                <h1>Selamat Datang</h1>
                <p class="subtitle">Masuk untuk mengelola aspirasi dan data sekolah.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input name="username" autocomplete="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" autocomplete="current-password" required>
                </div>
                
                <button class="btn btn-primary" style="width: 100%;">Masuk ke Dashboard</button>
            </form>
            
            <p style="margin-top: 16px; text-align: center; font-size: 13px;">
                Siswa? <a href="login_siswa.php">Login siswa di sini</a>
            </p>
        </div>
    </div>
</body>
</html>
