<?php
// 1. MANAJEMEN SESI & PENGAMBILAN DATA DARI DATABASE
// ==================================================
session_start();

// Cek otentikasi: pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=auth');
    exit;
}

// Hubungkan ke database dan ambil ID user dari session
require_once 'koneksi.php';
$user_id = $_SESSION['user_id'];

try {
    // Ambil semua data terbaru dari user yang akan diedit
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika user tidak ditemukan di database, paksa logout
    if (!$user) {
        session_destroy();
        header('Location: login.php?error=user_not_found');
        exit;
    }
} catch (PDOException $e) {
    die("Error mengambil data pengguna: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Profil - <?= htmlspecialchars($user['nama']) ?> | Amora Caffe</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="edit-profile.css">
    <style>
      /* Style dari jawaban sebelumnya untuk memastikan tombol dan warna konsisten */
      .btn-primary { background-color: #6f4e37; color: white; }
      .btn-secondary { background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
      .focus\:ring-primary:focus { --tw-ring-color: #6f4e37; }
      .focus\:border-primary:focus { --tw-border-opacity: 1; border-color: #6f4e37; }
      .border-primary { border-color: #6f4e37; }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col bg-gray-50">
      <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center cursor-pointer rounded-full hover:bg-gray-100" onclick="window.location.href='profil.php'" title="Kembali">
              <i class="ri-arrow-left-line ri-lg text-gray-700"></i>
            </div>
            <h1 class="text-xl font-semibold text-gray-800">Edit Profil</h1>
          </div>
        </div>
      </header>

      <main class="flex-grow container mx-auto px-4 py-6 profile-container">
        <form action="proses_edit_profil.php" method="POST">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div class="bg-white rounded-xl p-6 shadow-sm mb-6 form-section">
                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="ri-camera-line mr-2"></i>Foto Profil</h3>
                <div class="flex flex-col md:flex-row items-center">
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-primary mb-4 md:mb-0 flex-shrink-0">
                        <?php if (!empty($user['profil'])): ?>
                            <img src="<?= htmlspecialchars($user['profil']) ?>" alt="Foto Profil" class="w-full h-full object-cover object-top" />
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="ri-user-line text-4xl text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="md:ml-6 w-full">
                        <label for="profil" class="block text-gray-700 mb-2">URL Foto Profil Baru</label>
                        <input type="url" id="profil" name="profil" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-primary focus:border-primary" 
                               placeholder="https://example.com/gambar.jpg" value="<?= htmlspecialchars($user['profil'] ?? '') ?>" />
                        <p class="text-xs text-gray-500 mt-2">Masukkan URL gambar baru di sini untuk mengganti foto profil.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm mb-6 form-section">
                <h3 class="text-lg font-semibold text-gray-800 mb-6"><i class="ri-user-settings-line mr-2"></i>Informasi Pribadi</h3>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-primary focus:border-primary" value="<?= htmlspecialchars($user['nama']) ?>" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-primary focus:border-primary" value="<?= htmlspecialchars($user['email']) ?>" required />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="tel" name="no_telepon" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-primary focus:border-primary" value="<?= htmlspecialchars($user['no_telepon'] ?? '') ?>" />
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-primary focus:border-primary" value="<?= htmlspecialchars($user['tanggal_lahir']) ?>" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Alamat</label>
                        <textarea name="alamat" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-primary focus:border-primary" rows="3"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm mb-6 form-section">
                <h3 class="text-lg font-semibold text-gray-800 mb-6"><i class="ri-lock-password-line mr-2"></i>Keamanan Akun (Opsional)</h3>
                <div class="space-y-6">
                    <div>
                        <label for="password_lama" class="block text-gray-700 mb-2">Password Saat Ini</label>
                        <input id="password_lama" name="password_lama" type="password" class="w-full border border-gray-300 rounded-lg p-3" placeholder="Isi untuk mengubah password" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password_baru" class="block text-gray-700 mb-2">Password Baru</label>
                            <input id="password_baru" name="password_baru" type="password" class="w-full border border-gray-300 rounded-lg p-3" />
                        </div>
                        <div>
                            <label for="konfirmasi_password" class="block text-gray-700 mb-2">Konfirmasi Password Baru</label>
                            <input id="konfirmasi_password" name="konfirmasi_password" type="password" class="w-full border border-gray-300 rounded-lg p-3" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-end gap-3 mt-8">
                <button type="button" class="px-6 py-3 btn-secondary rounded-lg flex items-center justify-center" onclick="window.location.href='profil.php'">
                    <i class="ri-close-line mr-2"></i>Batal
                </button>
                <button type="submit" class="px-6 py-3 btn-primary rounded-lg flex items-center justify-center">
                    <i class="ri-save-line mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
      </main>

      <footer class="bg-[#4b2e25] text-white pt-8 pb-6 relative mt-auto">
        <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div><h5 class="font-bold text-lg mb-3">Amora Caffe</h5><p class="text-sm mb-1">Jl. Sudirman No. 123<br>Jakarta Pusat, 10220</p><p class="text-sm mb-1">Telp: (021) 555-0123</p><p class="text-sm">Email: info@amoradcaffe.com</p></div>
                <div><h5 class="font-bold text-lg mb-3">Jam Buka</h5><p class="text-sm mb-1">Senin – Jumat: 07:00 – 22:00</p><p class="text-sm">Sabtu – Minggu: 08:00 – 23:00</p></div>
                <div><h5 class="font-bold text-lg mb-3">Menu Cepat</h5><ul class="space-y-1 text-sm"><li><a href="menu.php" class="hover:underline">Menu</a></li><li><a href="about.php" class="hover:underline">Tentang Kami</a></li><li><a href="profile.php" class="hover:underline">Profile</a></li><li><a href="contact.php" class="hover:underline">Kontak</a></li></ul></div>
                <div><h5 class="font-bold text-lg mb-3">Ikuti Kami</h5><div class="flex space-x-2"><a href="#" class="w-8 h-8 flex items-center justify-center border border-white rounded-full hover:bg-white hover:text-[#4b2e25] transition"><i class="bi bi-instagram"></i></a><a href="#" class="w-8 h-8 flex items-center justify-center border border-white rounded-full hover:bg-white hover:text-[#4b2e25] transition"><i class="bi bi-facebook"></i></a><a href="#" class="w-8 h-8 flex items-center justify-center border border-white rounded-full hover:bg-white hover:text-[#4b2e25] transition"><i class="bi bi-twitter"></i></a></div></div>
            </div>
            <hr class="border-gray-300 my-6">
            <div class="text-center text-sm">© 2025 Amora Caffe. Hak Cipta Dilindungi.</div>
        </div>
      </footer>
    </div>
    <script src="edit-profile.js"></script>
  </body>
</html>