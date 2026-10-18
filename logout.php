<?php
session_start();

$role = $_SESSION['role'] ?? '';

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header("Location: " . ($role === 'Siswa' ? 'login_siswa.php' : 'login.php'));
exit;
