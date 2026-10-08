<?php
include dirname(__DIR__, 3) . '/legacy/legacy_database.php';

$query = trim((string) ($_GET['q'] ?? ''));
$pageTitle = 'Search — Davao Boss Computer';
$activePage = '';
$products = [];

if ($query !== '' && isset($pdo) && $pdo instanceof PDO) {
    try {
        $searchTerm = '%' . $query . '%';
        $stmt = $pdo->prepare(
            'SELECT * FROM products
             WHERE is_active = 1
               AND (name LIKE :name OR category LIKE :category OR type LIKE :type OR description LIKE :description)
             ORDER BY name ASC'
        );
        $stmt->execute([
            ':name' => $searchTerm,
            ':category' => $searchTerm,
            ':type' => $searchTerm,
            ':description' => $searchTerm,
        ]);
        $products = $stmt->fetchAll();
    } catch (Throwable $e) {
        $products = [];
    }
}

include __DIR__ . '/header.php';
?>

<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <h1 class="text-2xl md:text-3xl font-extrabold mb-6">Search Products</h1>

        <form method="get" class="flex flex-col sm:flex-row gap-3 mb-10">
            <label for="search-query" class="sr-only">Search products</label>
            <input id="search-query" name="q" type="search" value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Search desktops, laptops, accessories..."
                   class="flex-1 border border-gray-300 rounded px-3 py-3 text-sm focus:outline-none focus:border-purple-600">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-6 py-3 rounded">
                SEARCH
            </button>
        </form>

        <?php if ($query === ''): ?>
            <p class="text-sm text-gray-500">Enter a product name or category to begin searching.</p>
        <?php elseif (empty($products)): ?>
            <p class="text-sm text-gray-500">No products found for &quot;<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>&quot;.</p>
        <?php else: ?>
            <p class="text-sm text-gray-500 mb-6">
                <?= count($products) ?> result<?= count($products) === 1 ? '' : 's' ?> for &quot;<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>&quot;
            </p>
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