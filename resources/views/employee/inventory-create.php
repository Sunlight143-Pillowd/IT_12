<?php
require_once dirname(__DIR__, 3) . '/legacy/legacy_auth.php';
require_once dirname(__DIR__, 3) . '/legacy/legacy_database.php';
requireEmployee();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $type = strtolower((string) ($_POST['type'] ?? 'desktop'));
    $category = trim((string) ($_POST['category'] ?? 'general'));
    $price = (int) ($_POST['price'] ?? 0);
    $stockQuantity = (int) ($_POST['stock_quantity'] ?? 0);
    $lowStockThreshold = (int) ($_POST['low_stock_threshold'] ?? 5);
    $stockLocation = strtolower((string) ($_POST['stock_location'] ?? 'warehouse'));
    $description = trim((string) ($_POST['description'] ?? ''));
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name) ?? $name);
    $slug = trim((string) $slug, '-');

    $allowedTypes = ['desktop', 'laptop', 'accessory', 'gpu', 'cpu', 'monitor', 'mouse', 'keyboard', 'case', 'fan', 'cpu_cooler', 'ssd', 'ram', 'motherboard', 'power_supply', 'speaker', 'headset', 'printer', 'router', 'storage', 'networking'];
    $allowedLocations = ['warehouse', 'store', 'used_in_pc'];

    if (isset($pdo) && $pdo instanceof PDO && $name !== '' && in_array($type, $allowedTypes, true) && in_array($stockLocation, $allowedLocations, true) && $price >= 0) {
        $slug = $slug === '' ? 'product-' . time() : $slug;

        $stmt = $pdo->prepare(
            'INSERT INTO products (name, slug, type, category, price, stock_quantity, low_stock_threshold, stock_location, description, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
        );

        $stmt->execute([
            $name,
            $slug,
            $type,
            $category,
            $price,
            $stockQuantity,
            $lowStockThreshold,
            $stockLocation,
            $description,
        ]);
    }
}

header('Location: inventory.php?updated=1');
exit;
