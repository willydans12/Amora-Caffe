<?php
// ======================================================================
// BAGIAN 1: PENGAMBILAN DATA DARI DATABASE (READ DATABASE)
// ======================================================================
session_start();

// Cek otentikasi: pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=auth');
    exit;
}

// Hubungkan ke database dan siapkan variabel
require_once 'koneksi.php'; // Pastikan path ini benar
$user_id = $_SESSION['user_id'];
$user_data = []; // Variabel untuk menampung data user
$riwayat_kunjungan = []; // Variabel untuk menampung riwayat kunjungan

try {
    // Query untuk mengambil semua data lengkap dari user yang sedang login
    $stmtUser = $pdo->prepare("SELECT *, DATE_FORMAT(created_at, '%M %Y') as member_sejak FROM users WHERE id = :id");
    $stmtUser->execute([':id' => $user_id]);
    $user_data = $stmtUser->fetch(PDO::FETCH_ASSOC);

    // Jika data user tidak ada (misal: telah dihapus), paksa logout
    if (!$user_data) {
        session_destroy();
        header('Location: login.php?error=user_not_found');
        exit;
    }

    // DIUBAH: Query untuk mengambil riwayat kunjungan (payments) berdasarkan email user
    $stmtPayments = $pdo->prepare("
        SELECT 
            id, 
            total, 
            status, 
            DATE_FORMAT(created_at, '%e %M %Y') as tanggal_transaksi, 
            DATE_FORMAT(created_at, '%H:%i WIB') as waktu_transaksi,
            payment_method
        FROM payments 
        WHERE email = :email 
        ORDER BY created_at DESC
    ");
    $stmtPayments->execute([':email' => $user_data['email']]);
    $riwayat_kunjungan = $stmtPayments->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Tampilkan pesan error jika koneksi/query gagal
    die("Error: Gagal mengambil data dari database. " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profil <?= htmlspecialchars($user_data['nama']) ?> | Amora Caffe</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="profil.css">
    <style>
      .bg-primary { background-color: #6f4e37; } 
      .text-primary { color: #6f4e37; }
      .text-secondary { color: #A37E63; }
      .progress-bar { background-color: rgba(255, 255, 255, 0.3); border-radius: 999px; height: 8px; overflow: hidden; }
      .progress-bar-fill { background-color: #fff; height: 100%; border-radius: 999px; }
      /* Status Colors */
      .status-paid { color: #16a34a; } /* green-600 */
      .status-pending { color: #d97706; } /* amber-600 */
      .status-failed { color: #dc2626; } /* red-600 */
    </style>
  </head>
  <body>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <header class="bg-white shadow">
            <div class="container mx-auto px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 flex items-center justify-center cursor-pointer" onclick="window.location.href='index.php' ">
                        <i class="ri-arrow-left-line ri-lg text-gray-700"></i>
                    </div>
                    <h1 class="text-xl font-semibold text-gray-800">Profil Saya</h1>
                </div>
                <button class="px-4 py-2 bg-primary text-white rounded" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </header>

        <main class="flex-grow container mx-auto px-4 py-6 max-w-4xl">
            <div class="bg-white rounded p-6 shadow-sm mb-6">
                <div class="flex items-center">
                    <div class="w-20 h-20 rounded-full overflow-hidden flex-shrink-0">
                        <?php if (!empty($user_data['profil'])): ?>
                            <img src="<?= htmlspecialchars($user_data['profil']) ?>" alt="Foto Profil" class="w-full h-full object-cover object-top" />
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center"><i class="ri-user-line text-4xl text-gray-400"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($user_data['nama']) ?></h2>
                        <span class="text-sm text-gray-500">Member sejak <?= htmlspecialchars($user_data['member_sejak'] ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded shadow-sm p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Informasi Pribadi</h3>
                            <button class="px-4 py-2 bg-primary text-white rounded" onclick="window.location.href='edit-profile.php?id=<?= $user_id ?>'">Edit Profil</button>
                        </div>
                        <div class="space-y-4 text-sm">
                            <div class="grid grid-cols-2 gap-4"><div class="text-gray-500">Nama Lengkap</div><div class="font-medium text-gray-800"><?= htmlspecialchars($user_data['nama']) ?></div></div>
                            <div class="grid grid-cols-2 gap-4"><div class="text-gray-500">Email</div><div class="font-medium text-gray-800"><?= htmlspecialchars($user_data['email']) ?></div></div>
                            <div class="grid grid-cols-2 gap-4"><div class="text-gray-500">Nomor Telepon</div><div class="font-medium text-gray-800"><?= htmlspecialchars($user_data['no_telepon'] ?? 'Belum diatur') ?></div></div>
                            <div class="grid grid-cols-2 gap-4"><div class="text-gray-500">Alamat</div><div class="font-medium text-gray-800"><?= htmlspecialchars($user_data['alamat'] ?? 'Belum diatur') ?></div></div>
                            <div class="grid grid-cols-2 gap-4"><div class="text-gray-500">Password</div><div class="font-medium text-gray-800">••••••••</div></div>
                        </div>
                    </div>

                    <div class="bg-white rounded shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Kunjungan</h3>
    <div class="space-y-4">
        <?php if (empty($riwayat_kunjungan)): ?>
            <p class="text-center text-gray-500 py-4">Belum ada riwayat kunjungan.</p>
        <?php else: ?>
            <?php foreach ($riwayat_kunjungan as $kunjungan): ?>
                <div class="border border-gray-100 rounded p-4 hover:shadow-sm transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium"><?= htmlspecialchars($kunjungan['tanggal_transaksi']) ?></div>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($kunjungan['waktu_transaksi']) ?></div>
                            <div class="text-sm mt-1 font-medium status-<?= strtolower(htmlspecialchars($kunjungan['status'])) ?>">
                                Status: <?= ucfirst(htmlspecialchars($kunjungan['status'])) ?>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-primary">Rp <?= number_format($kunjungan['total'], 0, ',', '.') ?></div>
                            
                            <a href="invoice.php?id=<?= $kunjungan['id'] ?>" class="text-sm text-secondary underline mt-1">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</div>

                <div class="space-y-6">
                  <div class="bg-white rounded shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Poin & Rewards</h3>
                    <div class="bg-gradient-to-r from-primary to-yellow-900 rounded-lg p-4 text-white">
                      <div class="text-sm opacity-80">Total Poin</div>
                      <div class="text-3xl font-bold">750</div>
                      <div class="mt-3">
                        <div class="flex justify-between text-sm mb-1">
                          <span>Level VIP</span><span>1000 poin</span>
                        </div>
                        <div class="progress-bar"><div class="progress-bar-fill" style="width: 75%"></div></div>
                        <div class="text-xs mt-1 opacity-80">250 poin lagi untuk naik level</div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </main>
    </div>

    <div id="detailPesanan" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
      </div>
    
    <footer class="bg-[#4b2e25] text-white pt-8 pb-6 relative mt-auto">
        <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div><h5 class="font-bold text-lg mb-3">Amora Caffe</h5><p class="text-sm mb-1">Jl. Sudirman No. 123<br>Jakarta Pusat, 10220</p><p class="text-sm mb-1">Telp: (021) 555-0123</p><p class="text-sm">Email: info@amoradcaffe.com</p></div>
                <div><h5 class="font-bold text-lg mb-3">Jam Buka</h5><p class="text-sm mb-1">Senin – Jumat: 07:00 – 22:00</p><p class="text-sm">Sabtu – Minggu: 08:00 – 23:00</p></div>
                <div><h5 class="font-bold text-lg mb-3">Menu Cepat</h5><ul class="space-y-1 text-sm"><li><a href="menu.php" class="hover:underline">Menu</a></li><li><a href="about.php" class="hover:underline">Tentang Kami</a></li><li><a href="profil.php" class="hover:underline">Profile</a></li><li><a href="contact.php" class="hover:underline">Kontak</a></li></ul></div>
                <div><h5 class="font-bold text-lg mb-3">Ikuti Kami</h5><div class="flex space-x-2"><a href="#" class="w-8 h-8 flex items-center justify-center border border-white rounded-full hover:bg-white hover:text-[#4b2e25] transition"><i class="bi bi-instagram"></i></a><a href="#" class="w-8 h-8 flex items-center justify-center border border-white rounded-full hover:bg-white hover:text-[#4b2e25] transition"><i class="bi bi-facebook"></i></a><a href="#" class="w-8 h-8 flex items-center justify-center border border-white rounded-full hover:bg-white hover:text-[#4b2e25] transition"><i class="bi bi-twitter"></i></a></div></div>
            </div>
            <hr class="border-gray-300 my-6">
            <div class="text-center text-sm">© 2025 Amora Caffe. Hak Cipta Dilindungi.</div>
        </div>
    </footer>
    
    <script src="profil.js"></script>
  </body>
</html>