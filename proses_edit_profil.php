<?php
session_start();
require_once 'koneksi.php';

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Jika bukan metode POST, tendang keluar
    header('Location: profil.php');
    exit;
}

// 1. Ambil semua data dari form
$id = (int)$_POST['id'];
$nama = trim($_POST['nama']);
$email = trim($_POST['email']);
$profil = trim($_POST['profil']);
$no_telepon = trim($_POST['no_telepon']);
$tanggal_lahir = trim($_POST['tanggal_lahir']);
$alamat = trim($_POST['alamat']);

// Data password (opsional)
$password_lama = trim($_POST['password_lama']);
$password_baru = trim($_POST['password_baru']);
$konfirmasi_password = trim($_POST['konfirmasi_password']);

// Keamanan: pastikan user hanya bisa mengedit profilnya sendiri
if ($id !== (int)$_SESSION['user_id']) {
    die("Error: Anda tidak memiliki izin untuk mengedit profil ini.");
}

// 2. Update Informasi Pribadi
$sql_parts = [];
$params = [];

// Siapkan query utama
$sql = "UPDATE users SET nama = :nama, email = :email, profil = :profil, no_telepon = :no_telepon, tanggal_lahir = :tanggal_lahir, alamat = :alamat";
$params = [
    ':nama' => $nama,
    ':email' => $email,
    ':profil' => !empty($profil) ? $profil : NULL,
    ':no_telepon' => !empty($no_telepon) ? $no_telepon : NULL,
    ':tanggal_lahir' => $tanggal_lahir,
    ':alamat' => $alamat,
];

// 3. Logika Ubah Password (jika diisi)
if (!empty($password_lama) && !empty($password_baru) && !empty($konfirmasi_password)) {
    // Cek apakah password baru cocok dengan konfirmasi
    if ($password_baru !== $konfirmasi_password) {
        header('Location: edit_profil.php?error=password_mismatch');
        exit;
    }

    // Ambil password saat ini dari database untuk verifikasi
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $current_user = $stmt->fetch();

    // Verifikasi password lama (menggunakan plaintext sesuai permintaan)
    if ($password_lama === $current_user['password']) {
        // Jika cocok, tambahkan update password ke query
        $sql .= ", password = :password";
        $params[':password'] = $password_baru; // Simpan password baru (plaintext)
    } else {
        // Jika password lama salah
        header('Location: edit_profil.php?error=wrong_current_password');
        exit;
    }
}

// 4. Finalisasi dan Eksekusi Query
$sql .= " WHERE id = :id";
$params[':id'] = $id;

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Update data session jika nama atau profil diubah, agar langsung terlihat
    $_SESSION['user_nama'] = $nama;
    $_SESSION['user_profil'] = !empty($profil) ? $profil : NULL;

    // Redirect kembali ke halaman profil dengan pesan sukses
    header('Location: profil.php?status=sukses_edit');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Error duplikat email
        header('Location: edit_profil.php?error=email_exists');
    } else {
        die("Error mengupdate data: " . $e->getMessage());
    }
}
?>