<?php
require_once dirname(__DIR__, 3) . '/config/auth.php';
requireEmployee();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: quotation.php');
    exit;
}

$cart = json_decode($_POST['cart_json'] ?? '[]', true);
$customerName = trim($_POST['customer_name'] ?? '') ?: null;
$customerContact = trim($_POST['customer_contact'] ?? '') ?: null;
$notes = trim($_POST['notes'] ?? '') ?: null;

if (!is_array($cart) || count($cart) === 0) {
    header('Location: quotation.php?error=empty_cart');
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

        $stmt = $pdo->prepare('SELECT id, name, price FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        if (!$product) {
            continue;
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
    }

    if (empty($lineItems)) {
        $pdo->rollBack();
        header('Location: quotation.php?error=empty_cart');
        exit;
    }

    $employeeId = currentUser()['id'];
    $pdo->prepare(
        'INSERT INTO quotations (employee_id, customer_name, customer_contact, notes, total_amount)
         VALUES (?, ?, ?, ?, ?)'
    )->execute([$employeeId, $customerName, $customerContact, $notes, $total]);
    $quotationId = (int) $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        'INSERT INTO quotation_items (quotation_id, product_id, product_name, unit_price, quantity, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    foreach ($lineItems as $item) {
        $itemStmt->execute([
            $quotationId, $item['product_id'], $item['name'], $item['unit_price'], $item['quantity'], $item['subtotal'],
        ]);
    }

    $pdo->commit();

    header('Location: quotation-print.php?id=' . $quotationId);
    exit;
} catch (\Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: quotation.php?error=save_failed');
    exit;
}
