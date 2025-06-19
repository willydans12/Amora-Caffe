<?php
// Manajemen-Stok-Admin.php
session_start();


// Cek apakah 'user_id' ada di dalam session.
// Jika tidak ada, artinya user belum login.
if (!isset($_SESSION['user_id'])) {
    // Arahkan (redirect) user kembali ke halaman login
    header('Location: login.php?error=auth'); // 'auth' artinya butuh otentikasi
    exit; // Pastikan script berhenti dieksekusi setelah redirect
}
require_once __DIR__ . '/../koneksi.php'; // sesuaikan path

// 0) Hapus produk jika form delete dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delId = intval($_POST['delete_id']);
    $stmtDel = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmtDel->execute([':id' => $delId]);
    header("Location: Manajemen-Stok-Admin.php");
    exit;
}

// 1) Ambil semua kategori unik
$catsStmt   = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $catsStmt->fetchAll(PDO::FETCH_COLUMN);

// 2) Ambil semua produk
$stmt     = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Stok – Amorad Caffe</title>
    <!-- Google Fonts & Remixicon -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
    />
    <!-- Tailwind & Custom CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="Manajemen-Stok-Admin.css" />
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
    <nav class="sidebar-nav flex-1 overflow-y-auto py-4 px-2"> <a href="dashboard.php" class="flex items-center px-4 py-2 mb-1 rounded-lg text-white/80 hover:bg-[#3a241d] hover:text-white transition">
        <i class="ri-dashboard-line text-lg"></i><span class="ml-3">Dashboard</span>
      </a>
      <a href="manajemen-user.php" class="flex items-center px-4 py-2 mb-1 rounded-lg text-white/80 hover:bg-[#3a241d] hover:text-white transition">
        <i class="ri-user-line text-lg"></i><span class="ml-3">Manajemen User</span>
      </a>
      <a href="Manajemen-Stok-Admin.php" class="flex items-center px-4 py-2 mb-1 rounded-lg bg-[#3a241d] text-white font-medium">
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

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <header class="bg-white border-b">
        <div class="flex items-center justify-between px-6 py-3">
          <div class="relative w-80">
            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input id="searchInput" type="text" placeholder="Cari..." class="w-full pl-10 py-2 rounded-lg bg-gray-50 focus:outline-none" />
          </div>
          <div class="flex items-center gap-4">
            <button class="relative">
              <i class="ri-notification-3-line text-gray-600"></i>
              <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center">3</span>
            </button>
            <div class="flex items-center gap-2">
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
                <div class="text-sm font-medium"><?= htmlspecialchars($_SESSION['user_nama']) ?></div>
            </div>
          </div>
        </div>
      </header>

      <!-- Breadcrumb -->
      <div class="bg-white px-6 py-2 text-sm">
        <a href="dashboard.php" class="hover:text-primary">Dashboard</a>
        <i class="ri-arrow-right-s-line inline mx-1"></i>
        <span>Manajemen Stok</span>
      </div>

      <!-- Content -->
      <main class="p-6 overflow-y-auto">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-semibold">Manajemen Stok</h1>
            <a href="edit_stok.php" class="bg-primary text-white px-4 py-2 rounded flex items-center gap-2">
              <i class="ri-add-line"></i> Tambah Stok
            </a>
          </div>

          <!-- Filter Bar -->
          <div class="flex flex-wrap gap-4 mb-6">
            <div class="relative flex-1">
              <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              <input id="searchInput" type="text" placeholder="Cari bahan..." class="w-full pl-10 py-2 rounded-lg border" />
            </div>
            <div class="relative">
              <button id="kategoriBtn" class="px-4 py-2 rounded-lg border flex items-center gap-2">
                Semua Kategori <i class="ri-arrow-down-s-line"></i>
              </button>
              <ul id="kategoriList" class="hidden absolute bg-white shadow rounded mt-1">
                <li><a href="#" data-cat="all" class="block px-4 py-2 hover:bg-gray-100">Semua Kategori</a></li>
                <?php foreach($categories as $cat): ?>
                <li>
                  <a href="#" data-cat="<?= htmlspecialchars(strtolower($cat)) ?>"
                     class="block px-4 py-2 hover:bg-gray-100"><?= htmlspecialchars($cat) ?></a>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
            <button class="px-4 py-2 rounded-lg border flex items-center gap-2">
              <i class="ri-filter-line"></i> Filter
            </button>
            <button class="px-4 py-2 rounded-lg border flex items-center gap-2">
              <i class="ri-download-line"></i> Export
            </button>
          </div>

          <!-- Tabel -->
          <div class="overflow-x-auto">
            <table id="stokTable" class="w-full text-sm text-left">
              <thead class="bg-gray-50">
                <tr>
                  <th class="p-4"><input type="checkbox" id="chkAll" /></th>
                  <th class="px-6 py-3">Nama Bahan</th>
                  <th class="px-6 py-3">Kategori</th>
                  <th class="px-6 py-3">Stok Tersedia</th>
                  <th class="px-6 py-3">Harga Per Unit</th>
                  <th class="px-6 py-3">Tanggal Update</th>
                  <th class="px-6 py-3">Status</th>
                  <th class="px-6 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($products as $p):
                  $stock = (int)$p['stock'];
                  if ($stock === 0)           $cls = 'status-habis';
                  elseif ($stock <= 5)        $cls = 'status-hampir-habis';
                  else                         $cls = 'status-tersedia';
                ?>
                <tr class="table-row"
                    data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
                    data-cat="<?= htmlspecialchars(strtolower($p['category'])) ?>">
                  <td class="p-4"><input type="checkbox" /></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($p['name']) ?></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($p['category']) ?></td>
                  <td class="px-6 py-4"><?= $stock ?></td>
                  <td class="px-6 py-4">Rp <?= number_format($p['price'],0,',','.') ?></td>
                  <td class="px-6 py-4"><?= date('d M Y',strtotime($p['created_at'])) ?></td>
                  <td class="px-6 py-4">
                    <span class="<?= $cls ?> px-2.5 py-1 rounded-full text-xs font-medium">
                      <?= $cls==='status-habis'
                           ? 'Habis'
                           : ($cls==='status-hampir-habis'
                              ? 'Hampir Habis'
                              : 'Tersedia') ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 flex items-center gap-3">
                    <!-- Edit -->
                    <a href="edit_stok.php?id=<?= $p['id'] ?>" class="text-blue-500 hover:text-blue-700">
                      <i class="ri-edit-line"></i>
                    </a>
                    <!-- Delete langsung -->
                    <form method="post" onsubmit="return confirm('Yakin hapus <?= htmlspecialchars($p['name']) ?>?');">
                      <input type="hidden" name="delete_id" value="<?= $p['id'] ?>" />
                      <button type="submit" class="text-red-500 hover:text-red-700">
                        <i class="ri-delete-bin-line"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination (statis contoh) -->
          <div class="flex items-center justify-between mt-6">
            <div class="text-sm">Menampilkan 1 - <?= count($products) ?> dari <?= count($products) ?> data</div>
            <div class="flex items-center gap-1">
              <button class="px-2 py-1 rounded-lg border"><i class="ri-arrow-left-s-line"></i></button>
              <button class="px-3 py-1 rounded-full bg-primary text-white">1</button>
              <button class="px-3 py-1 rounded-lg border">2</button>
              <button class="px-3 py-1 rounded-lg border">3</button>
              <button class="px-2 py-1 rounded-lg border"><i class="ri-arrow-right-s-line"></i></button>
            </div>
          </div>

        </div>
      </main>
    </div>

    <script src="Manajemen-Stok-Admin.js"></script>
  </body>
</html>
