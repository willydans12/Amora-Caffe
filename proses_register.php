<?php
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $email = trim($_POST['email']);
    // DIUBAH: Menambahkan trim() untuk konsistensi
    $password = trim($_POST['password']); 
    $tanggal_lahir = $_POST['tanggal_lahir'];

    // Validasi dasar
    if (empty($nama) || empty($alamat) || empty($email) || empty($password) || empty($tanggal_lahir)) {
        die("Semua kolom wajib diisi.");
    }

    // Cek apakah email sudah ada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        header('Location: login.php?error=email_exists');
        exit;
    }

    // DIHAPUS: Baris untuk hashing password telah dihapus
    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Siapkan query untuk memasukkan data
    $sql = "INSERT INTO users (nama, alamat, email, password, tanggal_lahir, role, status) 
            VALUES (:nama, :alamat, :email, :password, :tanggal_lahir, 'User', 1)";
    
    $stmt = $pdo->prepare($sql);
    
    // Eksekusi query
    try {
        $stmt->execute([
            ':nama' => $nama,
            ':alamat' => $alamat,
            ':email' => $email,
            // DIUBAH: Menyimpan password asli (plaintext) ke database
            ':password' => $password, 
            ':tanggal_lahir' => $tanggal_lahir
        ]);
        // Redirect ke halaman login dengan pesan sukses
        header('Location: login.php?success=register');
        exit;
    } catch (PDOException $e) {
        die("Error saat registrasi: " . $e->getMessage());
    }
}
?>