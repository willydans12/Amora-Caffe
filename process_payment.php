<?php
// process_payment.php
require 'koneksi.php';
session_start();

// 1. Ambil data form
$firstName = trim($_POST['firstName'] ?? '');
$lastName  = trim($_POST['lastName']  ?? '');
$address   = trim($_POST['address']   ?? '');
$phone     = trim($_POST['phone']     ?? '');
$email     = trim($_POST['email']     ?? '');
$shippingCost = floatval($_POST['shipping'] ?? 0); // misalnya value="15000"

// -- Validasi minimal:
if ($firstName === '' || $lastName === '' || $address === '' || $phone === '' || $email === '') {
    die("Semua kolom harus diisi.");
}

// 2. Hitung subtotal & pajak, total
// Ambil semua baris di tabel checkout untuk user saat ini.
// Asumsi: di tabel checkout, kita punya: product_id, product_name, unit_price, quantity, total_price.

$stmtCheckout = $pdo->query("SELECT * FROM checkout");
$checkoutRows = $stmtCheckout->fetchAll(PDO::FETCH_ASSOC);

if (count($checkoutRows) === 0) {
    // Jika keranjang kosong, redirect atau tampilkan pesan error
    die("Keranjang Anda kosong. Tambahkan produk sebelum checkout.");
}

$subtotal = 0;
foreach ($checkoutRows as $row) {
    $subtotal += floatval($row['total_price']); 
}
$tax   = round($subtotal * 0.10, 2);
$total = round($subtotal + $tax + $shippingCost, 2);

// 3. Simpan ke tabel payments
try {
    $sql = "INSERT INTO payments
      (first_name, last_name, address, phone, email, shipping_cost, subtotal, tax, total, payment_method)
      VALUES
      (:first_name, :last_name, :address, :phone, :email, :shipping_cost, :subtotal, :tax, :total, NULL)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':first_name'    => $firstName,
        ':last_name'     => $lastName,
        ':address'       => $address,
        ':phone'         => $phone,
        ':email'         => $email,
        ':shipping_cost' => $shippingCost,
        ':subtotal'      => $subtotal,
        ':tax'           => $tax,
        ':total'         => $total
    ]);

    // Dapatkan ID payments yang baru saja dibuat (jika dibutuhkan)
    $paymentId = $pdo->lastInsertId();

    // 4. (Opsional) Kosongkan atau hapus data di tabel checkout
    // Misalnya kita hapus semua baris checkout:
    $pdo->exec("DELETE FROM checkout");

    // 5. Redirect ke halaman pembayaran berikutnya (misalnya payment_method.php?id=...)
    header("Location: payment_method.php?payment_id=" . $paymentId);
    exit;
}
catch (PDOException $e) {
    echo "Gagal menyimpan payment: " . $e->getMessage();
    exit;
}
