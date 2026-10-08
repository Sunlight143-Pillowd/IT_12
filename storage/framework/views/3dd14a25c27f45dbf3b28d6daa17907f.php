<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'Davao Boss Computer')); ?></title>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="bg-white text-gray-900 antialiased">
        <div class="bg-[#1c1c1c] text-gray-300 text-xs">
            <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-8">
                <div class="flex items-center gap-4">
                    <span class="hover:text-white">CORSAIR</span>
                    <span class="hover:text-white">elgato <span class="text-[10px]">(R)</span></span>
                    <span class="hover:text-white">SCUF GAMING</span>
                    <span class="hover:text-white">GAMER SENSE</span>
                </div>
                <div class="flex items-center gap-4">
                    <span>24/7 Lifetime Support</span>
                    <span>09123456789 (PH)</span>
                    <span>Chat Offline</span>
                    <span>Contact</span>
                </div>
            </div>
        </div>

        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
                <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2" aria-label="Davao Boss Computer home">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full border-4 border-purple-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-purple-600"></span>
                    </span>
                    <span class="text-3xl font-black tracking-tight text-purple-600">DAVAO BOSS COMPUTER</span>
                </a>

                <nav class="hidden lg:flex items-center gap-10 text-sm font-semibold text-gray-800">
                    <a href="<?php echo e(route('store.desktops')); ?>" class="hover:text-purple-600">DESKTOPS</a>
                    <a href="<?php echo e(route('store.laptops')); ?>" class="hover:text-purple-600">LAPTOPS</a>
                    <a href="<?php echo e(route('store.categories')); ?>" class="hover:text-purple-600">CATEGORIES</a>
                    <a href="<?php echo e(route('store.special-offers')); ?>" class="text-purple-600 hover:text-purple-700">SPECIAL OFFERS</a>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-purple-600">DASHBOARD</a>
                    <?php endif; ?>
                </nav>

                <div class="flex items-center gap-6 text-gray-700">
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-xs font-semibold uppercase tracking-wide hover:text-purple-600">Sign In</a>
                        <?php if(Route::has('register')): ?>
                            <a href="<?php echo e(route('register')); ?>" class="text-xs font-semibold uppercase tracking-wide hover:text-purple-600">Register</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                            <button type="button"
                                    @click="open = !open"
                                    aria-haspopup="true"
                                    :aria-expanded="open"
                                    class="inline-flex cursor-pointer items-center gap-2 text-xs font-semibold uppercase tracking-wide text-gray-500 hover:text-purple-600">
                                <span>Hi, <?php echo e(Auth::user()->name); ?></span>
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open"
                                 x-transition
                                 @click.outside="open = false"
                                 class="absolute right-0 z-50 mt-2 w-52 rounded-md border border-gray-200 bg-white py-1 shadow-lg"
                                 style="display: none;">
                                <?php if(Auth::user()?->isAdmin()): ?>
                                    <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('profile.edit')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <main>
            <section class="relative bg-black overflow-hidden">
                <div class="placeholder-img absolute inset-0 opacity-40"></div>
                <div class="relative max-w-7xl mx-auto px-4 py-16 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center min-h-[520px]">
                    <div class="text-white z-10">
                        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                            It's as simple as 1, 2, 3!
                        </h1>
                        <p class="text-gray-300 mb-6 max-w-md">
                            With 3 easy steps, choose your next gaming PC with our new
                            <span class="font-semibold text-white">Gaming Desktop Advisor</span>
                        </p>
                        <a href="<?php echo e(route('store.desktops')); ?>" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm tracking-wide px-6 py-3">
                            START NOW
                        </a>
                    </div>

                    <div class="relative z-10 flex items-center justify-center gap-4">
                        <div class="flex items-end gap-4">
                            <div class="placeholder-img w-56 h-72 md:w-64 md:h-80 flex items-center justify-center text-gray-400 text-xs text-center p-4 rounded">
                                [ Image Placeholder<br>Gaming PC Case ]
                            </div>
                            <div class="placeholder-img w-32 h-40 md:w-36 md:h-48 flex items-center justify-center text-gray-400 text-xs text-center p-2 rounded">
                                [ Image Placeholder<br>Speaker/Unit ]
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-[#1c1c1c] py-12">
                <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-5 gap-8">
                    <?php $__currentLoopData = $categoryCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($category['route']); ?>" class="group flex flex-col items-center gap-4 text-center">
                            <div class="placeholder-img w-full h-28 md:h-32 rounded flex items-center justify-center text-gray-400 text-[11px] text-center px-2">
                                [ Image Placeholder ]
                            </div>
                            <span class="text-white text-xs md:text-sm font-bold tracking-wide group-hover:text-purple-500">
                                <?php echo e(strtoupper($category['label'])); ?>

                            </span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        </main>

        <footer class="bg-[#111827] text-gray-300 py-10">
            <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between gap-4 text-sm">
                <div>
                    <p class="font-semibold text-white">Davao Boss Computer</p>
                    <p class="mt-2 max-w-md text-gray-400">Performance builds, workstations, and gaming gear for everyday power users.</p>
                </div>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white">Support</a>
                    <a href="#" class="hover:text-white">Shipping</a>
                    <a href="#" class="hover:text-white">Privacy</a>
                </div>
            </div>
        </footer>
    </body>
</html>
<?php /**PATH C:\Users\Francisco Luis\Downloads\IT_12\resources\views/welcome.blade.php ENDPATH**/ ?>