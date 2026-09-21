<?php
require_once dirname(__DIR__, 3) . '/config/auth.php';
requireEmployee();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pos.php');
    exit;
}

$cart = json_decode($_POST['cart_json'] ?? '[]', true);
$customerName = trim($_POST['customer_name'] ?? '') ?: null;

if (!is_array($cart) || count($cart) === 0) {
    header('Location: pos.php?error=empty_cart');
    exit;
}

try {
    $pdo->beginTransaction();

    $total = 0.0;
    $lineItems = [];

    foreach ($cart as $line) {
        $productId = (int) ($line['product_id'] ?? 0);
        $quantity = (int) ($line['quantity'] ?? 0);

        if ($productId <= 0 || $quantity <= 0) {
            continue;
        }

        // Lock the row and re-check stock server-side — never trust the client's numbers.
        $stmt = $pdo->prepare('SELECT id, name, price, stock_quantity FROM products WHERE id = ? FOR UPDATE');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            continue;
        }
        if ($quantity > $product['stock_quantity']) {
            $pdo->rollBack();
            header('Location: pos.php?error=insufficient_stock&product=' . urlencode($product['name']));
            exit;
        }

        $unitPrice = (float) $product['price'];
        $subtotal = $unitPrice * $quantity;
        $total += $subtotal;

        $lineItems[] = [
            'product_id' => $productId,
            'name' => $product['name'],
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ];

        $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?')
            ->execute([$quantity, $productId]);
    }

    if (empty($lineItems)) {
        $pdo->rollBack();
        header('Location: pos.php?error=empty_cart');
        exit;
    }

    $employeeId = currentUser()['id'];
    $pdo->prepare('INSERT INTO sales (employee_id, customer_name, total_amount) VALUES (?, ?, ?)')
        ->execute([$employeeId, $customerName, $total]);
    $saleId = (int) $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        'INSERT INTO sale_items (sale_id, product_id, product_name, unit_price, quantity, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    foreach ($lineItems as $item) {
        $itemStmt->execute([
            $saleId, $item['product_id'], $item['name'], $item['unit_price'], $item['quantity'], $item['subtotal'],
        ]);
    }

    $pdo->commit();

    header('Location: receipt.php?sale_id=' . $saleId);
    exit;
} catch (\Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: pos.php?error=checkout_failed');
    exit;
}
