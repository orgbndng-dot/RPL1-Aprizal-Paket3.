<?php
$host = "127.0.0.1";
$user = "root";
$pass = "root";
$db   = "12rpl1_aprizal_paket3";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>