<?php
// Mulai atau lanjutkan session yang ada
session_start();

// Cek apakah 'user_id' ada di dalam session.
// Jika tidak ada, artinya user belum login.
if (!isset($_SESSION['user_id'])) {
    // Arahkan (redirect) user kembali ke halaman login
    header('Location: login.php?error=auth'); // 'auth' artinya butuh otentikasi
    exit; // Pastikan script berhenti dieksekusi setelah redirect
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Amora Caffe - Hubungi Kami</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-[#fff8ef] text-[#3b2f2f]">

  <!-- Navbar (Tailwind CSS) -->
<nav class="bg-white shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <!-- Brand -->
      <div class="flex-shrink-0">
        <a href="#" class="text-xl font-bold text-[#4b2e25]">Amora Caffe</a>
      </div>

      

      <!-- Menu Items  -->
      <div class="hidden md:flex md:space-x-8">
        <a href="index.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Beranda</a>
        <a href="menu.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Menu</a>
        <a href="about.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Tentang Kami</a>
        <a href="profil.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Profile</a>
        <a href="contact.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Kontak</a>
      </div>

      <!-- Icons (search + cart) -->
      <div class="hidden md:flex md:items-center md:space-x-6">
        <!-- Search Icon -->
        
        <!-- Cart Icon with Badge -->
        <div class="relative">
          <a href="#" class="text-xl text-gray-600 hover:text-yellow-600">
            <i class="bi bi-cart3"></i>
          </a>
          <span class="absolute -top-1 -right-2 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-semibold leading-none text-[#3b2f2f] bg-yellow-400 rounded-full">0</span>
        </div>
      </div>
    </div>
  </div>
</nav>

  <!-- Judul -->
  <section class="text-center mt-10">
    <h2 class="text-4xl font-serif font-bold text-[#4b2e25]">Hubungi Kami</h2>
    <p class="mt-3 text-gray-700 max-w-2xl mx-auto">Kami selalu siap melayani Anda. Jangan ragu untuk menghubungi kami jika memiliki pertanyaan, saran, atau ingin memesan tempat untuk acara spesial.</p>
  </section>

  <!-- Konten Utama -->
  <section class="max-w-6xl mx-auto mt-12 grid md:grid-cols-2 gap-8 bg-white rounded-lg shadow-lg p-8">
    <!-- Info Kontak -->
    <div class="bg-[#4b2e25] text-white p-6 rounded-lg">
      <h3 class="text-xl font-bold mb-4">Informasi Kontak</h3>
      <ul class="space-y-3 text-sm">
        <li><i class="fas fa-map-marker-alt mr-2"></i>Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta, 10220</li>
        <li><i class="fas fa-phone mr-2"></i>+62 812 3456 7890</li>
        <li><i class="fas fa-envelope mr-2"></i>info@amoracaffe.com</li>
        <li><i class="fas fa-clock mr-2"></i>Senin - Jumat: 08.00 - 22.00<br>Sabtu - Minggu: 09.00 - 23.00</li>
      </ul>
      <div class="mt-5">
        <p class="font-semibold mb-2">Ikuti Kami</p>
        <div class="flex space-x-4 text-xl">
          <a href="#"><i class="fab fa-instagram hover:text-yellow-400"></i></a>
          <a href="#"><i class="fab fa-facebook hover:text-yellow-400"></i></a>
          <a href="#"><i class="fab fa-x-twitter hover:text-yellow-400"></i></a>
          <a href="#"><i class="fab fa-whatsapp hover:text-yellow-400"></i></a>
        </div>
      </div>
    </div>

    <!-- Form -->
    <form class="space-y-4">
      <div>
        <label class="block text-sm font-semibold">Nama Lengkap</label>
        <input type="text" placeholder="Masukkan nama lengkap Anda" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" />
      </div>
      <div>
        <label class="block text-sm font-semibold">Email</label>
        <input type="email" placeholder="Masukkan alamat email Anda" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" />
      </div>
      <div>
        <label class="block text-sm font-semibold">Nomor Telepon</label>
        <input type="tel" placeholder="Masukkan nomor telepon Anda" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" />
      </div>
      <div>
        <label class="block text-sm font-semibold">Pesan</label>
        <textarea rows="4" placeholder="Tulis pesan Anda..." class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
      </div>
      <button type="submit" class="bg-[#4b2e25] text-white px-6 py-2 rounded-md hover:bg-[#3a241d]">Kirim Pesan</button>
    </form>
  </section>

  <!-- Google Maps -->
  <section class="max-w-6xl mx-auto mt-10 px-4">
    <h3 class="text-xl font-bold text-center mb-4">Lokasi Kami</h3>
    <iframe class="w-full h-72 rounded-md shadow" loading="lazy" allowfullscreen
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.643488248852!2d106.8235558!3d-6.1775419999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5c2083d9f07%3A0x28cf8656d5b50952!2sJl.%20Sudirman%2C%20Jakarta%20Pusat!5e0!3m2!1sid!2sid!4v1688888888888!5m2!1sid!2sid">
    </iframe>
  </section>

  <!-- FAQ -->
  <section class="max-w-4xl mx-auto mt-16 px-4">
    <h3 class="text-xl font-bold text-center mb-6">Pertanyaan yang Sering Diajukan</h3>
    <div class="space-y-4">
      <details class="bg-white p-4 border rounded-lg">
        <summary class="font-semibold cursor-pointer">Apakah Amora Caffe menerima reservasi?</summary>
        <p class="mt-2 text-sm text-gray-700">Ya, kami menerima reservasi untuk minimal 4 orang. Reservasi dapat dilakukan melalui telepon atau formulir ini.</p>
      </details>
      <details class="bg-white p-4 border rounded-lg">
        <summary class="font-semibold cursor-pointer">Apakah Amora Caffe menyediakan layanan pengiriman?</summary>
        <p class="mt-2 text-sm text-gray-700">Kami bekerja sama dengan layanan pengantaran seperti GoFood dan GrabFood.</p>
      </details>
      <details class="bg-white p-4 border rounded-lg">
        <summary class="font-semibold cursor-pointer">Apakah Amora Caffe menyediakan fasilitas acara pribadi?</summary>
        <p class="mt-2 text-sm text-gray-700">Tentu, kami memiliki area khusus untuk acara seperti ulang tahun, rapat, hingga komunitas.</p>
      </details>
      <details class="bg-white p-4 border rounded-lg">
        <summary class="font-semibold cursor-pointer">Apakah tersedia menu vegetarian atau vegan?</summary>
        <p class="mt-2 text-sm text-gray-700">Ya, kami menyediakan berbagai menu sehat dan ramah vegan.</p>
      </details>
    </div>
  </section>

  <!-- Footer -->
  <!-- Footer (Tailwind CSS) -->
<footer class="bg-[#4b2e25] text-white pt-8 pb-6 relative">
  <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
    <!-- Grid 4 kolom -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Kolom 1: Alamat & Kontak -->
      <div>
        <h5 class="font-bold text-lg mb-3">Amora Caffe</h5>
        <p class="text-sm mb-1">
          Jl. Sudirman No. 123<br>
          Jakarta Pusat, 10220
        </p>
        <p class="text-sm mb-1">Telp: (021) 555-0123</p>
        <p class="text-sm">Email: info@amoradcaffe.com</p>
      </div>

      <!-- Kolom 2: Jam Buka -->
      <div>
        <h5 class="font-bold text-lg mb-3">Jam Buka</h5>
        <p class="text-sm mb-1">Senin – Jumat: 07:00 – 22:00</p>
        <p class="text-sm">Sabtu – Minggu: 08:00 – 23:00</p>
      </div>

      <!-- Kolom 3: Menu Cepat -->
      <div>
        <h5 class="font-bold text-lg mb-3">Menu Cepat</h5>
        <ul class="space-y-1 text-sm">
          <li>
            <a href="menu.php" class="hover:underline">Menu</a>
          </li>
          <li>
            <a href="about.php" class="hover:underline">Tentang Kami</a>
          </li>
          <li>
            <a href="profile.php" class="hover:underline">Profile</a>
          </li>
          <li>
            <a href="contact.php" class="hover:underline">Kontak</a>
          </li>
        </ul>
      </div>

      <!-- Kolom 4: Ikuti Kami -->
      <div>
        <h5 class="font-bold text-lg mb-3">Ikuti Kami</h5>
        <div class="flex space-x-2">
          <a href="#" class="w-8 h-8 flex items-center justify-center border border-white rounded-full hover:bg-white hover:text-[#4b2e25] transition">
            <i class="bi bi-instagram"></i>
          </a>
          <a href="#" class="w-8 h-8 flex items-center justify-center border border-white rounded-full hover:bg-white hover:text-[#4b2e25] transition">
            <i class="bi bi-facebook"></i>
          </a>
          <a href="#" class="w-8 h-8 flex items-center justify-center border border-white rounded-full hover:bg-white hover:text-[#4b2e25] transition">
            <i class="bi bi-twitter"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Garis pemisah -->
    <hr class="border-gray-300 my-6">

    <!-- Copyright -->
    <div class="text-center text-sm">
      © 2025 Amora Caffe. Hak Cipta Dilindungi.
    </div>
  </div>

  
</footer>


</body>
</html>
