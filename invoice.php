<?php
session_start();
require_once 'koneksi.php';

// Keamanan: Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=auth');
    exit;
}

// Ambil ID pesanan dari URL dan ID user dari session
$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

if ($payment_id <= 0) {
    die("Error: ID pesanan tidak valid.");
}

try {
    // Ambil data pembayaran, tapi pastikan pesanan ini milik user yang sedang login
    $stmt = $pdo->prepare("
        SELECT p.*, u.id as user_id
        FROM payments p
        JOIN users u ON p.email = u.email
        WHERE p.id = :payment_id AND u.id = :user_id
    ");
    $stmt->execute([
        ':payment_id' => $payment_id,
        ':user_id' => $user_id
    ]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika invoice tidak ditemukan atau bukan milik user ini, tampilkan error
    if (!$invoice) {
        die("Pesanan tidak ditemukan atau Anda tidak memiliki izin untuk melihatnya.");
    }

} catch (PDOException $e) {
    die("Error mengambil data invoice: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= htmlspecialchars($invoice['id']) ?> | Amora Caffe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet" />
    <style>
      .bg-primary { background-color: #6f4e37; } 
      .text-primary { color: #6f4e37; }
      .border-primary { border-color: #6f4e37; }
      @media print {
        body * { visibility: hidden; }
        .invoice-box, .invoice-box * { visibility: visible; }
        .invoice-box { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none; }
      }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto max-w-3xl my-8 px-4">
        <div class="flex justify-between items-center mb-4 no-print">
            <a href="profil.php" class="text-gray-600 hover:text-primary flex items-center">
                <i class="ri-arrow-left-line mr-2"></i> Kembali ke Profil
            </a>
            <button onclick="window.print()" class="bg-primary text-white px-4 py-2 rounded-md flex items-center hover:bg-opacity-90">
                <i class="ri-printer-line mr-2"></i> Cetak Invoice
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8 lg:p-12 invoice-box">
            <div class="flex justify-between items-start border-b pb-6 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-primary">INVOICE</h1>
                    <p class="text-gray-500">No. Pesanan: #<?= htmlspecialchars($invoice['id']) ?></p>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-semibold">Amora Caffe</h2>
                    <p class="text-sm text-gray-500">Jl. Sudirman No. 123, Jakarta</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2">Ditagihkan Kepada:</h3>
                    <p class="text-gray-600"><?= htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']) ?></p>
                    <p class="text-gray-600"><?= nl2br(htmlspecialchars($invoice['address'])) ?></p>
                    <p class="text-gray-600"><?= htmlspecialchars($invoice['phone']) ?></p>
                    <p class="text-gray-600"><?= htmlspecialchars($invoice['email']) ?></p>
                </div>
                <div class="text-right">
                    <h3 class="font-semibold text-gray-800 mb-2">Detail Pesanan:</h3>
                    <p class="text-gray-600"><strong>Tanggal Pesan:</strong> <?= date('d F Y', strtotime($invoice['created_at'])) ?></p>
                    <p class="text-gray-600"><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($invoice['payment_method'] ?? 'N/A') ?></p>
                    <p class="text-gray-600"><strong>Status:</strong> <span class="font-semibold"><?= ucfirst(htmlspecialchars($invoice['status'])) ?></span></p>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2">Rincian Pembayaran</h3>
                <div class="space-y-3 text-gray-700">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>Rp <?= number_format($invoice['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pajak (PPN 11%)</span>
                        <span>Rp <?= number_format($invoice['tax'], 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Pengiriman</span>
                        <span>Rp <?= number_format($invoice['shipping_cost'], 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between font-bold text-lg text-gray-900 border-t pt-3 mt-3">
                        <span>TOTAL PEMBAYARAN</span>
                        <span class="text-primary">Rp <?= number_format($invoice['total'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <div class="text-center text-gray-500 text-xs mt-12">
                <p>Terima kasih telah melakukan transaksi di Amora Caffe.</p>
            </div>
        </div>
    </div>
</body>
</html>