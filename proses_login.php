<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Cari user berdasarkan email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        // User tidak ditemukan
        header('Location: login.php?error=email_not_found');
        exit;
    }

    // Verifikasi password plaintext
    if ($password === $user['password']) { 
        // Cek status user
        if ($user['status'] != 1) {
            header('Location: login.php?error=user_inactive');
            exit;
        }

        // --- PERBAIKAN UTAMA DI SINI ---
        // Login berhasil, simpan SEMUA data yang dibutuhkan ke session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_profil'] = $user['profil']; // BARIS INI YANG MEMPERBAIKI MASALAH

        // Redirect berdasarkan role
        if (in_array($user['role'], ['Admin', 'Kasir', 'Staff'])) {
            header('Location: admin/dashboard.php');
        } else {
            // Asumsi index.php adalah halaman untuk 'User'
            header('Location: index.php'); 
        }
        exit;
    } else {
        // Password salah
        header('Location: login.php?error=wrong_password');
        exit;
    }
}
?>