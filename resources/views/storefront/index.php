<?php
$pageTitle = 'Davao Boss Computer — Build Your Gaming PC';
$activeNav = 'home';
include __DIR__ . '/header.php';

$categoryLinks = [
    ['label' => 'CPU CASE', 'href' => 'accessories.php?filter=components'],
    ['label' => 'CPU COOLERS', 'href' => 'accessories.php?filter=components'],
    ['label' => 'EXTERNAL HARD DRIVES', 'href' => 'accessories.php?filter=components'],
    ['label' => 'EXTERNAL SSD', 'href' => 'accessories.php?filter=components'],
    ['label' => 'FANS', 'href' => 'accessories.php?filter=components'],
    ['label' => 'FLASH DRIVES', 'href' => 'accessories.php?filter=components'],
    ['label' => 'GAMING CHAIRS', 'href' => 'desktops.php?filter=workstation'],
    ['label' => 'GAMING KEYBOARDS', 'href' => 'accessories.php?filter=peripherals'],
    ['label' => 'GAMING MOUSE', 'href' => 'accessories.php?filter=peripherals'],
    ['label' => 'GRAPHICS CARDS', 'href' => 'accessories.php?filter=components'],
    ['label' => 'HARD DRIVES', 'href' => 'accessories.php?filter=components'],
    ['label' => 'HEADSETS', 'href' => 'accessories.php?filter=audio'],
    ['label' => 'LAPTOPS', 'href' => 'laptops.php'],
    ['label' => 'MEMORY MODULES', 'href' => 'accessories.php?filter=components'],
    ['label' => 'MICRO SD', 'href' => 'accessories.php?filter=components'],
    ['label' => 'MONITORS', 'href' => 'accessories.php?filter=displays'],
    ['label' => 'MOTHERBOARDS', 'href' => 'accessories.php?filter=components'],
    ['label' => 'NETWORK ATTACHED STORAGE(NAS)', 'href' => 'accessories.php?filter=components'],
    ['label' => 'POWER SUPPLIES', 'href' => 'accessories.php?filter=components'],
    ['label' => 'PRINTERS', 'href' => 'accessories.php?filter=components'],
    ['label' => 'PROCESSORS', 'href' => 'accessories.php?filter=components'],
    ['label' => 'ROUTERS', 'href' => 'accessories.php?filter=components'],
    ['label' => 'SOLID STATE DRIVES(SSD)', 'href' => 'accessories.php?filter=components'],
    ['label' => 'SPEAKERS', 'href' => 'accessories.php?filter=audio'],
];
?>

<!-- HERO SECTION -->
<section class="relative bg-black overflow-hidden">
    <div class="placeholder-img absolute inset-0 opacity-40 flex items-center justify-center text-gray-500 text-sm">
        [ Background Image Placeholder — moody red/black PC studio shot ]
    </div>

    <div class="relative max-w-7xl mx-auto px-4 py-16 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center min-h-[520px]">
        <div class="text-white z-10">
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                It's as simple as 1, 2, 3!
            </h1>
            <p class="text-gray-300 mb-6 max-w-md">
                With 3 easy steps, choose your next gaming PC with our new
                <span class="font-semibold text-white">Gaming Desktop Advisor</span>
            </p>
            <a href="/advisor.php" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm tracking-wide px-6 py-3">
                START NOW
            </a>
        </div>

        <div class="relative z-10 flex items-center justify-center gap-4">
            <button aria-label="Previous" class="hidden md:block text-white text-2xl">‹</button>
            <div class="flex items-end gap-4">
                <div class="placeholder-img w-56 h-72 md:w-64 md:h-80 flex items-center justify-center text-gray-400 text-xs text-center p-4 rounded">
                    [ Image Placeholder<br>Gaming PC Case ]
                </div>
                <div class="placeholder-img w-32 h-40 md:w-36 md:h-48 flex items-center justify-center text-gray-400 text-xs text-center p-2 rounded">
                    [ Image Placeholder<br>Speaker/Unit ]
                </div>
            </div>
            <button aria-label="Next" class="hidden md:block text-white text-2xl">›</button>

            <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 bg-black border-2 border-white rounded-full w-16 h-16 flex items-center justify-center text-white text-[9px] font-bold text-center">
                CUE
            </div>
        </div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 pb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-center text-white text-xs md:text-sm font-semibold">
            <span>1. CHOOSE CASE SIZE</span>
            <span>2. SELECT FAVORITE GAMES &amp; RESOLUTION</span>
            <span>3. GET A RECOMMENDED PC</span>
        </div>
    </div>

    <div class="relative flex items-center justify-center gap-2 pb-6">
        <span class="w-2 h-2 rounded-full bg-white"></span>
        <span class="w-2 h-2 rounded-full bg-white"></span>
        <span class="w-2 h-2 rounded-full bg-white"></span>
        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
        <span class="w-2 h-2 rounded-full bg-white"></span>
    </div>
</section>

<!-- CATEGORY GRID -->
<section id="categories" class="bg-[#f3f3f3] py-10 border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-[240px_minmax(0,1fr)] gap-8 items-start">
        <div class="bg-white border border-gray-200 rounded-md p-4 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-2">
                <h2 class="text-xl font-black uppercase tracking-tight text-gray-900">CATEGORIES</h2>
                <span class="text-[10px] font-bold uppercase tracking-[0.22em] text-gray-400">Shop</span>
            </div>
            <ul class="space-y-1.5 text-sm font-medium text-gray-700">
                <?php foreach ($categoryLinks as $category): ?>
                    <li>
                        <a href="<?= htmlspecialchars($category['href']) ?>" class="block rounded px-2 py-1.5 hover:bg-purple-50 hover:text-purple-700 transition-colors">
                            <?= htmlspecialchars($category['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="bg-white border border-gray-200 rounded-md shadow-sm p-4">
            <div class="mb-4 flex items-center justify-between border-b border-gray-200 pb-2">
                <h3 class="text-sm font-black uppercase tracking-[0.22em] text-gray-600">Featured</h3>
                <span class="text-xs font-semibold text-purple-600">Powered by ASUS</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <a href="desktops.php" class="group block rounded-lg border border-gray-200 bg-[#fafafa] p-4 hover:border-purple-300 transition-colors">
                    <div class="placeholder-img h-36 mb-4 rounded flex items-center justify-center text-gray-400 text-[11px]">[ DESKTOP IMAGE ]</div>
                    <h4 class="text-lg font-black text-gray-900 group-hover:text-purple-600">Gaming Desktops</h4>
                </a>
                <a href="laptops.php" class="group block rounded-lg border border-gray-200 bg-[#fafafa] p-4 hover:border-purple-300 transition-colors">
                    <div class="placeholder-img h-36 mb-4 rounded flex items-center justify-center text-gray-400 text-[11px]">[ LAPTOP IMAGE ]</div>
                    <h4 class="text-lg font-black text-gray-900 group-hover:text-purple-600">Gaming Laptops</h4>
                </a>
                <a href="desktops.php?filter=workstation" class="group block rounded-lg border border-gray-200 bg-[#fafafa] p-4 hover:border-purple-300 transition-colors">
                    <div class="placeholder-img h-36 mb-4 rounded flex items-center justify-center text-gray-400 text-[11px]">[ WORKSTATION IMAGE ]</div>
                    <h4 class="text-lg font-black text-gray-900 group-hover:text-purple-600">Workstation PCs</h4>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>