<?php
$activeEmployeeNav = 'inventory';
$pageTitle = 'Inventory — Davao Boss Computer';
include __DIR__ . '/header.php';

$locationLabel = function (array $product): string {
    $location = strtolower((string) ($product['stock_location'] ?? ''));

    if ($location === 'store') {
        return 'Store';
    }

    if ($location === 'used_in_pc' || $location === 'used-in-pc' || $location === 'pc-build' || $location === 'build') {
        return 'Used in PC';
    }

    if ($location === 'warehouse') {
        return 'Warehouse';
    }

    return (isset($product['stock_quantity']) && (int) $product['stock_quantity'] > 0) ? 'Warehouse' : 'Store';
};

$colorClass = function (string $status): string {
    return match ($status) {
        'Warehouse' => 'bg-blue-50 text-blue-700 border-blue-200',
        'Store' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Used in PC' => 'bg-purple-50 text-purple-700 border-purple-200',
        default => 'bg-gray-50 text-gray-700 border-gray-200',
    };
};

$productTypes = [
    'desktop' => 'Desktop',
    'laptop' => 'Laptop',
    'accessory' => 'Accessory',
    'gpu' => 'GPU',
    'cpu' => 'CPU',
    'monitor' => 'Monitor',
    'mouse' => 'Mouse',
    'keyboard' => 'Keyboard',
    'case' => 'Case',
    'fan' => 'Fan',
    'cpu_cooler' => 'CPU Cooler',
    'ssd' => 'SSD',
    'ram' => 'RAM',
    'motherboard' => 'Motherboard',
    'power_supply' => 'Power Supply',
    'speaker' => 'Speaker',
    'headset' => 'Headset',
    'printer' => 'Printer',
    'router' => 'Router',
    'storage' => 'Storage',
    'networking' => 'Networking',
];

$products = $pdo->query('SELECT * FROM products ORDER BY type, name')->fetchAll();
?>

<section class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Inventory</h1>
        <?php if (isset($_GET['updated'])): ?>
            <span class="bg-green-50 border border-green-300 text-green-800 text-sm rounded px-3 py-1.5">Stock updated.</span>
        <?php endif; ?>
    </div>

    <div class="mb-6 bg-white border border-gray-200 rounded p-4">
        <h2 class="text-lg font-bold mb-4">Add New Item</h2>
        <form action="inventory-create.php" method="post" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-7 gap-3 items-end">
            <div class="xl:col-span-2">
                <label class="block text-[11px] uppercase tracking-wide text-gray-500 mb-1">Name</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-[11px] uppercase tracking-wide text-gray-500 mb-1">Type</label>
                <select name="type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <?php foreach ($productTypes as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-[11px] uppercase tracking-wide text-gray-500 mb-1">Category</label>
                <select name="category" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="general" selected>general</option>
                    <?php foreach ($productTypes as $value => $label): ?>
                        <?php $categoryOption = match ($value) {
                            'desktop' => 'Desktop',
                            'laptop' => 'Laptop',
                            'accessory' => 'Accessory',
                            default => strtoupper(str_replace('_', ' ', $label)),
                        }; ?>
                        <option value="<?= htmlspecialchars($categoryOption, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($categoryOption, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                    <?php foreach ($products as $product): ?>
                        <?php $categoryName = trim((string) ($product['category'] ?? '')); ?>
                        <?php if ($categoryName !== '' && $categoryName !== 'general'): ?>
                            <option value="<?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-[11px] uppercase tracking-wide text-gray-500 mb-1">Price</label>
                <input type="number" name="price" min="0" value="0" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-[11px] uppercase tracking-wide text-gray-500 mb-1">Stock</label>
                <input type="number" name="stock_quantity" min="0" value="0" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-[11px] uppercase tracking-wide text-gray-500 mb-1">Status</label>
                <select name="stock_location" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="warehouse">Warehouse</option>
                    <option value="store">Store</option>
                    <option value="used_in_pc">Used in PC</option>
                </select>
            </div>
            <div class="xl:col-span-2">
                <label class="block text-[11px] uppercase tracking-wide text-gray-500 mb-1">Low Stock Alert</label>
                <input type="number" name="low_stock_threshold" min="0" value="5" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div class="xl:col-span-5">
                <label class="block text-[11px] uppercase tracking-wide text-gray-500 mb-1">Description</label>
                <input type="text" name="description" placeholder="Optional description" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div class="xl:col-span-7 flex justify-end">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-4 py-2 rounded">
                    Add Item
                </button>
            </div>
        </form>
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
                <th class="py-2 pr-4">Status</th>
                <th class="py-2">Update</th>
            </tr>
        </thead>
        <tbody id="inventory-rows">
            <?php foreach ($products as $product): ?>
                <?php $isLow = $product['stock_quantity'] <= $product['low_stock_threshold']; ?>
                <?php $status = $locationLabel($product); ?>
                <tr class="border-b border-gray-100 inventory-row"
                    data-search="<?= htmlspecialchars(strtolower($product['name'] . ' ' . $product['type'] . ' ' . $product['category'] . ' ' . $status), ENT_QUOTES, 'UTF-8') ?>">
                    <td class="py-3 pr-4 font-medium"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="py-3 pr-4 text-gray-500 capitalize"><?= htmlspecialchars($product['type'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="py-3 pr-4 text-gray-500"><?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="py-3 pr-4 text-gray-500">₱<?= number_format((int) $product['price']) ?></td>
                    <td class="py-3 pr-4 <?= $isLow ? 'text-red-600 font-semibold' : '' ?>">
                        <?= (int) $product['stock_quantity'] ?><?= $isLow ? ' ⚠' : '' ?>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="inline-flex items-center border rounded-full px-2.5 py-1 text-[11px] font-semibold <?= $colorClass($status) ?>">
                            <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="py-3">
                        <form action="inventory-update.php" method="post" class="flex items-center gap-2">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <input type="number" name="stock_quantity" min="0" value="<?= (int) $product['stock_quantity'] ?>"
                                   class="w-20 border border-gray-300 rounded px-2 py-1">
                            <select name="stock_location" class="border border-gray-300 rounded px-2 py-1 text-xs">
                                <?php foreach (['warehouse' => 'Warehouse', 'store' => 'Store', 'used_in_pc' => 'Used in PC'] as $value => $label): ?>
                                    <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" <?= strtolower((string) ($product['stock_location'] ?? 'warehouse')) === $value ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
