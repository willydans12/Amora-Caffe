<?php
include 'koneksi.php';
$id     = $_POST['id'];
$nama   = $_POST['nama'];
$email  = $_POST['email'];
$role   = $_POST['role'];
$alamat = $_POST['alamat'];
$status = $_POST['status'];

mysqli_query($koneksi, "UPDATE users SET nama='$nama', email='$email', role='$role', alamat='$alamat', status='$status' WHERE id=$id");
header("Location: index.php");
