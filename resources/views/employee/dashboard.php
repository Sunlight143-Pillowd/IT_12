<?php
$activeEmployeeNav = 'dashboard';
$pageTitle = 'Dashboard — Davao Boss Computer';
include __DIR__ . '/header.php';

$totalProducts = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$lowStock = lowStockProducts($pdo);

$todaySalesCount = 0;
$todaySalesTotal = 0.0;
try {
    $row = $pdo->query(
        "SELECT COUNT(*) AS cnt, COALESCE(SUM(total_amount), 0) AS total
         FROM sales WHERE DATE(created_at) = CURDATE()"
    )->fetch();
    $todaySalesCount = (int) $row['cnt'];
    $todaySalesTotal = (float) $row['total'];
} catch (\PDOException $e) {
    // sales table doesn't exist yet — run database/pos-schema.sql
}
?>

<section class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <div class="bg-white border border-gray-200 rounded p-4">
            <p class="text-xs uppercase text-gray-500 font-semibold">Products</p>
            <p class="text-2xl font-bold mt-1"><?= $totalProducts ?></p>
        </div>
        <div class="bg-white border border-gray-200 rounded p-4">
            <p class="text-xs uppercase text-gray-500 font-semibold">Low Stock</p>
            <p class="text-2xl font-bold mt-1 <?= count($lowStock) ? 'text-red-600' : '' ?>"><?= count($lowStock) ?></p>
        </div>
        <div class="bg-white border border-gray-200 rounded p-4">
            <p class="text-xs uppercase text-gray-500 font-semibold">Sales Today</p>
            <p class="text-2xl font-bold mt-1"><?= $todaySalesCount ?></p>
        </div>
        <div class="bg-white border border-gray-200 rounded p-4">
            <p class="text-xs uppercase text-gray-500 font-semibold">Revenue Today</p>
            <p class="text-2xl font-bold mt-1">₱<?= number_format($todaySalesTotal, 2) ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <a href="pos.php" class="block bg-purple-600 hover:bg-purple-700 text-white rounded p-6">
            <p class="font-bold text-lg mb-1">Point of Sale</p>
            <p class="text-sm text-purple-100">Ring up a walk-in sale and print a receipt.</p>
        </a>
        <a href="inventory.php" class="block bg-white border border-gray-200 hover:border-purple-600 rounded p-6">
            <p class="font-bold text-lg mb-1">Inventory</p>
            <p class="text-sm text-gray-500">Check stock levels and update quantities.</p>
        </a>
        <a href="quotation.php" class="block bg-white border border-gray-200 hover:border-purple-600 rounded p-6">
            <p class="font-bold text-lg mb-1">Quotations</p>
            <p class="text-sm text-gray-500">Build a printable price quote for a customer.</p>
        </a>
    </div>

    <?php if (!empty($lowStock)): ?>
        <div class="bg-red-50 border border-red-300 text-red-800 rounded p-4">
            <p class="font-semibold text-sm mb-2">⚠ Low stock</p>
            <ul class="text-sm list-disc list-inside space-y-1">
                <?php foreach ($lowStock as $item): ?>
                    <li><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?> — <?= (int) $item['stock_quantity'] ?> left</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/footer.php'; ?>
