<?php
// payments.php
session_start();
require 'koneksi.php';

// 1) Pastikan user sudah melewati checkout dan memiliki payment_id di session
if (!isset($_SESSION['payment_id']) || !is_numeric($_SESSION['payment_id'])) {
    header("Location: payment.php");
    exit;
}
$payment_id = intval($_SESSION['payment_id']);

// 2) Ambil data payment (dari tabel payments)
$stmtP = $pdo->prepare("SELECT * FROM payments WHERE id = :id LIMIT 1");
$stmtP->execute([':id' => $payment_id]);
$paymentData = $stmtP->fetch(PDO::FETCH_ASSOC);

if (!$paymentData) {
    unset($_SESSION['payment_id']);
    header("Location: payment.php");
    exit;
}

// 3) Ambil item‐item di tabel checkout untuk ringkasan pesanan
$sqlItems = "
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
$itemsStmt = $pdo->query($sqlItems);
$items     = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung subtotal, pajak, ongkir, total (data di‐load dari payments)
$subtotal    = floatval($paymentData['subtotal']);
$shipping    = floatval($paymentData['shipping_cost']);
$tax         = floatval($paymentData['tax']);
$totalAmount = floatval($paymentData['total']);

$successMsg = "";
$errorMsg   = "";

// 4) Jika form Konfirmasi Pembayaran disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    // a) Ambil payment_method
    $method  = $_POST['payment_method'] ?? '';
    $allowed = ['bank', 'ewallet', 'qris', 'credit'];
    if (!in_array($method, $allowed, true)) {
        $errorMsg = "Metode pembayaran tidak valid.";
    }

    // b) Jika metode “bank” atau “ewallet”, wajib upload bukti
    $proofFilename = null;
    if (empty($errorMsg) && in_array($method, ['bank','ewallet'])) {
        // Pastikan form memiliki enctype="multipart/form-data"
        if (!isset($_FILES['proofUpload']) || $_FILES['proofUpload']['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = "Anda harus mengupload bukti pembayaran.";
        } else {
            // Validasi jenis file (jpg/png/pdf) dan ukuran (maks 2MB)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $_FILES['proofUpload']['tmp_name']);
            finfo_close($finfo);

            $allowedMimes = ['image/jpeg','image/png','application/pdf'];
            if (!in_array($mime, $allowedMimes, true)) {
                $errorMsg = "Format file tidak diperbolehkan. (JPG, PNG, PDF saja)";
            } elseif ($_FILES['proofUpload']['size'] > 2 * 1024 * 1024) {
                $errorMsg = "Ukuran file maksimal 2MB.";
            } else {
                // Simpan file ke folder uploads/
                $ext = pathinfo($_FILES['proofUpload']['name'], PATHINFO_EXTENSION);
                // Buat nama file unik: proof_{payment_id}_{uniqid()}.{ext}
                $newName     = "proof_{$payment_id}_" . uniqid() . "." . strtolower($ext);
                $destination = __DIR__ . "/uploads/" . $newName;
                if (!move_uploaded_file($_FILES['proofUpload']['tmp_name'], $destination)) {
                    $errorMsg = "Gagal menyimpan file bukti. Silakan coba lagi.";
                } else {
                    // Berhasil memindahkan: simpan nama file saja
                    $proofFilename = $newName;
                }
            }
        }
    }

    // c) Setelah validasi, jika tidak ada error → update payments & hapus checkout
    if (empty($errorMsg)) {

        $sqlUpdate = "
          UPDATE payments
          SET payment_method = :method,
              proof_filename = :proof,
              status         = 'paid'
          WHERE id = :id
        ";
        $upd = $pdo->prepare($sqlUpdate);
        $upd->bindValue(':method', $method);
        // Jika $proofFilename = null, maka proof_filename akan di‐set ke NULL di DB
        if ($proofFilename !== null) {
            $upd->bindValue(':proof', $proofFilename, PDO::PARAM_STR);
        } else {
            // Jika kredit/QRIS dan tidak perlu bukti, kita bind sebagai NULL
            $upd->bindValue(':proof', null, PDO::PARAM_NULL);
        }
        $upd->bindValue(':id', $payment_id, PDO::PARAM_INT);

        // Execute & periksa hasil
        if (!$upd->execute()) {
            // Bila terjadi kesalahan SQL (misalnya kolom proof_filename tidak ada), tangkap errornya:
            $arrErr = $upd->errorInfo(); 
            $errorMsg = "Gagal mengupdate database: " . htmlspecialchars($arrErr[2]);
        } else {
            // Hapus semua item di tabel checkout (keranjang dikosongkan)
            $pdo->exec("DELETE FROM checkout");

            $successMsg = "Pembayaran berhasil dikonfirmasi. Terima kasih!";
            // Reload data paymentData
            $stmtP->execute([':id' => $payment_id]);
            $paymentData = $stmtP->fetch(PDO::FETCH_ASSOC);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Metode Pembayaran | Amorad Caffe</title>

  <!-- Google Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"/>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body { font-family: 'Poppins', sans-serif; }
    /* Highlight border & inner circle jika radio checked */
    .payment-option > input:checked + .outer { border-color: #3C2A21 !important; }
    .payment-option > input:checked + .outer .inner { background-color: #3C2A21 !important; }
  </style>
</head>
<body class="min-h-screen bg-gray-50">

  <!-- Navbar (Tailwind) -->
  <nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex-shrink-0">
          <a href="index.php" class="text-xl font-bold text-[#4b2e25]">Amorad Caffe</a>
        </div>
        <div class="hidden md:flex md:space-x-8">
          <a href="index.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Beranda</a>
          <a href="menu.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Menu</a>
          <a href="about.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Tentang Kami</a>
          <a href="profil.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Profile</a>
          <a href="contact.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-[#3b2f2f] hover:text-yellow-600">Kontak</a>
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
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="container mx-auto px-6 md:px-8 lg:px-16 py-12 max-w-6xl grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Bagian Kiri: Metode Pembayaran -->
    <div class="lg:col-span-2 space-y-8">

      <!-- Pesan Sukses / Error -->
      <?php if (!empty($successMsg)): ?>
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4">
          <?= htmlspecialchars($successMsg) ?>
        </div>
      <?php elseif (!empty($errorMsg)): ?>
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
          <?= htmlspecialchars($errorMsg) ?>
        </div>
      <?php endif; ?>

      <!-- Jika paymentData masih “unpaid”, tampilkan form -->
      <?php if ($paymentData['status'] !== 'paid'): ?>
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Metode Pembayaran</h2>
        <!-- PENTING: enctype="multipart/form-data" harus ada agar $_FILES terbaca -->
        <form action="payments.php" method="post" enctype="multipart/form-data">

          <!-- ===== Transfer Bank ===== -->
          <label class="flex items-center border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-primary payment-option">
            <input type="radio" name="payment_method" value="bank" class="hidden" <?= $paymentData['payment_method'] === 'bank' ? 'checked' : '' ?> />
            <div class="outer w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center mr-4">
              <div class="inner w-3 h-3 bg-transparent rounded-full"></div>
            </div>
            <span class="flex-1 text-gray-800 font-medium">Transfer Bank</span>
            <div class="flex space-x-2">
              <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">BCA</span>
              <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">BNI</span>
              <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">Mandiri</span>
            </div>
          </label>

          <!-- ===== E-Wallet ===== -->
          <label class="flex items-center border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-primary mt-3 payment-option">
            <input type="radio" name="payment_method" value="ewallet" class="hidden" <?= $paymentData['payment_method'] === 'ewallet' ? 'checked' : '' ?> />
            <div class="outer w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center mr-4">
              <div class="inner w-3 h-3 bg-transparent rounded-full"></div>
            </div>
            <span class="flex-1 text-gray-800 font-medium">E-Wallet</span>
            <div class="flex space-x-2">
              <i class="ri-wallet-3-fill text-purple-600 ri-lg"></i>
              <i class="ri-wallet-fill text-green-600 ri-lg"></i>
              <i class="ri-wallet-2-fill text-blue-600 ri-lg"></i>
            </div>
          </label>

          <!-- ===== QRIS ===== -->
          <label class="flex items-center border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-primary mt-3 payment-option">
            <input type="radio" name="payment_method" value="qris" class="hidden" <?= $paymentData['payment_method'] === 'qris' ? 'checked' : '' ?> />
            <div class="outer w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center mr-4">
              <div class="inner w-3 h-3 bg-transparent rounded-full"></div>
            </div>
            <span class="flex-1 text-gray-800 font-medium">QRIS</span>
            <i class="ri-qr-code-fill text-primary ri-lg"></i>
          </label>

          <!-- ===== Kartu Kredit ===== -->
          <label class="flex items-center border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-primary mt-3 payment-option">
            <input type="radio" name="payment_method" value="credit" class="hidden" <?= $paymentData['payment_method'] === 'credit' ? 'checked' : '' ?> />
            <div class="outer w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center mr-4">
              <div class="inner w-3 h-3 bg-transparent rounded-full"></div>
            </div>
            <span class="flex-1 text-gray-800 font-medium">Kartu Kredit</span>
            <div class="flex space-x-2">
              <i class="ri-visa-fill text-blue-600 ri-lg"></i>
              <i class="ri-mastercard-fill text-orange-600 ri-lg"></i>
            </div>
          </label>

          <!-- ===== Detail Transfer Bank ===== -->
          <div id="bankDetails" class="hidden bg-white rounded-lg shadow-md p-6 border border-gray-200 mt-6">
            <h3 class="text-xl font-semibold text-[#3C2A21] mb-4">Detail Pembayaran Bank Transfer</h3>
            <div class="space-y-4">
              <div class="flex justify-between">
                <span class="text-gray-600">Bank</span>
                <span class="font-medium">Bank Central Asia (BCA)</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600">Nomor Rekening</span>
                <div class="flex items-center">
                  <span class="font-medium mr-2">8723456789</span>
                  <button id="copyAccount" type="button" class="text-[#3C2A21] hover:text-[#372018]">
                    <i class="ri-file-copy-line ri-lg"></i>
                  </button>
                </div>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Atas Nama</span>
                <span class="font-medium">PT Amora Caffe Indonesia</span>
              </div>
            </div>

            <!-- Countdown Timer -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
              <div class="flex items-center">
                <i class="ri-time-line text-yellow-600 ri-lg mr-2"></i>
                <div>
                  <p class="text-sm text-yellow-800">Harap selesaikan pembayaran dalam</p>
                  <p id="countdown" class="font-medium text-yellow-800">23:59:45</p>
                </div>
              </div>
            </div>

            <!-- Container untuk upload file -->
            <div id="uploadContainer" class="mt-6"></div>
          </div>

          <!-- ===== Detail E-Wallet ===== -->
          <div id="ewalletDetails" class="hidden bg-white rounded-lg shadow-md p-6 border border-gray-200 mt-6">
            <h3 class="text-xl font-semibold text-[#3C2A21] mb-4">Detail Pembayaran E-Wallet</h3>
            <p class="text-gray-600">Silakan scan atau buka aplikasi E-Wallet (GoPay, OVO, Dana, dll) <br> lalu transfer ke nomor: <strong>0812-3456-7890</strong></p>
            <div id="uploadContainerEwallet" class="mt-6"></div>
          </div>

          <!-- ===== Detail QRIS ===== -->
          <div id="qrisDetails" class="hidden bg-white rounded-lg shadow-md p-6 border border-gray-200 mt-6">
            <h3 class="text-xl font-semibold text-[#3C2A21] mb-4">Detail Pembayaran QRIS</h3>
            <div class="flex justify-center mb-4">
              <img src="qris_sample.png" alt="QRIS" class="w-40 h-40 object-contain" />
            </div>
            <p class="text-gray-600 text-center">Scan QR di atas dengan aplikasi perbankan atau e-Wallet Anda.</p>
            <div id="uploadContainerQris" class="mt-6"></div>
          </div>

          <!-- ===== Detail Kartu Kredit ===== -->
          <div id="creditDetails" class="hidden bg-white rounded-lg shadow-md p-6 border border-gray-200 mt-6">
            <h3 class="text-xl font-semibold text-[#3C2A21] mb-4">Detail Pembayaran Kartu Kredit</h3>
            <p class="text-gray-600">Data kartu kredit akan diminta pada halaman berikutnya.</p>
            <!-- Tidak ada upload di sini karena metode kredit tidak memerlukan bukti -->
          </div>

          <!-- Tombol Konfirmasi Pembayaran -->
          <button name="confirm" type="submit" class="mt-6 w-full bg-[#3C2A21] text-white py-3 rounded-button font-medium hover:bg-opacity-90 transition">
            Konfirmasi Pembayaran
          </button>
        </form>
      </div>
      <?php else: ?>
        <!-- Jika status = paid, tampilkan pesan sukses saja -->
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
          <i class="ri-check-line ri-lg mr-2"></i> Pembayaran Anda telah dikonfirmasi. Terima kasih!
        </div>
      <?php endif; ?>

    </div>

    <!-- Bagian Kanan: Ringkasan Pesanan -->
    <div class="space-y-6">
      <div class="bg-white rounded-lg shadow-md p-6 sticky top-16">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Ringkasan Pesanan</h2>
        <div class="space-y-4 mb-6">
          <?php if (count($items) === 0): ?>
            <div class="text-center text-gray-600 py-12">
              <i class="bi bi-cart-x text-6xl mb-4"></i>
              <p class="text-lg">Keranjang Kosong</p>
            </div>
          <?php else: ?>
            <?php foreach ($items as $it): ?>
              <div class="flex items-center space-x-4 pb-4 border-b border-gray-100">
                <div class="w-16 h-16 bg-gray-100 rounded-md overflow-hidden">
                  <img
                    src="<?= htmlspecialchars($it['image_url']) ?>"
                    alt="<?= htmlspecialchars($it['product_name']) ?>"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div class="flex-1">
                  <h3 class="font-medium text-gray-800"><?= htmlspecialchars($it['product_name']) ?></h3>
                  <p class="text-sm text-gray-500">
                    <?= intval($it['quantity']) ?> x Rp <?= number_format($it['unit_price'], 0, ',', '.') ?>
                  </p>
                </div>
                <div class="font-medium text-gray-800">
                  Rp <?= number_format($it['total_price'], 0, ',', '.') ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Ringkasan Total -->
        <div class="space-y-3 mb-6">
          <div class="flex justify-between">
            <span class="text-gray-600">Subtotal</span>
            <span class="font-medium text-gray-800">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Pengiriman</span>
            <span class="font-medium text-gray-800">Rp <?= number_format($shipping, 0, ',', '.') ?></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Pajak (10%)</span>
            <span class="font-medium text-gray-800">Rp <?= number_format($tax, 0, ',', '.') ?></span>
          </div>
        </div>
        <div class="border-t border-gray-200 pt-4">
          <div class="flex justify-between font-semibold text-lg">
            <span>Total</span>
            <span class="text-gray-900">Rp <?= number_format($totalAmount, 0, ',', '.') ?></span>
          </div>
        </div>

        <!-- Info Tambahan -->
        <div class="mt-6 pt-6 border-t border-gray-200 space-y-4">
          <div class="flex items-center">
            <i class="ri-information-line text-gray-600 ri-lg mr-2"></i>
            <p class="text-sm text-gray-600">Pesanan akan diproses setelah pembayaran dikonfirmasi.</p>
          </div>
          <div class="flex items-center">
            <i class="ri-shield-check-line text-gray-600 ri-lg mr-2"></i>
            <p class="text-sm text-gray-600">Pembayaran aman & terenkripsi.</p>
          </div>
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
            Jl. Sudirman No. 123<br>
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

  <!-- File JavaScript untuk interaksi (payments.js) -->
  <script src="payments.js"></script>
</body>
</html>
