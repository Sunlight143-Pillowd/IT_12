<?php
include dirname(__DIR__,2) . '/config/database.php';

$pageTitle = 'Gaming Desktops — Davao Boss Computer';
$activeNav = 'desktops';

$filter = $_GET['filter'] ?? 'all';
$sort   = $_GET['sort'] ?? 'default';

$allowedFilters = ['all', 'ready-to-ship', 'gaming', 'workstation'];
if (!in_array($filter, $allowedFilters, true)) {
    $filter = 'all';
}

$sql = "SELECT * FROM products WHERE type = 'desktop' AND is_active = 1";
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
?>

<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold">Gaming Desktops</h1>
                <p class="text-sm text-gray-500 mt-1">Ready-to-ship and made-to-order towers.</p>
            </div>
            <form method="get" id="sort-form">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <select name="sort" id="sort" onchange="this.form.submit()" class="border border-gray-300 text-sm px-3 py-2 rounded">
                    <option value="default" <?= $sort === 'default' ? 'selected' : '' ?>>Sort: Featured</option>
                    <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Filters -->
            <aside class="lg:col-span-1">
                <h2 class="text-sm font-bold uppercase tracking-wide mb-3">Filter</h2>
                <form method="get" class="flex flex-col gap-2 text-sm mb-6">
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                    <?php foreach ([
                        'all' => 'All Desktops',
                        'ready-to-ship' => 'Ready to Ship',
                        'gaming' => 'Gaming',
                        'workstation' => 'Workstation',
                    ] as $value => $label): ?>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="filter" value="<?= $value ?>"
                                   <?= $filter === $value ? 'checked' : '' ?>
                                   onchange="this.form.submit()">
                            <?= htmlspecialchars($label) ?>
                        </label>
                    <?php endforeach; ?>
                </form>
                <a href="/advisor.php" class="block text-center bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-4 py-3">
                    NOT SURE? TRY THE ADVISOR
                </a>
            </aside>

            <!-- Product grid -->
            <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php if (empty($products)): ?>
                    <p class="col-span-full text-sm text-gray-500 py-12 text-center">No desktops match this filter yet.</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <a href="/product.php?type=desktop&amp;slug=<?= urlencode($product['slug']) ?>"
                           class="group border border-gray-200 hover:border-purple-400 rounded p-4 flex flex-col">
                            <div class="placeholder-img w-full h-40 rounded flex items-center justify-center text-gray-400 text-[11px] mb-4">
                                <?php if (!empty($product['image_path'])): ?>
                                    <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover rounded">
                                <?php else: ?>
                                    [ Image Placeholder ]
                                <?php endif; ?>
                            </div>
                            <h3 class="text-sm font-bold group-hover:text-purple-600 mb-1"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="text-xs text-gray-500 mb-3"><?= htmlspecialchars(ucfirst(str_replace('-', ' ', $product['category']))) ?></p>
                            <p class="mt-auto text-sm font-extrabold">₱<?= number_format((int) $product['price']) ?></p>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>