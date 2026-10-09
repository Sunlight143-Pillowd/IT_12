<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'Davao Boss Computer')); ?></title>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <style>
            @media (min-width: 1280px) {
                .storefront-desktop-navigation {
                    display: flex !important;
                }

                .storefront-menu-toggle,
                .storefront-responsive-navigation {
                    display: none !important;
                }
            }

            @media (max-width: 1279px) {
                .storefront-desktop-navigation {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body class="bg-white text-gray-900 antialiased">
        <div class="bg-[#1c1c1c] text-gray-300 text-xs">
            <div class="mx-auto flex h-8 max-w-7xl items-center justify-between px-4">
                <div class="hidden items-center gap-4 md:flex">
                    <span class="hover:text-white">CORSAIR</span>
                    <span class="hover:text-white">elgato <span class="text-[10px]">(R)</span></span>
                    <span class="hover:text-white">SCUF GAMING</span>
                    <span class="hover:text-white">GAMER SENSE</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <span class="hidden sm:inline">24/7 Lifetime Support</span>
                    <a href="tel:<?php echo e(config('store.phone')); ?>" class="hover:text-white"><?php echo e(config('store.phone')); ?> (PH)</a>
                    <span class="hidden sm:inline">Contact</span>
                </div>
            </div>
        </div>

        <header x-data="{ mobileMenuOpen: false }" class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4">
                <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2" aria-label="Davao Boss Computer home">
                    <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['class' => 'shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'shrink-0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $attributes = $__attributesOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $component = $__componentOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__componentOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
                </a>

                <nav class="storefront-desktop-navigation hidden items-center gap-5 text-xs font-bold tracking-wide text-gray-800 xl:flex">
                    <a href="<?php echo e(route('store.desktops')); ?>" class="hover:text-purple-700">DESKTOPS</a>
                    <a href="<?php echo e(route('store.laptops')); ?>" class="hover:text-purple-700">LAPTOPS</a>
                    <a href="<?php echo e(route('store.categories')); ?>" class="hover:text-purple-700">CATEGORIES</a>
                    <a href="<?php echo e(route('store.top-selling')); ?>" class="hover:text-purple-700">TOP SELLING</a>
                    <a href="<?php echo e(route('buildpc.customer')); ?>" class="hover:text-purple-700">BUILD PC</a>
                    <a href="<?php echo e(route('store.special-offers')); ?>" class="text-purple-700 hover:text-purple-800">OFFERS</a>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-purple-600"><?php echo e(Auth::user()->isAdmin() ? 'DASHBOARD' : 'MY ORDERS'); ?></a>
                    <?php endif; ?>
                </nav>

                <div class="flex items-center gap-3 text-gray-700 sm:gap-5">
                    <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('cart.index')); ?>" class="inline-flex items-center gap-2 rounded-lg px-2 py-2 text-xs font-bold uppercase tracking-wide hover:bg-purple-50 hover:text-purple-700" aria-label="Shopping cart, <?php echo e(array_sum(session('cart', []))); ?> items">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.1 10.1a2 2 0 002 1.6h8.8a2 2 0 002-1.6L21 8H6"></path><circle cx="10" cy="20" r="1"></circle><circle cx="18" cy="20" r="1"></circle>
                        </svg>
                        <span>CART (<?php echo e(array_sum(session('cart', []))); ?>)</span>
                    </a>
                    <?php endif; ?>
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('login')); ?>" class="inline-flex items-center whitespace-nowrap text-xs font-bold uppercase tracking-wide hover:text-purple-700">Sign In</a>
                        <?php if(Route::has('register')): ?>
                            <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center whitespace-nowrap text-xs font-bold uppercase tracking-wide hover:text-purple-700">Register</a>
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
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()" aria-label="Toggle navigation" class="storefront-menu-toggle inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-700 hover:bg-gray-50 xl:hidden">
                        <svg x-show="!mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"></path></svg>
                    </button>
                </div>
            </div>

            <nav x-show="mobileMenuOpen" x-cloak class="storefront-responsive-navigation border-t border-gray-100 bg-white px-4 py-3 xl:hidden">
                <div class="mx-auto grid max-w-7xl gap-1 sm:grid-cols-2">
                    <a href="<?php echo e(route('store.desktops')); ?>" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Desktops</a>
                    <a href="<?php echo e(route('store.laptops')); ?>" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Laptops</a>
                    <a href="<?php echo e(route('store.categories')); ?>" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Categories</a>
                    <a href="<?php echo e(route('store.top-selling')); ?>" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Top Selling</a>
                    <a href="<?php echo e(route('buildpc.customer')); ?>" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Build PC</a>
                    <a href="<?php echo e(route('store.special-offers')); ?>" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Special offers</a>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50"><?php echo e(Auth::user()->isAdmin() ? 'Dashboard' : 'My orders'); ?></a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Sign in</a>
                        <?php if(Route::has('register')): ?>
                            <a href="<?php echo e(route('register')); ?>" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Register</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </nav>
        </header>

        <main>
            <?php if(session('status')): ?>
                <div class="mx-auto mt-4 max-w-7xl px-4 text-sm text-green-700" role="status">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>
            <?php if($errors->has('quantity')): ?>
                <div class="mx-auto mt-4 max-w-7xl px-4 text-sm text-red-700" role="alert">
                    <?php echo e($errors->first('quantity')); ?>

                </div>
            <?php endif; ?>
            <?php if($errors->has('image')): ?>
                <div class="mx-auto mt-4 max-w-7xl px-4 text-sm text-red-700" role="alert">
                    <?php echo e($errors->first('image')); ?>

                </div>
            <?php endif; ?>
            <section class="relative bg-black overflow-hidden">
                <div class="placeholder-img absolute inset-0 opacity-40"></div>
                <div class="relative max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center min-h-[420px]">
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
                            <?php $__currentLoopData = [
                                'hero-main' => ['width' => 'w-44 h-56 md:w-64 md:h-80', 'button' => 'Upload Photo'],
                                'hero-secondary' => ['width' => 'w-28 h-36 md:w-36 md:h-48', 'button' => 'Upload'],
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $imageKey => $heroImage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php ($heroImagePath = $heroImages->get($imageKey)); ?>
                                <div class="group relative <?php echo e($heroImage['width']); ?>">
                                    <img data-category-preview src="<?php echo e($heroImagePath ? asset('storage/'.$heroImagePath) : ''); ?>" alt="Homepage feature" class="<?php echo e($heroImagePath ? '' : 'hidden'); ?> h-full w-full rounded object-cover">
                                    <div data-category-placeholder class="placeholder-img flex h-full w-full flex-col items-center justify-center gap-2 rounded p-4 text-center text-gray-300 <?php echo e($heroImagePath ? 'hidden' : ''); ?>">
                                        <span class="text-4xl font-light leading-none text-white/80">＋</span>
                                        <?php if(! Auth::user()?->canManageOrders()): ?>
                                            <span class="text-[10px] font-semibold uppercase tracking-[0.22em] text-white/80">Image</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if(Auth::user()?->canManageOrders() && ! $heroImagePath): ?>
                                        <form method="POST" action="<?php echo e(route('homepage-images.upload', $imageKey)); ?>" enctype="multipart/form-data" class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-2">
                                            <?php echo csrf_field(); ?>
                                            <label class="inline-flex cursor-pointer items-center gap-2 rounded bg-white/95 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.18em] text-gray-800 shadow transition hover:bg-purple-100 hover:text-purple-800">
                                                <span aria-hidden="true" class="text-xl leading-none">＋</span>
                                                <span><?php echo e($heroImage['button']); ?></span>
                                                <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/bmp,image/webp" required class="sr-only" onchange="previewCategoryPhoto(this)">
                                            </label>
                                            <button type="submit" data-category-submit class="hidden rounded bg-purple-700 px-3 py-2 text-[10px] font-bold uppercase tracking-wide text-white shadow transition hover:bg-purple-800">Save Photo</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-[#1c1c1c] py-12">
                <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-5 gap-8">
                    <?php $__currentLoopData = $categoryCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="group relative flex flex-col items-center gap-4 text-center">
                            <a href="<?php echo e($category['route']); ?>" class="flex w-full flex-col items-center gap-4">
                                <div class="relative h-28 w-full overflow-hidden rounded md:h-32">
                                    <img data-category-preview src="<?php echo e($category['image_path'] ? asset('storage/'.$category['image_path']) : ''); ?>" alt="<?php echo e($category['label']); ?>" class="<?php echo e($category['image_path'] ? '' : 'hidden'); ?> h-full w-full object-contain">
                                    <div data-category-placeholder class="placeholder-img flex h-full w-full flex-col items-center justify-center gap-2 px-2 text-center text-[11px] text-gray-300 <?php echo e($category['image_path'] ? 'hidden' : ''); ?>">
                                        <span class="text-3xl font-light leading-none text-white/80">＋</span>
                                        <?php if(! Auth::user()?->canManageOrders()): ?>
                                            <span class="text-[9px] font-semibold uppercase tracking-[0.2em] text-white/80">Image</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="text-xs font-bold tracking-wide text-white group-hover:text-purple-500 md:text-sm">
                                    <?php echo e(strtoupper($category['label'])); ?>

                                </span>
                            </a>
                            <?php if(Auth::user()?->canManageOrders() && ! $category['image_path']): ?>
                                <form method="POST" action="<?php echo e(route('inventory.categories.image', $category['slug'])); ?>" enctype="multipart/form-data" class="absolute right-2 top-2 flex flex-col items-end gap-1">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="category_name" value="<?php echo e($category['name']); ?>">
                                    <label class="inline-flex cursor-pointer items-center gap-1.5 rounded bg-white/95 px-2.5 py-1.5 text-[9px] font-bold uppercase tracking-wide text-gray-800 shadow transition hover:bg-purple-100 hover:text-purple-800">
                                        <span aria-hidden="true" class="text-sm leading-none">＋</span>
                                        <span>Upload Photo</span>
                                        <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/bmp,image/webp" required class="sr-only" onchange="previewCategoryPhoto(this)">
                                    </label>
                                    <button type="submit" data-category-submit class="hidden rounded bg-purple-700 px-2.5 py-1.5 text-[9px] font-bold uppercase tracking-wide text-white shadow transition hover:bg-purple-800">Save Photo</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>

            <section class="py-12">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="mb-6 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-600">In stock now</p>
                            <h2 class="mt-2 text-2xl font-black text-gray-900">Featured Products</h2>
                        </div>
                        <a href="<?php echo e(route('store.desktops')); ?>" class="text-sm font-semibold text-purple-600 hover:text-purple-700">Browse store</a>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <?php $__empty_1 = true; $__currentLoopData = $featured; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php ($availableStock = $product->availableStock()); ?>
                            <article data-photo-card class="relative flex h-full flex-col border border-gray-200 bg-white p-4">
                                <div class="relative mt-3 flex h-52 w-full items-center justify-center overflow-hidden bg-gray-100">
                                    <img data-category-preview src="<?php echo e($product->featured_image_path ? asset('storage/'.$product->featured_image_path) : ''); ?>" alt="<?php echo e($product->name); ?>" class="<?php echo e($product->featured_image_path ? '' : 'hidden'); ?> h-full w-full object-contain">
                                    <div data-category-placeholder class="h-full w-full <?php echo e($product->featured_image_path ? 'hidden' : ''); ?>"></div>
                                </div>
                                <h3 class="mt-3 min-h-14 text-lg font-bold text-gray-900"><?php echo e($product->name); ?></h3>
                                <div class="mt-auto flex items-center justify-between gap-3 pt-4">
                                    <span class="font-black text-purple-600">₱<?php echo e(number_format($product->price, 0)); ?></span>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($availableStock <= $product->low_stock_threshold ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'); ?>">
                                        <?php echo e($availableStock); ?> in stock
                                    </span>
                                </div>
                                <?php if (isset($component)) { $__componentOriginal7eb846a9d6661322cec973b548c4c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb846a9d6661322cec973b548c4c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.store.cart-controls','data' => ['product' => $product,'availableStock' => $availableStock]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('store.cart-controls'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'available-stock' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableStock)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb846a9d6661322cec973b548c4c18b)): ?>
<?php $attributes = $__attributesOriginal7eb846a9d6661322cec973b548c4c18b; ?>
<?php unset($__attributesOriginal7eb846a9d6661322cec973b548c4c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb846a9d6661322cec973b548c4c18b)): ?>
<?php $component = $__componentOriginal7eb846a9d6661322cec973b548c4c18b; ?>
<?php unset($__componentOriginal7eb846a9d6661322cec973b548c4c18b); ?>
<?php endif; ?>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="col-span-full text-sm text-gray-500">Products will appear here when they are added to inventory.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>

        <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <script>
            function previewCategoryPhoto(input) {
                const file = input.files?.[0];
                const card = input.closest('[data-photo-card], .group');
                const image = card?.querySelector('[data-category-preview]');
                const placeholder = card?.querySelector('[data-category-placeholder]');
                const submitButton = card?.querySelector('[data-category-submit]');

                if (!file || !image || !placeholder || !submitButton) {
                    return;
                }

                image.src = URL.createObjectURL(file);
                image.classList.remove('hidden');
                placeholder.classList.add('hidden');
                submitButton.classList.remove('hidden');
            }
        </script>
    </body>
</html>
<?php /**PATH C:\Users\MYPC\Downloads\IT_12-main (3)\IT_12-main\resources\views/welcome.blade.php ENDPATH**/ ?>