<?php
// payment_method.php
require 'koneksi.php';

$paymentId = intval($_GET['payment_id'] ?? 0);
if ($paymentId <= 0) {
    die("ID payment tidak valid.");
}

// Ambil data payment yang sudah dibuat
$stmt = $pdo->prepare("SELECT * FROM payments WHERE id = :id");
$stmt->execute([':id' => $paymentId]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$payment) {
    die("Payment tidak ditemukan.");
}

// Jika form dikirim (metode pembayaran dipilih)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = trim($_POST['payment_method'] ?? '');
    if ($method === '') {
        $error = "Silakan pilih metode pembayaran.";
    } else {
        // Update ke database
        $upd = $pdo->prepare("UPDATE payments SET payment_method = :method WHERE id = :id");
        $upd->execute([
            ':method' => $method,
            ':id'     => $paymentId
        ]);
        // Misalnya redirect ke halaman “invoice” atau “terima kasih”
        header("Location: success.php?payment_id=" . $paymentId);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pilih Metode Pembayaran – Amorad Caffe</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container py-5">
    <h3 class="mb-4">Metode Pembayaran</h3>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Pilih Metode:</label>
        <select name="payment_method" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="Transfer Bank">Transfer Bank</option>
          <option value="OVO">OVO</option>
          <option value="GoPay">GoPay</option>
          <option value="Dana">Dana</option>
          <option value="Cash on Delivery">Cash on Delivery</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Bayar</button>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
