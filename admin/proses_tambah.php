<?php
// admin/proses_tambah.php
require_once __DIR__ . '/../koneksi.php';

// 1. Ambil semua data dari form
$nama           = trim($_POST['nama'] ?? '');
$email          = trim($_POST['email'] ?? '');
$password       = trim($_POST['password'] ?? '');
$tanggal_lahir  = trim($_POST['tanggal_lahir'] ?? '');
$alamat         = trim($_POST['alamat'] ?? '');
$role           = trim($_POST['role'] ?? '');
$profil         = trim($_POST['profil'] ?? ''); // DIUBAH: Ambil data URL profil

// 2. Validasi dasar
if (empty($nama) || empty($email) || empty($password) || empty($tanggal_lahir) || empty($alamat) || empty($role)) {
    die("Error: Semua field wajib diisi, kecuali URL Profil.");
}

// Cek apakah email sudah terdaftar
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
if ($stmt->fetchColumn() > 0) {
    die("Error: Email sudah terdaftar. Gunakan email lain.");
}

// 3. Siapkan data untuk disimpan
// Menggunakan password plaintext sesuai permintaan sebelumnya
$password_to_save = $password;

// Jika input URL profil kosong, simpan sebagai NULL di database
$profil_to_save = !empty($profil) ? $profil : NULL;


// 4. Siapkan dan eksekusi query INSERT
// DIUBAH: Tambahkan kolom 'profil' dan placeholder ':profil'
$sql = "INSERT INTO users (nama, email, password, tanggal_lahir, alamat, role, status, profil)
        VALUES (:nama, :email, :password, :tanggal_lahir, :alamat, :role, 1, :profil)";

$stmt = $pdo->prepare($sql);

try {
    // DIUBAH: Tambahkan parameter ':profil' ke dalam array execute
    $stmt->execute([
        ':nama'           => $nama,
        ':email'          => $email,
        ':password'       => $password_to_save,
        ':tanggal_lahir'  => $tanggal_lahir,
        ':alamat'         => $alamat,
        ':role'           => $role,
        ':profil'         => $profil_to_save,
    ]);

    // 5. Redirect ke halaman manajemen user jika berhasil
    header("Location: manajemen-user.php?status=sukses_tambah");
    exit;

} catch (PDOException $e) {
    die("Error: Gagal menyimpan data ke database. " . $e->getMessage());
}
?>