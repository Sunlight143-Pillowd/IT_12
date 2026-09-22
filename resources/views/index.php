<?php
$pageTitle = 'Davao Boss Computer — Build Your Gaming PC';
$activeNav = 'home';
include __DIR__ . '/header.php';

$categories = [
    'Gaming Desktops'        => 'desktops.php',
    'Ready to Ship Desktops' => 'desktops.php?filter=ready-to-ship',
    'Gaming Laptops'         => 'laptops.php',
    'Workstation Desktops'   => 'desktops.php?filter=workstation',
    'Gear Shop'              => 'accessories.php',
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
<section class="bg-[#1c1c1c] py-12">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-5 gap-8">
        <?php foreach ($categories as $category => $link): ?>
            <a href="<?= htmlspecialchars($link) ?>" class="flex flex-col items-center gap-4 text-center group">
                <div class="placeholder-img w-full h-28 md:h-32 rounded flex items-center justify-center text-gray-400 text-[11px] text-center px-2">
                    [ Image Placeholder ]
                </div>
                <span class="text-white text-xs md:text-sm font-bold tracking-wide group-hover:text-purple-500">
                    <?= strtoupper(htmlspecialchars($category)) ?>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>