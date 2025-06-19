<?php
// admin/manajemen-user.php

session_start();


// Cek apakah 'user_id' ada di dalam session.
// Jika tidak ada, artinya user belum login.
if (!isset($_SESSION['user_id'])) {
    // Arahkan (redirect) user kembali ke halaman login
    header('Location: login.php?error=auth'); // 'auth' artinya butuh otentikasi
    exit; // Pastikan script berhenti dieksekusi setelah redirect
}
require_once __DIR__ . '/../koneksi.php';

// 0) Proses delete jika form POST mengirimkan delete_id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delId = intval($_POST['delete_id']);
    $stmtDel = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmtDel->execute([':id' => $delId]);
    header("Location: manajemen-user.php");
    exit;
}

// 1) Ambil semua user
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen User – Amorad Caffe</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet" />
  <style>
    .sidebar::-webkit-scrollbar { width:4px }
    .sidebar::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.3);border-radius:2px }
  </style>
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
      <a href="manajemen-user.php" class="flex items-center px-4 py-2 mb-1 rounded-lg bg-[#3a241d] text-white font-medium">
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
        </div>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b px-6 py-4">
      <h1 class="text-2xl font-bold">Manajemen User</h1>
    </header>
    <main class="p-6 overflow-y-auto">
      <div class="bg-white rounded-2xl p-6 shadow max-w-7xl mx-auto">
        <div class="flex justify-between mb-6">
          <h3 class="text-xl font-semibold">Daftar Pengguna</h3>
          <a href="tambah_user.php" class="inline-flex items-center gap-2 bg-[#6f4e37] text-white px-4 py-2 rounded-lg hover:bg-[#5c3c2f]">
            <i class="ri-user-add-line"></i><span>Tambah User</span>
          </a>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php foreach($users as $u): ?>
              <tr>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($u['nama']) ?></div>
                  <div class="text-xs text-gray-500"><?= htmlspecialchars($u['email']) ?></div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($u['email']) ?></td>
                <td class="px-6 py-4">
                  <?php
                 
  $role = $u['role'];

  // Tentukan background berdasarkan role
  if ($role === 'Admin') {
    $bg = 'bg-[#8d6e63]';
  } elseif ($role === 'Kasir') {
    $bg = 'bg-[#a1887f]';
  } else {
    // Untuk Staff & Role Custom lainnya
    $bg = 'bg-gray-200';
  }

  // Tentukan warna teks berdasarkan role
  if ($role === 'Admin' || $role === 'Kasir') {
    $clr = 'text-white';
  } else {
    // Untuk Staff & Role Custom lainnya
    $clr = 'text-gray-700';
  }
?>

                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $bg ?> <?= $clr ?>">
                    <?= htmlspecialchars($role) ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($u['alamat']) ?></td>
                <td class="px-6 py-4">
                  <?php if($u['status']): ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                  <?php else: ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">Nonaktif</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-center text-sm">
                  <a href="edit_user.php?id=<?= $u['id'] ?>" class="text-blue-600 hover:text-blue-900 mx-1">
                    <i class="ri-edit-line text-lg"></i>
                  </a>
                  <form method="post" class="inline-block" onsubmit="return confirm('Hapus <?= htmlspecialchars($u['nama']) ?>?')">
                    <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
                    <button type="submit" class="text-red-600 hover:text-red-900 mx-1">
                      <i class="ri-delete-bin-line text-lg"></i>
                    </button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      </div>
    </main>
  </div>
</body>
</html>
