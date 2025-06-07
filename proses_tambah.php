<?php
include 'koneksi.php';
$nama = $_POST['nama'];
$email = $_POST['email'];
$role = $_POST['role'];
$alamat = $_POST['alamat'];

mysqli_query($koneksi, "INSERT INTO users (nama, email, role, alamat) VALUES ('$nama', '$email', '$role', '$alamat')");
header("Location: index.php");
