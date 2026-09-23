<?php
require_once dirname(__DIR__, 3) . '/legacy/legacy_auth.php';
requireEmployee();

$quotationId = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT q.*, u.name AS employee_name FROM quotations q JOIN users u ON u.id = q.employee_id WHERE q.id = ?');
$stmt->execute([$quotationId]);
$quotation = $stmt->fetch();

if (!$quotation) {
    http_response_code(404);
    exit('Quotation not found.');
}

$itemsStmt = $pdo->prepare('SELECT * FROM quotation_items WHERE quotation_id = ? ORDER BY id');
$itemsStmt->execute([$quotationId]);
$items = $itemsStmt->fetchAll();

$validUntil = date('M j, Y', strtotime($quotation['created_at'] . ' +7 days'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation #<?= $quotationId ?> — Davao Boss Computer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 py-10">

    <div class="no-print max-w-2xl mx-auto mb-4 flex items-center justify-between">
        <a href="quotation.php" class="text-sm text-purple-600 hover:text-purple-800">← New Quotation</a>
        <button onclick="window.print()" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-4 py-2 rounded">
            Print Quotation
        </button>
    </div>

    <div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded p-8">
        <div class="flex items-start justify-between mb-8">
            <div>
                <p class="text-xl font-black text-purple-600">DAVAO BOSS COMPUTER</p>
                <p class="text-xs text-gray-500">Price Quotation</p>
            </div>
            <div class="text-right text-xs text-gray-600">
                <p>Quotation #: <?= $quotation['id'] ?></p>
                <p>Date: <?= date('M j, Y', strtotime($quotation['created_at'])) ?></p>
                <p>Valid until: <?= $validUntil ?></p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-8">
            <div>
                <p class="text-xs uppercase text-gray-400 font-semibold mb-1">Prepared For</p>
                <p class="font-medium"><?= htmlspecialchars($quotation['customer_name'] ?: '—', ENT_QUOTES, 'UTF-8') ?></p>
                <?php if ($quotation['customer_contact']): ?>
                    <p class="text-gray-500"><?= htmlspecialchars($quotation['customer_contact'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-xs uppercase text-gray-400 font-semibold mb-1">Prepared By</p>
                <p class="font-medium"><?= htmlspecialchars($quotation['employee_name'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>

        <table class="w-full text-sm border-t border-gray-200 mb-6">
            <thead>
                <tr class="text-left text-xs text-gray-500 uppercase border-b border-gray-200">
                    <th class="py-2">Item</th>
                    <th class="py-2 text-center">Qty</th>
                    <th class="py-2 text-right">Unit Price</th>
                    <th class="py-2 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr class="border-b border-gray-100">
                        <td class="py-2"><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-2 text-center"><?= (int) $item['quantity'] ?></td>
                        <td class="py-2 text-right">₱<?= number_format($item['unit_price'], 2) ?></td>
                        <td class="py-2 text-right">₱<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="flex justify-end mb-8">
            <div class="w-48 flex items-center justify-between font-bold text-lg">
                <span>Total</span>
                <span>₱<?= number_format($quotation['total_amount'], 2) ?></span>
            </div>
        </div>

        <?php if ($quotation['notes']): ?>
            <div class="text-sm text-gray-600 border-t border-gray-200 pt-4 mb-4">
                <p class="text-xs uppercase text-gray-400 font-semibold mb-1">Notes</p>
                <p><?= nl2br(htmlspecialchars($quotation['notes'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
        <?php endif; ?>

        <p class="text-xs text-gray-400 border-t border-gray-200 pt-4">
            This quotation is valid for 7 days from the date issued. Prices are subject to change without prior notice.
        </p>
    </div>

</body>
</html>
