<?php
// admin/proses_edit.php
session_start();
require_once __DIR__ . '/../koneksi.php';

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?error=auth');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Ambil semua data dari form
    $id             = (int)($_POST['id'] ?? 0);
    $nama           = trim($_POST['nama'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $password       = trim($_POST['password'] ?? ''); // Password baru (jika diisi)
    $tanggal_lahir  = trim($_POST['tanggal_lahir'] ?? '');
    $alamat         = trim($_POST['alamat'] ?? '');
    $role           = trim($_POST['role'] ?? '');
    $status         = (int)($_POST['status'] ?? 0);
    $profil         = trim($_POST['profil'] ?? ''); // DIUBAH: Ambil URL profil

    // 2. Validasi dasar
    if ($id <= 0) {
        die("Error: ID user tidak valid.");
    }
    // ... validasi lainnya bisa ditambahkan ...

    // 3. Bangun query UPDATE secara dinamis
    $sql = "UPDATE users SET
                nama          = :nama,
                email         = :email,
                tanggal_lahir = :tanggal_lahir,
                alamat        = :alamat,
                role          = :role,
                status        = :status,
                profil        = :profil"; // DIUBAH: Tambahkan 'profil' ke query

    // Siapkan parameter dasar
    $params = [
        ':id'            => $id,
        ':nama'          => $nama,
        ':email'         => $email,
        ':tanggal_lahir' => $tanggal_lahir,
        ':alamat'        => $alamat,
        ':role'          => $role,
        ':status'        => $status,
        ':profil'        => !empty($profil) ? $profil : NULL, // DIUBAH: Tambahkan parameter profil
    ];

    // Cek apakah user memasukkan password baru
    if (!empty($password)) {
        // Jika ya, tambahkan query untuk update password dan parameternya
        $sql .= ", password = :password";
        $params[':password'] = $password; // Menggunakan plaintext sesuai permintaan
    }

    // Tambahkan klausa WHERE di akhir
    $sql .= " WHERE id = :id";

    try {
        // 4. Eksekusi query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // 5. Redirect kembali
        header("Location: manajemen-user.php?status=sukses_edit");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            die("Error: Email '{$email}' sudah digunakan oleh user lain.");
        } else {
            die("Error saat mengupdate data: " . $e->getMessage());
        }
    }
}
?>