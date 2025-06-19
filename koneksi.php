<?php
// koneksi.php
$host = 'localhost';
$db   = 'kafe';
$user = 'root';      // sesuaikan dengan user MySQL Anda
$pass = '';          // sesuaikan password-nya
$opt  = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, $opt);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
