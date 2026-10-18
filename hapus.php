<?php
include "auth.php";
include "conection.php";

$id = $_GET['id'] ?? '';

if ($id === '' || !is_numeric($id)) {
    die("ID aspirasi tidak valid. <a href='index.php'>Kembali</a>");
}

$id = (int)$id;

$sql = "DELETE FROM id_aspirasi WHERE id_aspirasi = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query gagal: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: index.php?status=hapus");
    exit;
}

die("Gagal menghapus data: " . $stmt->error);
?>
