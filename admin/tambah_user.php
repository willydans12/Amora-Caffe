<?php
// admin/tambah_user.php
require_once __DIR__ . '/../koneksi.php';

// Menentukan role yang diizinkan untuk dropdown
$allowed_roles = ['admin', 'kasir', 'staff', 'user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User Baru – Amorad Caffe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet" />
    <script>
        // Konfigurasi custom color untuk Tailwind CSS
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6f4e37',
                        sidebar: '#4b2e25',
                        sidebarHover: '#3a241d'
                    }
                }
            }
        }
    </script>
</head>
<body class="flex h-screen overflow-hidden bg-gray-100 font-sans">

    <div class="sidebar w-64 bg-sidebar text-white flex flex-col">
        <div class="p-4 flex items-center gap-2 border-b border-white/20">
            <img src="../logo.jpeg" class="w-8 h-8 rounded-full" alt="Logo Amorad Caffe" />
            <span class="text-lg font-semibold">Amorad Caffe</span>
        </div>
        <nav class="flex-1 py-4 overflow-y-auto px-2">
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-2.5 mb-1 rounded-lg hover:bg-sidebarHover">
                <i class="ri-dashboard-line"></i><span class="text-sm">Dashboard</span>
            </a>
            <a href="manajemen-user.php" class="bg-sidebarHover flex items-center gap-3 px-4 py-2.5 mb-1 rounded-lg">
                <i class="ri-user-line"></i><span class="text-sm">Manajemen User</span>
            </a>
            <a href="Manajemen-Stok-Admin.php" class="flex items-center gap-3 px-4 py-2.5 mb-1 rounded-lg hover:bg-sidebarHover">
                <i class="ri-store-2-line"></i><span class="text-sm">Manajemen Stok</span>
            </a>
        </nav>
        <div class="p-4 border-t border-white/20">
           </div>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-900">➕ Tambah User Baru</h1>
        </header>

        <main class="p-6 flex-1 overflow-y-auto bg-gray-100">
            <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-8">
                <form action="proses_tambah.php" method="post" class="space-y-6">

                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>

                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>
                    
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" id="role" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                            <option value="" disabled selected>-- Pilih Role --</option>
                            <?php foreach ($allowed_roles as $r): ?>
                                <option value="<?= htmlspecialchars($r) ?>"><?= ucfirst(htmlspecialchars($r)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="profil" class="block text-sm font-medium text-gray-700">URL Foto Profil (Opsional)</label>
                        <input type="url" name="profil" id="profil" placeholder="https://example.com/gambar.jpg" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="manajemen-user.php" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Simpan User</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

</body>
</html>