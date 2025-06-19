<?php
// DIUBAH: Memulai session di baris paling atas
session_start();

// DIUBAH: Jika user sudah login, langsung redirect ke halaman yang sesuai
if (isset($_SESSION['user_id'])) {
    // Cek role dari session dan arahkan
    if (in_array($_SESSION['user_role'], ['Admin', 'Kasir', 'Staff'])) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit; // Pastikan script berhenti setelah redirect
}

// DIUBAH: Logika untuk menangani & menampilkan pesan notifikasi
$error_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'email_exists':
            $error_message = 'Email sudah terdaftar. Silakan login.';
            break;
        case 'email_not_found':
            $error_message = 'Email tidak ditemukan.';
            break;
        case 'wrong_password':
            $error_message = 'Password salah.';
            break;
        case 'user_inactive':
            $error_message = 'Akun Anda tidak aktif. Hubungi admin.';
            break;
        default:
            $error_message = 'Terjadi kesalahan. Silakan coba lagi.';
    }
}

$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 'register') {
    $success_message = 'Registrasi berhasil! Silakan login.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Amorad Caffe</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>

    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
        .toggle-btn-active { background-color: #A37E63; color: #ffffff; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'coffee-dark': '#3C2F2F', 'coffee-mid': '#A37E63',
                        'coffee-light': '#EAE0DA', 'coffee-accent': '#C7A17A'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-cover bg-center bg-no-repeat" style="background-image: url('https://asset.kompas.com/crops/AdV6jMsNAYNgyXBb2iMWBu13cYs=/312x108:1608x972/1200x800/data/photo/2021/03/05/6042200d4e753.jpg');">
    <div class="flex items-center justify-center min-h-screen bg-black bg-opacity-40">
        
        <div class="w-full max-w-md p-8 space-y-6 bg-coffee-dark bg-opacity-70 backdrop-blur-xl rounded-2xl shadow-2xl transition-all duration-300">
            
            <div class="text-center text-white">
                <i class="ri-coffee-line text-4xl text-coffee-accent"></i>
                <h1 class="text-4xl font-display mt-2">Amorad Caffe</h1>
                <p class="text-coffee-light mt-1">Satu Cangkir, Ribuan Cerita</p>
            </div>

            <?php if ($error_message): ?>
                <div class="bg-red-500/50 text-white text-center p-2 rounded-full text-sm transition-opacity duration-300" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="bg-green-500/50 text-white text-center p-2 rounded-full text-sm transition-opacity duration-300" role="alert">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <div class="flex justify-center bg-black bg-opacity-20 rounded-full p-1">
                <button id="login-toggle" class="w-1/2 py-2 text-sm font-medium text-white rounded-full transition-colors duration-300 toggle-btn-active">Login</button>
                <button id="register-toggle" class="w-1/2 py-2 text-sm font-medium text-gray-300 rounded-full transition-colors duration-300">Register</button>
            </div>

            <form id="login-form" class="space-y-6" action="proses_login.php" method="POST">
                <div class="relative">
                    <i class="ri-mail-line absolute top-1/2 -translate-y-1/2 left-3 text-coffee-light"></i>
                    <input type="email" name="email" placeholder="Email" required class="w-full pl-10 pr-4 py-2 bg-black bg-opacity-20 border border-coffee-mid/50 rounded-full text-white placeholder:text-coffee-light/70 focus:outline-none focus:ring-2 focus:ring-coffee-accent transition-all">
                </div>
                <div class="relative">
                    <i class="ri-lock-password-line absolute top-1/2 -translate-y-1/2 left-3 text-coffee-light"></i>
                    <input type="password" name="password" placeholder="Password" required class="w-full pl-10 pr-4 py-2 bg-black bg-opacity-20 border border-coffee-mid/50 rounded-full text-white placeholder:text-coffee-light/70 focus:outline-none focus:ring-2 focus:ring-coffee-accent transition-all">
                </div>
                <div class="text-right">
                    <a href="#" class="text-xs text-coffee-light hover:text-coffee-accent hover:underline">Lupa Password?</a>
                </div>
                <button type="submit" class="w-full py-3 bg-coffee-mid text-white font-semibold rounded-full hover:bg-coffee-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-coffee-dark focus:ring-coffee-accent transition-transform duration-200 active:scale-95">
                    LOGIN
                </button>
            </form>

            <form id="register-form" class="space-y-4 hidden" action="proses_register.php" method="POST">
                <div class="relative">
                    <i class="ri-user-line absolute top-1/2 -translate-y-1/2 left-3 text-coffee-light"></i>
                    <input type="text" name="nama" placeholder="Nama Lengkap" required class="w-full pl-10 pr-4 py-2 bg-black bg-opacity-20 border border-coffee-mid/50 rounded-full text-white placeholder:text-coffee-light/70 focus:outline-none focus:ring-2 focus:ring-coffee-accent transition-all">
                </div>
                <div class="relative">
                    <i class="ri-map-pin-line absolute top-1/2 -translate-y-1/2 left-3 text-coffee-light"></i>
                    <input type="text" name="alamat" placeholder="Alamat" required class="w-full pl-10 pr-4 py-2 bg-black bg-opacity-20 border border-coffee-mid/50 rounded-full text-white placeholder:text-coffee-light/70 focus:outline-none focus:ring-2 focus:ring-coffee-accent transition-all">
                </div>
                <div class="relative">
                    <i class="ri-mail-line absolute top-1/2 -translate-y-1/2 left-3 text-coffee-light"></i>
                    <input type="email" name="email" placeholder="Email" required class="w-full pl-10 pr-4 py-2 bg-black bg-opacity-20 border border-coffee-mid/50 rounded-full text-white placeholder:text-coffee-light/70 focus:outline-none focus:ring-2 focus:ring-coffee-accent transition-all">
                </div>
                <div class="relative">
                    <i class="ri-lock-password-line absolute top-1/2 -translate-y-1/2 left-3 text-coffee-light"></i>
                    <input type="password" name="password" placeholder="Password" required class="w-full pl-10 pr-4 py-2 bg-black bg-opacity-20 border border-coffee-mid/50 rounded-full text-white placeholder:text-coffee-light/70 focus:outline-none focus:ring-2 focus:ring-coffee-accent transition-all">
                </div>
                <div class="relative">
                    <i class="ri-calendar-2-line absolute top-1/2 -translate-y-1/2 left-3 text-coffee-light"></i>
                    <input type="text" name="tanggal_lahir" placeholder="Tanggal Lahir" required onfocus="(this.type='date')" onblur="(this.type='text')" class="w-full pl-10 pr-4 py-2 bg-black bg-opacity-20 border border-coffee-mid/50 rounded-full text-white placeholder:text-coffee-light/70 focus:outline-none focus:ring-2 focus:ring-coffee-accent transition-all">
                </div>
                <button type="submit" class="w-full py-3 bg-coffee-mid text-white font-semibold rounded-full hover:bg-coffee-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-coffee-dark focus:ring-coffee-accent transition-transform duration-200 active:scale-95">
                    REGISTER
                </button>
            </form>
        </div>
    </div>

    <script>
        const loginToggle = document.getElementById('login-toggle');
        const registerToggle = document.getElementById('register-toggle');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        loginToggle.addEventListener('click', () => {
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
            loginToggle.classList.add('toggle-btn-active');
            registerToggle.classList.remove('toggle-btn-active');
        });

        registerToggle.addEventListener('click', () => {
            loginForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
            loginToggle.classList.remove('toggle-btn-active');
            registerToggle.classList.add('toggle-btn-active');
        });

        // DIUBAH: Jika ada pesan error atau sukses, tentukan form mana yang harus tampil
        window.addEventListener('DOMContentLoaded', (event) => {
            const params = new URLSearchParams(window.location.search);
            if (params.has('error')) {
                // Jika error berhubungan dengan registrasi, tampilkan form register
                if (params.get('error') === 'email_exists') {
                    registerToggle.click();
                } else {
                    loginToggle.click();
                }
            } else if (params.has('success')) {
                loginToggle.click();
            }
        });
    </script>
</body>
</html>