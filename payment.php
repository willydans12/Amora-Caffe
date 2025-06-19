<?php
// payment.php
session_start();
require 'koneksi.php';

// Ambil semua item di tabel checkout (jika ada) untuk ringkasan pesanan
$sql = "
  SELECT 
    c.product_id,
    c.product_name,
    c.unit_price,
    c.quantity,
    c.total_price,
    p.image_url
  FROM checkout c
  JOIN products p ON p.id = c.product_id
  ORDER BY c.id
";
$stmt    = $pdo->query($sql);
$items    = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung subtotal, pajak, ongkos kirim (Rp 15.000 tetap)
$subtotal      = 0;
foreach ($items as $it) {
    $subtotal += floatval($it['total_price']);
}
$shippingCost  = 15000;                   // ongkir tetap
$tax           = round($subtotal * 0.10); // pajak 10%
$total         = $subtotal + $shippingCost + $tax;

// 2) Periksa apakah sudah ada payment_id di session
$paymentData = null;
if (isset($_SESSION['payment_id'])) {
    $pid = intval($_SESSION['payment_id']);
    if ($pid > 0) {
        $q = $pdo->prepare("SELECT * FROM payments WHERE id = :id");
        $q->execute([':id' => $pid]);
        $paymentData = $q->fetch(PDO::FETCH_ASSOC);
        // Jika record sudah tidak ada, atau sudah berstatus paid, hapus session
        if (!$paymentData || $paymentData['status'] === 'paid') {
            unset($_SESSION['payment_id']);
            $paymentData = null;
        }
    } else {
        unset($_SESSION['payment_id']);
    }
}

// 3) Jika user mengklik “Batal” (cancel) dan memang ada data unpaid, hapus record + session lalu redirect
if (isset($_GET['cancel']) && $paymentData) {
    $pid = intval($paymentData['id']);
    // Hapus record payments
    $del = $pdo->prepare("DELETE FROM payments WHERE id = :id");
    $del->execute([':id' => $pid]);
    unset($_SESSION['payment_id']);
    header("Location: payment.php");
    exit;
}

// 4) Proses form POST jika belum ada paymentData (artinya user belum menyimpan data apa pun)
$errorMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$paymentData) {
    // Ambil input form
    $firstName    = trim($_POST['firstName'] ?? '');
    $lastName     = trim($_POST['lastName']  ?? '');
    $address      = trim($_POST['address']   ?? '');
    $phone        = trim($_POST['phone']     ?? '');
    $email        = trim($_POST['email']     ?? '');
    $shippingCostInput = floatval($_POST['shipping'] ?? 0);

    // Validasi minimal
    if ($firstName === '' || $lastName === '' || $address === '' || $phone === '' || $email === '') {
        $errorMsg = "Semua kolom harus diisi.";
    } elseif (count($items) === 0) {
        $errorMsg = "Keranjang kosong, tidak bisa checkout.";
    } else {
        // Simpan ke tabel payments (status default = 'unpaid')
        try {
            $sqlInsert = "
              INSERT INTO payments
                (first_name, last_name, address, phone, email, shipping_cost, subtotal, tax, total, payment_method, proof_filename, status)
              VALUES
                (:first_name, :last_name, :address, :phone, :email, :shipping_cost, :subtotal, :tax, :total, NULL, NULL, 'unpaid')
            ";
            $st = $pdo->prepare($sqlInsert);
            $st->execute([
                ':first_name'    => $firstName,
                ':last_name'     => $lastName,
                ':address'       => $address,
                ':phone'         => $phone,
                ':email'         => $email,
                ':shipping_cost' => $shippingCostInput,
                ':subtotal'      => $subtotal,
                ':tax'           => $tax,
                ':total'         => $total
            ]);
            $newPaymentId = $pdo->lastInsertId();

            // Simpan ke session
            $_SESSION['payment_id'] = $newPaymentId;
            header("Location: payment.php");
            exit;
        }
        catch (PDOException $e) {
            $errorMsg = "Gagal menyimpan data pembayaran: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Checkout – Amorad Caffe</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#3C2A21',
            secondary: '#967259'
          },
          borderRadius: {
            none: '0px', sm: '4px', DEFAULT: '8px',
            md: '12px', lg: '16px', xl: '20px',
            '2xl': '24px', '3xl': '32px', full: '9999px',
            button: '8px'
          }
        }
      }
    }
  </script>
  <!-- Google Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
    rel="stylesheet"
  />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap"
    rel="stylesheet"
  />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
  />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="payment.css" />
</head>
<body class="bg-gray-100 font-poppins min-h-screen">

  <!-- Navbar (Tailwind) -->
  <nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex-shrink-0">
          <a href="index.php" class="text-2xl font-bold text-[#3C2A21]">Amorad Caffe</a>
        </div>
        <div class="hidden md:flex md:space-x-8">
          <a href="index.php" class="px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Beranda</a>
          <a href="menu.php"  class="px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Menu</a>
          <a href="about.php" class="px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Tentang Kami</a>
          <a href="profil.php" class="px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Profile</a>
          <a href="contact.php" class="px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Kontak</a>
        </div>
        <div class="hidden md:flex md:items-center md:space-x-6">
          <a href="#" class="text-xl text-gray-600 hover:text-yellow-600"><i class="bi bi-search"></i></a>
          <div class="relative">
            <a href="menu.php" class="text-xl text-gray-600 hover:text-yellow-600"><i class="bi bi-cart3"></i></a>
            <span class="absolute -top-1 -right-2 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-semibold leading-none text-[#3b2f2f] bg-yellow-400 rounded-full">
              <?= count($items) ?>
            </span>
          </div>
        </div>
        <div class="md:hidden flex items-center">
          <button
            type="button"
            class="text-gray-600 hover:text-yellow-600 focus:outline-none"
            onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
          >
            <i class="bi bi-list text-2xl"></i>
          </button>
        </div>
      </div>
    </div>
    <!-- Mobile Menu -->
    <div class="md:hidden hidden" id="mobile-menu">
      <div class="px-2 pt-2 pb-3 space-y-1">
        <a href="index.php"  class="block px-3 py-2 text-base font-medium text-[#3b2f2f] hover:text-yellow-600">Beranda</a>
        <a href="menu.php"   class="block px-3 py-2 text-base font-medium text-[#3b2f2f] hover:text-yellow-600">Menu</a>
        <a href="about.php" class="block px-3 py-2 text-base font-medium text-[#3b2f2f] hover:text-yellow-600">Tentang Kami</a>
        <a href="profil.php" class="block px-3 py-2 text-base font-medium text-[#3b2f2f] hover:text-yellow-600">Profile</a>
        <a href="contact.php" class="block px-3 py-2 text-base font-medium text-[#3b2f2f] hover:text-yellow-600">Kontak</a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="container mx-auto px-6 md:px-8 lg:px-16 py-12 max-w-6xl">
    <h1 class="text-3xl md:text-4xl font-semibold text-gray-900 mb-10">Checkout</h1>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- 1) Form “Informasi Pengiriman” (kolom kiri) -->
      <div class="lg:col-span-2 bg-white rounded-md shadow-sm p-6 md:p-8">
        <h2 class="text-xl font-medium text-gray-800 mb-6">Informasi Pengiriman</h2>

        <!-- Tampilkan pesan error jika ada -->
        <?php if (!empty($errorMsg)): ?>
          <div class="mb-4 text-red-600"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <!-- Jika data belum disimpan (paymentData = null), tampilkan form -->
        <?php if (!$paymentData): ?>
          <form action="payment.php" method="post">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">Nama Depan</label>
                <input
                  type="text" name="firstName" id="firstName"
                  class="w-full px-4 py-3 border border-gray-200 rounded focus:border-primary"
                  placeholder="Masukkan nama depan" required
                />
              </div>
              <div>
                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Nama Belakang</label>
                <input
                  type="text" name="lastName" id="lastName"
                  class="w-full px-4 py-3 border border-gray-200 rounded focus:border-primary"
                  placeholder="Masukkan nama belakang" required
                />
              </div>
            </div>

            <div class="mb-6">
              <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
              <textarea
                name="address" id="address" rows="4"
                class="w-full px-4 py-3 border border-gray-200 rounded focus:border-primary"
                placeholder="Masukkan alamat lengkap" required
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                <input
                  type="tel" name="phone" id="phone"
                  class="w-full px-4 py-3 border border-gray-200 rounded focus:border-primary"
                  placeholder="Contoh: 081234567890" required
                />
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input
                  type="email" name="email" id="email"
                  class="w-full px-4 py-3 border border-gray-200 rounded focus:border-primary"
                  placeholder="Contoh: nama@email.com" required
                />
              </div>
            </div>

            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-800 mb-4">Metode Pengiriman</h3>
              <div class="space-y-3">
                <label
                  class="flex items-center p-4 border border-gray-200 rounded cursor-pointer hover:border-primary"
                >
                  <input
                    type="radio" name="shipping" value="15000"
                    class="w-4 h-4 text-primary" checked
                  />
                  <span class="ml-2 flex-1">
                    <span class="block font-medium">Instant Same Day</span>
                    <span class="text-sm text-gray-500">Rp 15.000</span>
                  </span>
                  <span class="text-primary font-medium">Dipilih</span>
                </label>
              </div>
            </div>

            <!-- Tombol Kirim Form -->
            <button
              type="submit"
              class="w-full bg-primary text-white py-4 px-6 rounded-button font-medium hover:bg-opacity-90 transition whitespace-nowrap"
            >
              Bayar Sekarang
            </button>
          </form>

        <?php else: ?>
          <!-- 2) Setelah disimpan: Tampilkan ringkasan data yang baru saja masuk (status masih unpaid) -->
          <div class="space-y-4">
            <div>
              <h3 class="text-lg font-medium text-gray-800">Data Pengiriman Tersimpan</h3>
              <p>Nama: <?= htmlspecialchars($paymentData['first_name'] . ' ' . $paymentData['last_name']) ?></p>
              <p>Alamat: <?= nl2br(htmlspecialchars($paymentData['address'])) ?></p>
              <p>Telepon: <?= htmlspecialchars($paymentData['phone']) ?></p>
              <p>Email: <?= htmlspecialchars($paymentData['email']) ?></p>
              <p>Ongkir: Rp <?= number_format($paymentData['shipping_cost'], 0, ',', '.') ?></p>
            </div>
            <div>
              <h3 class="text-lg font-medium text-gray-800">Ringkasan Harga</h3>
              <ul class="space-y-1">
                <li>Subtotal: Rp <?= number_format($paymentData['subtotal'], 0, ',', '.') ?></li>
                <li>Pajak (10%): Rp <?= number_format($paymentData['tax'], 0, ',', '.') ?></li>
                <li>Ongkir: Rp <?= number_format($paymentData['shipping_cost'], 0, ',', '.') ?></li>
                <li class="font-semibold">Total: Rp <?= number_format($paymentData['total'], 0, ',', '.') ?></li>
              </ul>
            </div>
            <div class="pt-4 flex items-center">
              <a href="payments.php?payment_id=<?= $paymentData['id'] ?>"
                 class="inline-block bg-secondary text-white py-3 px-6 rounded-button font-medium hover:bg-opacity-90 transition">
                Lanjut ke Metode Pembayaran
              </a>
              <a href="payment.php?cancel=1"
                 class="inline-block ml-4 text-red-600 hover:underline">
                Batal
              </a>
            </div>
          </div>
        <?php endif; ?>

      </div>

      <!-- 3) Order Summary (kolom kanan) -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-md shadow-sm p-6 md:p-8 sticky top-6">
          <h2 class="text-xl font-medium text-gray-800 mb-6">Ringkasan Pesanan</h2>

          <?php if (count($items) === 0): ?>
            <div class="text-center text-gray-600 py-12">
              <i class="bi bi-cart-x text-6xl mb-4"></i>
              <p class="text-lg">Keranjang Kosong</p>
            </div>
          <?php else: ?>
            <div class="space-y-4 mb-6">
              <?php foreach ($items as $it): ?>
                <div class="flex items-center space-x-4 pb-4 border-b border-gray-100">
                  <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden">
                    <img
                      src="<?= htmlspecialchars($it['image_url']) ?>"
                      alt="<?= htmlspecialchars($it['product_name']) ?>"
                      class="w-full h-full object-cover"
                    />
                  </div>
                  <div class="flex-1">
                    <h3 class="font-medium"><?= htmlspecialchars($it['product_name']) ?></h3>
                    <p class="text-sm text-gray-500">
                      <?= intval($it['quantity']) ?> x Rp <?= number_format($it['unit_price'], 0, ',', '.') ?>
                    </p>
                  </div>
                  <div class="font-medium">
                    Rp <?= number_format($it['total_price'], 0, ',', '.') ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="space-y-3 pt-2 border-t border-gray-200">
              <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-medium">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Pengiriman</span>
                <span class="font-medium">Rp <?= number_format($shippingCost, 0, ',', '.') ?></span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Pajak (10%)</span>
                <span class="font-medium">Rp <?= number_format($tax, 0, ',', '.') ?></span>
              </div>
              <div class="flex justify-between pt-3 border-t border-gray-200">
                <span class="font-semibold text-lg">Total</span>
                <span class="font-semibold text-lg">Rp <?= number_format($total, 0, ',', '.') ?></span>
              </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
              <div class="flex items-center mb-4">
                <div class="w-8 h-8 flex items-center justify-center text-primary">
                  <i class="ri-gift-line ri-lg"></i>
                </div>
                <h3 class="font-medium ml-2">Punya kode promo?</h3>
              </div>
              <div class="flex">
                <input
                  type="text"
                  class="flex-1 px-4 py-3 border border-gray-200 rounded-l focus:border-primary"
                  placeholder="Masukkan kode promo"
                />
                <button
                  class="bg-primary text-white px-4 py-3 rounded-r-button font-medium hover:bg-opacity-90 transition whitespace-nowrap"
                >
                  Terapkan
                </button>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-[#4b2e25] text-white pt-8 pb-6 relative">
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <h5 class="font-bold text-lg mb-3">Amorad Caffe</h5>
          <p class="text-sm mb-1">
            Jl. Sudirman No. 123<br />
            Jakarta Pusat, 10220
          </p>
          <p class="text-sm mb-1">Telp: (021) 555-0123</p>
          <p class="text-sm">Email: info@amoradcaffe.com</p>
        </div>
        <div>
          <h5 class="font-bold text-lg mb-3">Jam Buka</h5>
          <p class="text-sm mb-1">Senin – Jumat: 07:00 – 22:00</p>
          <p class="text-sm">Sabtu – Minggu: 08:00 – 23:00</p>
        </div>
        <div>
          <h5 class="font-bold text-lg mb-3">Menu Cepat</h5>
          <ul class="space-y-1 text-sm">
            <li><a href="menu.php" class="hover:underline">Menu</a></li>
            <li><a href="about.php" class="hover:underline">Tentang Kami</a></li>
            <li><a href="profil.php" class="hover:underline">Profile</a></li>
            <li><a href="contact.php" class="hover:underline">Kontak</a></li>
          </ul>
        </div>
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
      <hr class="border-gray-300 my-6" />
      <div class="text-center text-sm">© 2025 Amorad Caffe. Hak Cipta Dilindungi.</div>
    </div>
  </footer>

  <!-- Jika Anda ingin membuat file JavaScript khusus, bisa dipanggil di sini -->
  <script src="payment.js"></script>
</body>
</html>
