<?php
// add_to_cart.php
require 'koneksi.php';
header('Content-Type: application/json');

// Tangkap parameter POST
$product_id   = isset($_POST['product_id'])   ? intval($_POST['product_id'])   : 0;
$product_name = isset($_POST['product_name']) ? trim($_POST['product_name'])   : '';
$unit_price   = isset($_POST['unit_price'])   ? floatval($_POST['unit_price']) : 0.0;

// Validasi dasar
if ($product_id <= 0 || $unit_price <= 0 || $product_name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak valid.']);
    exit;
}

try {
    // Mulai transaksi agar stok & checkout tetap konsisten
    $pdo->beginTransaction();

    // 1) CEK & LOCK stok di tabel products
    $stokStmt = $pdo->prepare("SELECT stock FROM products WHERE id = :pid FOR UPDATE");
    $stokStmt->execute([':pid' => $product_id]);
    $stokRow = $stokStmt->fetch(PDO::FETCH_ASSOC);

    if (!$stokRow) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan.']);
        exit;
    }

    $currentStock = intval($stokRow['stock']);
    if ($currentStock <= 0) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Stok produk habis.']);
        exit;
    }

    // 2) CEK apakah sudah ada di tabel checkout (LOCK juga)
    $chkStmt = $pdo->prepare("
      SELECT id, quantity 
      FROM checkout 
      WHERE product_id = :pid 
      FOR UPDATE
    ");
    $chkStmt->execute([':pid' => $product_id]);
    $chkRow = $chkStmt->fetch(PDO::FETCH_ASSOC);

    if ($chkRow) {
        // UPDATE: tambahkan quantity, hitung total baru
        $checkoutId = intval($chkRow['id']);
        $newQty     = intval($chkRow['quantity']) + 1;
        $newTotal   = $newQty * $unit_price;

        $updStmt = $pdo->prepare("
          UPDATE checkout 
             SET quantity    = :q,
                 total_price = :tp
           WHERE id = :cid
        ");
        $updStmt->execute([
          ':q'   => $newQty,
          ':tp'  => $newTotal,
          ':cid' => $checkoutId
        ]);
    } else {
        // INSERT: baris baru di checkout
        // Gunakan dua placeholder terpisah untuk harga
        $insStmt = $pdo->prepare("
          INSERT INTO checkout 
            (product_id, product_name, unit_price, quantity, total_price)
          VALUES 
            (:pid, :pname, :unit_price, 1, :total_price)
        ");
        $insStmt->execute([
          ':pid'         => $product_id,
          ':pname'       => $product_name,
          ':unit_price'  => $unit_price,
          ':total_price' => $unit_price
        ]);
    }

    // 3) KURANGI stok di tabel products
    $newStock = $currentStock - 1;
    $stkUpd = $pdo->prepare("
      UPDATE products 
         SET stock = :s 
       WHERE id = :pid
    ");
    $stkUpd->execute([
      ':s'   => $newStock,
      ':pid' => $product_id
    ]);

    // Commit transaksi
    $pdo->commit();
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
      'status'  => 'error',
      'message' => $e->getMessage()
    ]);
}
