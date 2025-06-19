<?php
// proses_hapus.php
require_once __DIR__ . '/../koneksi.php';

// 1. Ambil dan validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID tidak valid.');
}
$id = (int) $_GET['id'];

// 2. Hapus user
$sql = "DELETE FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);

// 3. Redirect kembali ke daftar user
header('Location: manajemen-user.php');
exit;
