<?php
session_start();

// Cek apakah 'user_id' ada di dalam session.
// Jika tidak ada, artinya user belum login.
if (!isset($_SESSION['user_id'])) {
    // Arahkan (redirect) user kembali ke halaman login
    header('Location: login.php?error=auth'); // 'auth' artinya butuh otentikasi
    exit; // Pastikan script berhenti dieksekusi setelah redirect
}
// admin/dashboard.php
require_once __DIR__ . '/../koneksi.php';  // koneksi PDO

// 1) Total User (hanya role = 'User')
$stmtUsers = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = :role");
$stmtUsers->execute(['role' => 'User']);
$totalUsers = $stmtUsers->fetchColumn();

// 2) Total Produk (semua baris)
$totalProducts = $pdo
  ->query("SELECT COUNT(*) FROM products")
  ->fetchColumn();

// 3) Penjualan Bulan Ini
$stmtSales = $pdo->prepare("
    SELECT COALESCE(SUM(`total`), 0)
      FROM payments
     WHERE MONTH(`created_at`) = MONTH(CURDATE())
       AND YEAR(`created_at`)  = YEAR(CURDATE())
");
$stmtSales->execute();
$totalSales = $stmtSales->fetchColumn();

// 4) Stok Menipis (hitung produk dengan stock <= 5)
$lowStockCount = $pdo
  ->query("SELECT COUNT(*) FROM products WHERE stock <= 5")
  ->fetchColumn();

// 5) Detail Produk Stok <=5 (nama, stock, image_url)
$stmtLow = $pdo->query("SELECT name, stock, image_url FROM products WHERE stock <= 5");
$lowStockItems = $stmtLow->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Amora Cafe</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="dashboard.css" />
</head>
<body class="flex h-screen bg-gray-100">
  <aside class="sidebar w-64 bg-[#4b2e25] text-white flex flex-col">
    <div class="p-4 flex items-center border-b border-white/20">
      <img src="/projekteori/logo.jpeg" alt="Logo" class="w-8 h-8 rounded-full object-cover" />
      <span class="ml-2 text-lg font-semibold">Amora Cafe</span>
      <button class="ml-auto p-2 focus:outline-none">
        <i class="ri-menu-line text-xl"></i>
      </button>
    </div>
    <nav class="sidebar-nav flex-1 overflow-y-auto py-4 px-2"> <a href="dashboard.php" class="flex items-center px-4 py-2 mb-1 rounded-lg bg-[#3a241d] text-white font-medium">
        <i class="ri-dashboard-line text-lg"></i><span class="ml-3">Dashboard</span>
      </a>
      <a href="manajemen-user.php" class="flex items-center px-4 py-2 mb-1 rounded-lg text-white/80 hover:bg-[#3a241d] hover:text-white transition">
        <i class="ri-user-line text-lg"></i><span class="ml-3">Manajemen User</span>
      </a>
      <a href="Manajemen-Stok-Admin.php" class="flex items-center px-4 py-2 mb-1 rounded-lg text-white/80 hover:bg-[#3a241d] hover:text-white transition">
        <i class="ri-store-2-line text-lg"></i><span class="ml-3">Manajemen Stok</span>
      </a>
    </nav>

    <div class="p-4 border-t border-white/20">
        <a href="../logout.php" class="flex items-center w-full px-4 py-2.5 mb-3 rounded-lg text-red-300 hover:bg-red-500 hover:text-white transition-colors duration-200 group">
            <i class="ri-logout-box-r-line text-lg text-red-300 group-hover:text-white"></i>
            <span class="ml-3 font-medium">Keluar</span>
        </a>

       <div class="flex items-center">

    <?php // Memeriksa apakah session untuk URL profil ada dan tidak kosong ?>
    <?php if (isset($_SESSION['user_profil']) && !empty($_SESSION['user_profil'])): ?>
        
        <img src="<?= htmlspecialchars($_SESSION['user_profil']) ?>" 
             alt="Foto Profil" 
             class="w-10 h-10 rounded-full object-cover border-2 border-white/30"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >
        <div class="w-10 h-10 bg-white/20 rounded-full items-center justify-center" style="display:none;">
            <i class="ri-user-3-line text-xl text-white"></i>
        </div>

    <?php else: ?>
    
        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
            <i class="ri-user-3-line text-xl text-white"></i>
        </div>

    <?php endif; ?>

    <div class="ml-3">
        <div class="text-sm font-medium"><?= htmlspecialchars($_SESSION['user_nama']) ?></div>
        
        <div class="text-xs text-white/60"><?= htmlspecialchars($_SESSION['user_role']) ?></div>
    </div>
</div>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 overflow-y-auto p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
      <button class="btn-primary">Unduh Laporan</button>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <!-- Total User -->
      <div class="card">
        <h2 class="text-sm text-gray-500">Total User</h2>
        <p class="text-xl font-bold text-gray-900"><?= htmlspecialchars($totalUsers); ?></p>
        <p class="text-green-500 text-sm">—</p>
      </div>
      <!-- Total Produk -->
      <div class="card">
        <h2 class="text-sm text-gray-500">Total Produk</h2>
        <p class="text-xl font-bold text-gray-900"><?= htmlspecialchars($totalProducts); ?></p>
        <p class="text-green-500 text-sm">—</p>
      </div>
      <!-- Penjualan Bulan Ini -->
      <div class="card">
        <h2 class="text-sm text-gray-500">Penjualan Bulan Ini</h2>
        <p class="text-xl font-bold text-gray-900">Rp <?= number_format($totalSales, 0, ',', '.'); ?></p>
        <p class="text-green-500 text-sm">—</p>
      </div>
      <!-- Stok Menipis -->
      <div class="card">
        <h2 class="text-sm text-gray-500">Stok Menipis</h2>
        <p class="text-xl font-bold text-gray-900"><?= htmlspecialchars($lowStockCount); ?></p>
        <p class="text-red-500 text-sm">Perlu perhatian</p>
      </div>
    </div>

    <!-- Grafik -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
      <div class="card">
        <h2 class="font-bold text-gray-900 mb-2">Penjualan Bulanan</h2>
        <canvas id="salesChart"></canvas>
      </div>
      <div class="card">
        <h2 class="font-bold text-gray-900 mb-2">Produk Terlaris</h2>
        <canvas id="productChart"></canvas>
      </div>
    </div>

    <!-- Aktivitas & Stok Menipis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
      <div class="card">
        <h2 class="font-bold text-gray-900 mb-2">Aktivitas Terbaru</h2>
        <ul class="text-sm text-gray-700 space-y-1">
          <li>✔️ Pengguna baru ditambahkan</li>
          <li>➕ Produk baru ditambahkan</li>
          <li>📦 Stok diperbarui</li>
          <li>❌ Produk dihapus</li>
        </ul>
      </div>
      <div class="card">
        <h2 class="font-bold text-gray-900 mb-2">Produk dengan Stok Menipis</h2>
        <ul class="text-sm text-gray-700 space-y-2">
          <?php foreach($lowStockItems as $item): ?>
          <li class="flex items-center">
            <img src="<?= htmlspecialchars($item['image_url']); ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="w-6 h-6 rounded-full mr-2 object-cover" />
            <span class="flex-1"><?= htmlspecialchars($item['name']); ?></span>
            <span class="font-medium text-gray-900"><?= htmlspecialchars($item['stock']); ?> kg</span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="dashboard.js"></script>
</body>
</html>
