<?php
$activeEmployeeNav = 'dashboard';
$pageTitle = 'Dashboard — Davao Boss Computer';
include __DIR__ . '/header.php';

$totalProducts = 0;
$lowStock = [];
$totalSoldUnits = 0;
$todaySalesCount = 0;
$todaySalesTotal = 0.0;
$thisMonthSalesTotal = 0.0;
$totalRevenue = 0.0;
$topSellingItems = [];

if (isset($pdo) && $pdo instanceof PDO) {
    $totalProducts = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $lowStock = lowStockProducts($pdo);

    try {
        $todaySalesRow = $pdo->query(
            "SELECT COUNT(*) AS cnt, COALESCE(SUM(total_amount), 0) AS total
             FROM sales WHERE DATE(created_at) = CURDATE()"
        )->fetch();
        $todaySalesCount = (int) $todaySalesRow['cnt'];
        $todaySalesTotal = (float) $todaySalesRow['total'];

        $salesSummary = $pdo->query(
            "SELECT
                COALESCE(SUM(CASE WHEN DATE(created_at) = CURDATE() THEN total_amount ELSE 0 END), 0) AS revenue_today,
                COALESCE(SUM(CASE WHEN DATE(created_at) >= DATE_FORMAT(CURDATE(), '%Y-%m-01') THEN total_amount ELSE 0 END), 0) AS revenue_month,
                COALESCE(SUM(total_amount), 0) AS total_revenue,
                COUNT(*) AS sales_count
             FROM sales"
        )->fetch();

        $thisMonthSalesTotal = (float) ($salesSummary['revenue_month'] ?? 0);
        $totalRevenue = (float) ($salesSummary['total_revenue'] ?? 0);

        $totalSoldUnits = (int) $pdo->query('SELECT COALESCE(SUM(quantity), 0) FROM sale_items')->fetchColumn();

        $topSellingItems = $pdo->query(
            "SELECT product_name AS name, SUM(quantity) AS units_sold, SUM(subtotal) AS revenue
             FROM sale_items
             GROUP BY product_id, product_name
             ORDER BY units_sold DESC, revenue DESC
             LIMIT 5"
        )->fetchAll();
    } catch (\PDOException $e) {
        // sales/sale_items tables may not exist yet — run database/pos-schema.sql
    }
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
        <div class="bg-white border border-gray-200 rounded p-4">
            <p class="text-xs uppercase text-gray-500 font-semibold">Units Sold</p>
            <p class="text-2xl font-bold mt-1"><?= $totalSoldUnits ?></p>
        </div>
        <div class="bg-white border border-gray-200 rounded p-4">
            <p class="text-xs uppercase text-gray-500 font-semibold">This Month</p>
            <p class="text-2xl font-bold mt-1">₱<?= number_format($thisMonthSalesTotal, 2) ?></p>
        </div>
        <div class="bg-white border border-gray-200 rounded p-4">
            <p class="text-xs uppercase text-gray-500 font-semibold">Overall Revenue</p>
            <p class="text-2xl font-bold mt-1">₱<?= number_format($totalRevenue, 2) ?></p>
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

    <?php if (!empty($topSellingItems)): ?>
        <div class="mt-10 bg-white border border-gray-200 rounded p-4">
            <h2 class="text-lg font-bold mb-4">Top Selling Products</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="text-left border-b border-gray-200 text-gray-500 uppercase text-xs">
                            <th class="py-2 pr-4">Product</th>
                            <th class="py-2 pr-4">Units Sold</th>
                            <th class="py-2 pr-4">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topSellingItems as $item): ?>
                            <tr class="border-b border-gray-100">
                                <td class="py-3 pr-4 font-medium"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="py-3 pr-4"><?= (int) $item['units_sold'] ?></td>
                                <td class="py-3 pr-4">₱<?= number_format((float) $item['revenue'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/footer.php'; ?>
