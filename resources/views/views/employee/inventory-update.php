<?php
require_once dirname(__DIR__, 3) . '/config/auth.php';
require_once dirname(__DIR__, 3) . '/config/database.php';
requireEmployee();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['stock_quantity'] ?? -1);
    $stockLocation = strtolower((string) ($_POST['stock_location'] ?? 'warehouse'));
    $allowedLocations = ['warehouse', 'store', 'used_in_pc'];

    if (isset($pdo) && $pdo instanceof PDO && $productId > 0 && $quantity >= 0 && in_array($stockLocation, $allowedLocations, true)) {
        $stmt = $pdo->prepare('UPDATE products SET stock_quantity = ?, stock_location = ? WHERE id = ?');
        $stmt->execute([$quantity, $stockLocation, $productId]);
    }
}

header('Location: inventory.php?updated=1');
exit;
