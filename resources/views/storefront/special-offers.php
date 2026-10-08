<?php
include dirname(__DIR__, 3) . '/legacy/legacy_database.php';

$pageTitle = 'Special Offers — Davao Boss Computer';
$activePage = 'special-offers';
$type = $_GET['type'] ?? 'all';
$sort = $_GET['sort'] ?? 'featured';

$allowedTypes = ['all', 'desktop', 'laptop', 'accessory'];
if (!in_array($type, $allowedTypes, true)) {
    $type = 'all';
}

$products = [];

if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $sql = 'SELECT * FROM products WHERE is_active = 1';
        $params = [];

        if ($type !== 'all') {
            $sql .= ' AND type = :type';
            $params[':type'] = $type;
        }

        if ($sort === 'price-asc') {
            $sql .= ' ORDER BY price ASC';
        } elseif ($sort === 'price-desc') {
            $sql .= ' ORDER BY price DESC';
        } else {
            $sql .= ' ORDER BY created_at DESC, name ASC';
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
    } catch (Throwable $e) {
        $products = [];
    }
}

include __DIR__ . '/header.php';
?>

<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold">Special Offers</h1>
                <p class="text-sm text-gray-500 mt-1">Explore current product offers from Davao Boss Computer.</p>
            </div>
            <form method="get" class="flex flex-wrap gap-3">
                <label for="offer-type" class="sr-only">Product type</label>
                <select id="offer-type" name="type" class="border border-gray-300 text-sm px-3 py-2 rounded">
                    <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All Products</option>
                    <option value="desktop" <?= $type === 'desktop' ? 'selected' : '' ?>>Desktops</option>
                    <option value="laptop" <?= $type === 'laptop' ? 'selected' : '' ?>>Laptops</option>
                    <option value="accessory" <?= $type === 'accessory' ? 'selected' : '' ?>>Accessories</option>
                </select>
                <label for="offer-sort" class="sr-only">Sort offers</label>
                <select id="offer-sort" name="sort" onchange="this.form.submit()" class="border border-gray-300 text-sm px-3 py-2 rounded">
                    <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Sort: Featured</option>
                    <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-4 py-2 rounded">
                    APPLY
                </button>
            </form>
        </div>

        <?php if (empty($products)): ?>
            <p class="text-sm text-gray-500 py-12 text-center">No offers are available for this category yet.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                    <a href="product.php?type=<?= urlencode($product['type']) ?>&amp;slug=<?= urlencode($product['slug']) ?>"
                       class="group border border-gray-200 hover:border-purple-400 rounded p-4 flex flex-col">
                        <div class="placeholder-img w-full h-40 rounded flex items-center justify-center text-gray-400 text-[11px] mb-4">
                            <?php if (!empty($product['image_path'])): ?>
                                <img src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8') ?>"
                                     alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                                     class="w-full h-full object-cover rounded">
                            <?php else: ?>
                                [ Image Placeholder ]
                            <?php endif; ?>
                        </div>
                        <p class="text-[10px] uppercase tracking-wide text-purple-600 font-semibold mb-1">
                            <?= htmlspecialchars(ucfirst($product['type']), ENT_QUOTES, 'UTF-8') ?> offer
                        </p>
                        <h2 class="text-sm font-bold group-hover:text-purple-600 mb-1">
                            <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <p class="text-xs text-gray-500 mb-3">
                            <?= htmlspecialchars(ucfirst(str_replace('-', ' ', $product['category'])), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="mt-auto text-sm font-extrabold">₱<?= number_format((int) $product['price']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
