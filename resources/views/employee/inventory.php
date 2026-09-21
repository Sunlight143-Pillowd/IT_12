<?php
$activeEmployeeNav = 'inventory';
$pageTitle = 'Inventory — Davao Boss Computer';
include __DIR__ . '/header.php';

$products = $pdo->query('SELECT * FROM products ORDER BY type, name')->fetchAll();
?>

<section class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Inventory</h1>
        <?php if (isset($_GET['updated'])): ?>
            <span class="bg-green-50 border border-green-300 text-green-800 text-sm rounded px-3 py-1.5">Stock updated.</span>
        <?php endif; ?>
    </div>

    <input type="text" id="inventory-search" placeholder="Search by name, type, or category…"
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-4">

    <table class="w-full text-sm border-collapse bg-white">
        <thead>
            <tr class="text-left border-b border-gray-200 text-gray-500 uppercase text-xs">
                <th class="py-2 pr-4">Product</th>
                <th class="py-2 pr-4">Type</th>
                <th class="py-2 pr-4">Category</th>
                <th class="py-2 pr-4">Price</th>
                <th class="py-2 pr-4">Stock</th>
                <th class="py-2">Update</th>
            </tr>
        </thead>
        <tbody id="inventory-rows">
            <?php foreach ($products as $product): ?>
                <?php $isLow = $product['stock_quantity'] <= $product['low_stock_threshold']; ?>
                <tr class="border-b border-gray-100 inventory-row"
                    data-search="<?= htmlspecialchars(strtolower($product['name'] . ' ' . $product['type'] . ' ' . $product['category']), ENT_QUOTES, 'UTF-8') ?>">
                    <td class="py-3 pr-4 font-medium"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="py-3 pr-4 text-gray-500 capitalize"><?= htmlspecialchars($product['type'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="py-3 pr-4 text-gray-500"><?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="py-3 pr-4 text-gray-500">₱<?= number_format((int) $product['price']) ?></td>
                    <td class="py-3 pr-4 <?= $isLow ? 'text-red-600 font-semibold' : '' ?>">
                        <?= (int) $product['stock_quantity'] ?><?= $isLow ? ' ⚠' : '' ?>
                    </td>
                    <td class="py-3">
                        <form action="inventory-update.php" method="post" class="flex items-center gap-2">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <input type="number" name="stock_quantity" min="0" value="<?= (int) $product['stock_quantity'] ?>"
                                   class="w-20 border border-gray-300 rounded px-2 py-1">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-3 py-1.5 rounded">
                                Save
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<script>
document.getElementById('inventory-search').addEventListener('input', function () {
    const term = this.value.trim().toLowerCase();
    document.querySelectorAll('.inventory-row').forEach(function (row) {
        row.style.display = row.dataset.search.includes(term) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
