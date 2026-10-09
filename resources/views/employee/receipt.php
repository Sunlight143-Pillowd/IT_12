<?php
require_once dirname(__DIR__, 3) . '/legacy/legacy_auth.php';
requireEmployee();

$saleId = (int) ($_GET['sale_id'] ?? 0);

$stmt = $pdo->prepare('SELECT s.*, u.name AS employee_name FROM sales s JOIN users u ON u.id = s.employee_id WHERE s.id = ?');
$stmt->execute([$saleId]);
$sale = $stmt->fetch();

if (!$sale) {
    http_response_code(404);
    exit('Sale not found.');
}

$itemsStmt = $pdo->prepare('SELECT * FROM sale_items WHERE sale_id = ? ORDER BY id');
$itemsStmt->execute([$saleId]);
$items = $itemsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?= $saleId ?> — Davao Boss Computer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 py-10">

    <div class="no-print max-w-md mx-auto mb-4 flex items-center justify-between">
        <a href="pos.php" class="text-sm text-purple-600 hover:text-purple-800">← New Sale</a>
        <button onclick="window.print()" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-4 py-2 rounded">
            Print Receipt
        </button>
    </div>

    <div class="max-w-md mx-auto bg-white border border-gray-200 rounded p-6">
        <div class="text-center mb-6">
            <p class="text-lg font-black text-purple-600">DAVAO BOSS COMPUTER</p>
            <p class="text-xs text-gray-500">Official Receipt</p>
        </div>

        <div class="text-xs text-gray-600 mb-4 space-y-0.5">
            <p>Receipt #: <?= $sale['id'] ?></p>
            <p>Date: <?= date('M j, Y g:i A', strtotime($sale['created_at'])) ?></p>
            <p>Served by: <?= htmlspecialchars($sale['employee_name'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php if ($sale['customer_name']): ?>
                <p>Customer: <?= htmlspecialchars($sale['customer_name'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <table class="w-full text-sm border-t border-b border-gray-200 py-2 mb-4">
            <thead>
                <tr class="text-left text-xs text-gray-500 uppercase">
                    <th class="py-2">Item</th>
                    <th class="py-2 text-center">Qty</th>
                    <th class="py-2 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="py-1.5">
                            <?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?>
                            <div class="text-xs text-gray-400">₱<?= number_format($item['unit_price'], 2) ?> each</div>
                        </td>
                        <td class="py-1.5 text-center"><?= (int) $item['quantity'] ?></td>
                        <td class="py-1.5 text-right">₱<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="flex items-center justify-between font-bold text-lg">
            <span>Total</span>
            <span>₱<?= number_format($sale['total_amount'], 2) ?></span>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">Thank you for shopping with us!</p>
    </div>

</body>
</html>
