<?php
    require_once dirname(__DIR__, 3) . '/legacy/legacy_auth.php';
    // Pages set $activeNav (legacy) or $activePage (newer pattern). Normalize both
    // so the current nav link stays highlighted regardless of how the page loads it.
    $activePage = $activePage ?? ($activeNav ?? '');
    $pageTitle = $pageTitle ?? 'Davao Boss Computer';
    $navLink = function (string $page, string $href, string $label) use ($activePage) {
        $isActive = $activePage === $page;
        $class = 'hover:text-purple-600' . ($isActive ? ' text-purple-600' : '');
        echo '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" class="' . $class . '">'
        . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
    };

    $lowStockCount = isEmployee() && isset($pdo) && $pdo instanceof PDO
        ? count(lowStockProducts($pdo))
        : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <!-- If your project uses Vite + Tailwind, remove this CDN line and rely on your compiled app.css instead -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .placeholder-img {
            background-image: repeating-linear-gradient(45deg, #2a2a2a, #2a2a2a 10px, #333333 10px, #333333 20px);
        }
    </style>
</head>
<body class="bg-white text-gray-900">

<!-- ===================== TOP UTILITY BAR ===================== -->
<div class="bg-[#1c1c1c] text-gray-300 text-xs">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-8">
        <div class="flex items-center gap-4">
            <a href="#" class="hover:text-white">CORSAIR</a>
            <a href="#" class="hover:text-white">elgato <span class="text-[10px]">(R)</span></a>
            <a href="#" class="hover:text-white">SCUF GAMING</a>
            <a href="#" class="hover:text-white">GAMER SENSE</a>
        </div>
        <div class="flex items-center gap-4">
            <span>24/7 Lifetime Support</span>
            <span>09123456789 (PH)</span>
            <a href="#" id="topbar-chat-link" class="hover:text-white">Chat Offline</a>
            <a href="#" class="hover:text-white">Contact</a>
        </div>
    </div>
</div>

<!-- ===================== MAIN NAV ===================== -->
<header class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
        <a href="index.php" class="flex items-center gap-2" aria-label="ORIGIN home">
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border-4 border-purple-600">
                <span class="h-1.5 w-1.5 rounded-full bg-purple-600"></span>
            </span>
            <span class="text-3xl font-black tracking-tight text-purple-600">DAVAO BOSS COMPUTER</span>
        </a>
        <nav class="hidden lg:flex items-center gap-8 text-sm font-bold uppercase tracking-wide text-gray-800">
            <?php $navLink('desktops', 'desktops.php', 'DESKTOPS'); ?>
            <?php $navLink('laptops', 'laptops.php', 'LAPTOPS'); ?>
            <?php $navLink('categories', 'index.php#categories', 'CATEGORIES'); ?>
            <a href="special-offers.php" class="text-purple-600 hover:text-purple-700">SPECIAL OFFERS</a>
            <?php if (isEmployee()): ?>
                <a href="warehouse/index.php" class="flex items-center gap-1.5 hover:text-purple-600">
                    WAREHOUSE
                    <?php if ($lowStockCount > 0): ?>
                        <span class="bg-red-600 text-white text-[10px] font-bold rounded-full px-1.5 leading-4" title="<?= $lowStockCount ?> item(s) low on stock">
                            <?= $lowStockCount ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </nav>
        <div class="flex items-center gap-9 text-gray-700">
            <a id="search-toggle-btn" aria-label="Search" aria-expanded="false" aria-controls="search-overlay" class="text-xs font-semibold uppercase tracking-wide hover:text-purple-600" href="search.php">Search</a>
            <?php if (isLoggedIn()): ?>
                <div class="relative">
                    <button type="button" id="account-menu-toggle" aria-haspopup="true" aria-expanded="false" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-gray-500 hover:text-purple-600 focus:outline-none">
                        <span><?= htmlspecialchars(currentUser()['name'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="account-menu" class="absolute right-0 z-50 mt-2 hidden w-48 rounded-md border border-gray-200 bg-white py-1 shadow-lg">
                        <?php if (isEmployee()): ?>
                            <a href="employee/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                        <?php endif; ?>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="text-xs font-semibold uppercase tracking-wide hover:text-purple-600">Sign In</a>
            <?php endif; ?>
            <a href="cart.php" id="cart-toggle-btn" aria-label="Cart" aria-expanded="false" aria-controls="cart-drawer" class="text-xs font-semibold uppercase tracking-wide hover:text-purple-600">Cart</a>
        </div>
    </div>

    <!-- Search overlay: hidden until the Search button is toggled -->
    <div id="search-overlay" class="hidden border-t border-gray-200 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center gap-3">
            <input id="search-input" type="text" placeholder="Search"
                   class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-purple-600">
            <button id="search-close-btn" aria-label="Close search" class="text-gray-500 hover:text-gray-800 text-xl leading-none">&times;</button>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const accountToggle = document.getElementById('account-menu-toggle');
        const accountMenu = document.getElementById('account-menu');

        if (!accountToggle || !accountMenu) {
            return;
        }

        accountToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            const isHidden = accountMenu.classList.contains('hidden');
            accountMenu.classList.toggle('hidden', !isHidden);
            accountToggle.setAttribute('aria-expanded', String(isHidden));
        });

        document.addEventListener('click', function (event) {
            if (!accountToggle.contains(event.target) && !accountMenu.contains(event.target)) {
                accountMenu.classList.add('hidden');
                accountToggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                accountMenu.classList.add('hidden');
                accountToggle.setAttribute('aria-expanded', 'false');
            }
        });
    });
</script>

<!-- Cart panel: hidden until the Cart button is toggled -->
<div id="cart-drawer" class="hidden fixed inset-0 z-50">
    <div id="cart-drawer-backdrop" class="absolute inset-0 bg-black/50"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-sm bg-white shadow-xl">
        <div class="flex items-center justify-between px-4 h-14 border-b border-gray-200">
            <span class="font-bold text-sm uppercase tracking-wide">Cart</span>
            <button id="cart-close-btn" aria-label="Close cart" class="text-gray-500 hover:text-gray-800 text-xl leading-none">&times;</button>
        </div>
        <div class="placeholder-img h-40 m-4 rounded flex items-center justify-center text-gray-400 text-xs">
            [ Image Placeholder ]
        </div>
    </div>
</div>