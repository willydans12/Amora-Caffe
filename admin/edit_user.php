<?php
// admin/edit_user.php
session_start();
require_once __DIR__ . '/../koneksi.php';

// Auth guard: Pastikan user login dan punya role
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?error=auth');
    exit;
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: manajemen-user.php');
    exit;
}

// Definisikan role yang diizinkan
$allowed_roles = ['admin', 'kasir', 'staff', 'user'];

// Ambil data user yang akan diedit
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: manajemen-user.php?status=notfound');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User – <?= htmlspecialchars($user['nama']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet"/>
    <script>
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
            <a href="../logout.php" class="flex items-center w-full px-4 py-2.5 mb-3 rounded-lg text-red-300 hover:bg-red-500 hover:text-white transition-colors duration-200 group">
                <i class="ri-logout-box-r-line text-lg"></i><span class="ml-3 font-medium">Keluar</span>
            </a>
            <div class="flex items-center">
                <?php if (isset($_SESSION['user_profil']) && !empty($_SESSION['user_profil'])): ?>
                    <img src="<?= htmlspecialchars($_SESSION['user_profil']) ?>" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white/30">
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

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-900">✏️ Edit User: <?= htmlspecialchars($user['nama']) ?></h1>
        </header>
        <main class="p-6 flex-1 overflow-y-auto bg-gray-100">
            <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-8">
                <form action="proses_edit.php" method="post" class="space-y-6">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">

                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" required value="<?= htmlspecialchars($user['nama']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required value="<?= htmlspecialchars($user['email']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password Baru <span class="text-xs text-gray-500">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" required value="<?= htmlspecialchars($user['tanggal_lahir']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"><?= htmlspecialchars($user['alamat']) ?></textarea>
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" id="role" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                            <option value="" disabled>-- Pilih Role --</option>
                            <?php foreach($allowed_roles as $r): ?>
                                <option value="<?= htmlspecialchars($r) ?>" <?= $user['role'] === $r ? 'selected' : '' ?>>
                                    <?= ucfirst(htmlspecialchars($r)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                            <option value="1" <?= $user['status'] == 1 ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= $user['status'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Foto Profil Saat Ini</label>
                        <?php if(!empty($user['profil'])): ?>
                            <img src="<?= htmlspecialchars($user['profil']) ?>" alt="Foto Profil" class="mt-2 w-24 h-24 rounded-md object-cover">
                        <?php else: ?>
                            <p class="mt-2 text-sm text-gray-500">Tidak ada foto profil.</p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="profil" class="block text-sm font-medium text-gray-700">URL Foto Profil Baru (Opsional)</label>
                        <input type="url" name="profil" id="profil" value="<?= htmlspecialchars($user['profil'] ?? '') ?>" placeholder="https://example.com/gambar.jpg" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="manajemen-user.php" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-opacity-90">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>