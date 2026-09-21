<?php
require_once dirname(__DIR__, 3) . '/config/auth.php';
requireEmployee();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['stock_quantity'] ?? -1);

    if ($productId > 0 && $quantity >= 0) {
        $stmt = $pdo->prepare('UPDATE products SET stock_quantity = ? WHERE id = ?');
        $stmt->execute([$quantity, $productId]);
    }
}

header('Location: inventory.php?updated=1');
exit;
