<?php
require_once dirname(__DIR__, 3) . '/legacy/legacy_auth.php';
require_once dirname(__DIR__, 3) . '/legacy/legacy_database.php';
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
            <div class="relative">
                <button type="button" id="employee-account-toggle" aria-haspopup="true" aria-expanded="false" class="inline-flex items-center gap-2 text-gray-400 hover:text-purple-400 focus:outline-none">
                    <span><?= htmlspecialchars(currentUser()['name'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></span>
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div id="employee-account-menu" class="absolute right-0 z-50 mt-2 hidden w-48 rounded-md border border-gray-700 bg-[#1f1f1f] py-1 shadow-lg">
                    <a href="../index.php" class="block px-4 py-2 text-xs uppercase tracking-wide text-gray-200 hover:bg-gray-800">Back to Site</a>
                    <a href="../logout.php" class="block px-4 py-2 text-xs uppercase tracking-wide text-gray-200 hover:bg-gray-800">Logout</a>
                </div>
            </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('employee-account-toggle');
        const menu = document.getElementById('employee-account-menu');

        if (!toggle || !menu) {
            return;
        }

        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden', !isHidden);
            toggle.setAttribute('aria-expanded', String(isHidden));
        });

        document.addEventListener('click', function (event) {
            if (!toggle.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                menu.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    });
</script>
