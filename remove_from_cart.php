<?php
// remove_from_cart.php
require 'koneksi.php';
header('Content-Type: application/json');

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak valid.']);
    exit;
}

try {
    // Mulai transaksi
    $pdo->beginTransaction();

    // 1) Ambil entri checkout (LOCK) berdasar product_id
    $stmt = $pdo->prepare("
      SELECT id, quantity, unit_price 
      FROM checkout 
      WHERE product_id = :pid 
      FOR UPDATE
    ");
    $stmt->execute([':pid' => $product_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan di keranjang.']);
        exit;
    }

    $checkoutId = intval($row['id']);
    $currentQty = intval($row['quantity']);
    $unitPrice  = floatval($row['unit_price']);

    if ($currentQty > 1) {
        // Kurangi quantity, update total_price
        $newQty   = $currentQty - 1;
        $newTotal = $newQty * $unitPrice;

        $upd = $pdo->prepare("
          UPDATE checkout 
             SET quantity = :q, total_price = :t
           WHERE id = :cid
        ");
        $upd->execute([
          ':q'   => $newQty,
          ':t'   => $newTotal,
          ':cid' => $checkoutId
        ]);
    } else {
        // Jika quantity = 1, hapus baris
        $del = $pdo->prepare("DELETE FROM checkout WHERE id = :cid");
        $del->execute([':cid' => $checkoutId]);
    }

    // 2) Ambil stok produk saat ini (LOCK) kemudian tambahkan +1
    $stokStmt = $pdo->prepare("SELECT stock FROM products WHERE id = :pid FOR UPDATE");
    $stokStmt->execute([':pid' => $product_id]);
    $stokRow = $stokStmt->fetch(PDO::FETCH_ASSOC);

    if ($stokRow) {
        $currentStock = intval($stokRow['stock']);
        $newStock     = $currentStock + 1;

        $stkUpd = $pdo->prepare("UPDATE products SET stock = :s WHERE id = :pid");
        $stkUpd->execute([
          ':s'   => $newStock,
          ':pid' => $product_id
        ]);
    }

    // Commit
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
