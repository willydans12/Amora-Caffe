<?php
// get_cart.php
require 'koneksi.php';
header('Content-Type: application/json');

try {
    // JOIN checkout dengan products untuk mendapatkan image_url
    $sql = "
      SELECT 
        c.product_id,
        c.product_name,
        c.unit_price,
        c.quantity,
        c.total_price,
        p.image_url
      FROM checkout c
      JOIN products p 
        ON p.id = c.product_id
      ORDER BY c.id
    ";
    $stmt = $pdo->query($sql);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += floatval($item['total_price']);
    }
    $tax   = $subtotal * 0.10;  // 10% pajak
    $total = $subtotal + $tax;

    echo json_encode([
      'status'   => 'success',
      'items'    => $items,
      'subtotal' => $subtotal,
      'tax'      => $tax,
      'total'    => $total
    ]);
} catch (PDOException $e) {
    echo json_encode([
      'status'  => 'error',
      'message' => $e->getMessage()
    ]);
}
