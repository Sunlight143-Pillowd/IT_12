<?php
require_once dirname(__DIR__, 3) . '/config/auth.php';
require_once dirname(__DIR__, 3) . '/config/database.php';
requireEmployee();

$activeEmployeeNav = $activeEmployeeNav ?? '';
$pageTitle = $pageTitle ?? 'Employee Dashboard — Davao Boss Computer';

$empNavLink = function (string $key, string $href, string $label) use ($activeEmployeeNav) {
    $isActive = $activeEmployeeNav === $key;
    $class = 'px-3 py-2 text-xs font-semibold uppercase tracking-wide rounded transition '
        . ($isActive ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white');
    echo '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" class="' . $class . '">'
        . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
};

$lowStockCount = isset($pdo) && $pdo instanceof PDO ? count(lowStockProducts($pdo)) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .placeholder-img {
            background-image: repeating-linear-gradient(45deg, #2a2a2a, #2a2a2a 10px, #333333 10px, #333333 20px);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">

<div class="bg-[#1c1c1c] text-white">
    <div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-14">
        <a href="dashboard.php" class="flex items-center gap-2">
            <span class="text-lg font-black tracking-tight text-purple-500">DAVAO BOSS COMPUTER</span>
            <span class="text-xs font-semibold uppercase tracking-wide text-gray-400 border-l border-gray-600 pl-2">Employee</span>
        </a>
        <div class="flex items-center gap-5 text-xs font-semibold uppercase tracking-wide">
            <span class="text-gray-400">Hi, <?= htmlspecialchars(currentUser()['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <a href="../index.php" class="hover:text-purple-400">Back to Site</a>
            <a href="../logout.php" class="hover:text-purple-400">Sign Out</a>
        </div>
    </div>
    <div class="max-w-6xl mx-auto px-4 flex items-center gap-2 h-12 border-t border-gray-800">
        <?php $empNavLink('dashboard', 'dashboard.php', 'Dashboard'); ?>
        <?php $empNavLink('pos', 'pos.php', 'Point of Sale'); ?>
        <?php $empNavLink('inventory', 'inventory.php', 'Inventory'); ?>
        <?php $empNavLink('quotation', 'quotation.php', 'Quotations'); ?>
        <?php if ($lowStockCount > 0): ?>
            <span class="ml-auto bg-red-600 text-white text-[10px] font-bold rounded-full px-2 py-1">
                <?= $lowStockCount ?> low stock
            </span>
        <?php endif; ?>
    </div>
</div>
