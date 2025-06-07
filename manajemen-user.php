<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen User - Cafe Kopi</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- RemixIcon untuk ikon sidebar -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet" />
  <style>
    /* Agar scrollbar sidebar lebih halus */
    .sidebar::-webkit-scrollbar {
      width: 4px;
    }
    .sidebar::-webkit-scrollbar-thumb {
      background-color: rgba(255,255,255,0.3);
      border-radius: 2px;
    }
  </style>
</head>
<body class="flex h-screen bg-gray-100 overflow-hidden">

  <!-- Sidebar -->
  <div class="sidebar w-64 bg-[#4b2e25] text-white flex flex-col">
    <!-- Header Sidebar -->
    <div class="p-4 flex items-center gap-2 border-b border-white/20">
      <img src="logokopi.jpeg" alt="Logo Amora Cafe" class="w-8 h-8 rounded-full object-cover" />
      <span class="text-lg font-semibold">Amora Cafe</span>
      <button class="ml-auto text-white w-8 h-8 flex items-center justify-center focus:outline-none">
        <i class="ri-menu-line"></i>
      </button>
    </div>

    <!-- Navigasi Sidebar -->
    <nav class="flex-1 overflow-y-auto py-4">
      <div class="space-y-1 px-1">
        <a
          href="dashboard.html"
          class="sidebar-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#3a241d] transition-colors text-white/80 hover:text-white"
        >
          <div class="w-5 h-5 flex items-center justify-center">
            <i class="ri-dashboard-line"></i>
          </div>
          <span class="text-sm">Dashboard</span>
        </a>

        <a
          href="manajemen-user.php"
          class="sidebar-item active flex items-center gap-3 px-4 py-2 rounded-lg bg-[#3a241d] text-white font-medium"
        >
          <div class="w-5 h-5 flex items-center justify-center">
            <i class="ri-user-line"></i>
          </div>
          <span class="text-sm">Manajemen User</span>
        </a>

        <a
          href="Manajemen-Stok-Admin.html"
          class="sidebar-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#3a241d] transition-colors text-white/80 hover:text-white"
        >
          <div class="w-5 h-5 flex items-center justify-center">
            <i class="ri-store-2-line"></i>
          </div>
          <span class="text-sm">Manajemen Stok</span>
        </a>

        
      </div>
    </nav>

    <!-- Footer Sidebar (profil singkat) -->
    <div class="p-4 border-t border-white/20">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white">
          <i class="ri-user-3-line text-xl"></i>
        </div>
        <div>
          <div class="text-white font-medium text-sm">Dilvi Yola</div>
          <div class="text-white/60 text-xs">Admin</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="flex-1 flex flex-col overflow-hidden">
    <!-- Header (bidang kosong bisa ditambahkan search atau notifikasi jika diperlukan) -->
    <header class="bg-white border-b border-gray-200 px-6 py-4">
      <h1 class="text-2xl font-bold text-gray-900">Manajemen User</h1>
    </header>

    <!-- Konten Utama -->
    <main class="flex-1 overflow-y-auto p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Card Utama -->
        <div class="bg-white rounded-2xl p-6 shadow">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Daftar Pengguna</h3>
            <a href="tambah_user.php" class="inline-flex items-center gap-2 px-4 py-2 bg-[#6f4e37] text-white rounded-lg hover:bg-[#5c3c2f] transition">
              <i class="bi bi-person-plus-fill"></i>
              <span>Tambah User</span>
            </a>
          </div>

          <!-- Tabel Pengguna -->
          <div class="overflow-x-auto">
            <table class="min-w-full bg-white divide-y divide-gray-200">
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
                <?php
                $query = mysqli_query($koneksi, "SELECT * FROM users");
                while($row = mysqli_fetch_assoc($query)) {
                ?>
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900"><?= $row['nama']; ?></div>
                    <div class="text-xs text-gray-500"><?= $row['email']; ?></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $row['email']; ?></td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <?php if($row['role'] == 'Admin') { ?>
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-[#8d6e63] text-white">Admin</span>
                    <?php } elseif($row['role'] == 'Kasir') { ?>
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-[#a1887f] text-white">Kasir</span>
                    <?php } else { ?>
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-700">Staff</span>
                    <?php } ?>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $row['alamat']; ?></td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <?php if($row['status'] == 1): ?>
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                    <?php else: ?>
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">Nonaktif</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    <a href="edit_user.php?id=<?= $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mx-1">
                      <i class="ri-edit-line text-lg"></i>
                    </a>
                    <a href="proses_hapus.php?id=<?= $row['id']; ?>" class="text-red-600 hover:text-red-900 mx-1" onclick="return confirm('Hapus user ini?')">
                      <i class="ri-delete-bin-line text-lg"></i>
                    </a>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

          <!-- Jika ingin menambahkan pagination, bisa diletakkan di sini -->
        </div>
      </div>
    </main>
  </div>

</body>
</html>
