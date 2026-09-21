<?php
include dirname(__DIR__,2) . '/config/database.php';

$pageTitle = 'Gear Shop — Davao Boss Computer';
$activeNav = 'accessories';

$filter = $_GET['filter'] ?? 'all';
$sort   = $_GET['sort'] ?? 'default';

$allowedFilters = ['all', 'peripherals', 'displays', 'audio', 'components'];
if (!in_array($filter, $allowedFilters, true)) {
    $filter = 'all';
}

$sql = "SELECT * FROM products WHERE type = 'accessory' AND is_active = 1";
$params = [];

if ($filter !== 'all') {
    $sql .= " AND category = :category";
    $params[':category'] = $filter;
}

if ($sort === 'price-asc') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort === 'price-desc') {
    $sql .= " ORDER BY price DESC";
} else {
    $sql .= " ORDER BY name ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

include __DIR__ . '/header.php';

$added = $_GET['added'] ?? null;
?>

<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold">Gear Shop</h1>
                <p class="text-sm text-gray-500 mt-1">Peripherals, displays, and components.</p>
            </div>
            <form method="get">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <select name="sort" onchange="this.form.submit()" class="border border-gray-300 text-sm px-3 py-2 rounded">
                    <option value="default" <?= $sort === 'default' ? 'selected' : '' ?>>Sort: Featured</option>
                    <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <aside class="lg:col-span-1">
                <h2 class="text-sm font-bold uppercase tracking-wide mb-3">Filter</h2>
                <form method="get" class="flex flex-col gap-2 text-sm">
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                    <?php foreach ([
                        'all' => 'All Gear',
                        'peripherals' => 'Peripherals',
                        'displays' => 'Displays',
                        'audio' => 'Audio',
                        'components' => 'Components',
                    ] as $value => $label): ?>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="filter" value="<?= $value ?>"
                                   <?= $filter === $value ? 'checked' : '' ?>
                                   onchange="this.form.submit()">
                            <?= htmlspecialchars($label) ?>
                        </label>
                    <?php endforeach; ?>
                </form>
            </aside>

            <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php if (empty($products)): ?>
                    <p class="col-span-full text-sm text-gray-500 py-12 text-center">No gear matches this filter yet.</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="border border-gray-200 hover:border-purple-400 rounded p-4 flex flex-col">
                            <div class="placeholder-img w-full h-32 rounded flex items-center justify-center text-gray-400 text-[11px] mb-4">
                                <?php if (!empty($product['image_path'])): ?>
                                    <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover rounded">
                                <?php else: ?>
                                    [ Image Placeholder ]
                                <?php endif; ?>
                            </div>
                            <h3 class="text-sm font-bold mb-1"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="text-xs text-gray-500 mb-3"><?= htmlspecialchars(ucfirst($product['category'])) ?></p>
                            <div class="mt-auto flex items-center justify-between">
                                <span class="text-sm font-extrabold">₱<?= number_format((int) $product['price']) ?></span>
                                <form method="post" action="/add-to-cart.php">
                                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                    <input type="hidden" name="return_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-3 py-2">
                                        ADD
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if ($added): ?>
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-black text-white text-sm px-4 py-3 rounded shadow-lg">
        <?= htmlspecialchars($added) ?> added to cart
    </div>
    <?php
    $pageScripts = <<<'HTML'
    <script>
        setTimeout(function () {
            var toast = document.getElementById('toast');
            if (toast) toast.style.display = 'none';
        }, 2200);
    </script>
    HTML;
    ?>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>