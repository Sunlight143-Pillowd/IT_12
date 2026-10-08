<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if($isAdmin): ?>
         <?php $__env->slot('header', null, []); ?> 
            <div class="flex items-center justify-between gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <?php echo e(__('Admin Dashboard')); ?>

                </h2>

                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                    Go back to Main Dashboard
                </a>
            </div>
         <?php $__env->endSlot(); ?>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <section class="relative overflow-hidden rounded-2xl bg-black text-white shadow-xl">
                    <div class="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_top,_rgba(168,85,247,0.55),_transparent_55%)]"></div>
                    <div class="relative grid gap-8 p-8 lg:grid-cols-2 lg:p-10">
                        <div>
                            <p class="mb-3 text-sm font-bold uppercase tracking-[0.2em] text-purple-300">Admin access</p>
                            <h1 class="text-3xl font-black leading-tight md:text-5xl">
                                Control your store from one dashboard.
                            </h1>
                            <p class="mt-4 max-w-lg text-sm text-gray-300 md:text-base">
                                Keep an eye on inventory, daily sales, and high-priority product movement from a single control center.
                            </p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="<?php echo e(route('inventory.index')); ?>" class="inline-block bg-purple-600 px-5 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-purple-700">VIEW INVENTORY</a>
                                <a href="<?php echo e(route('pos.index')); ?>" class="inline-block border border-white/30 bg-white/5 px-5 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-white/10">OPEN POS</a>
                            </div>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                            <div class="mb-4 flex items-center justify-between">
                                <span class="text-sm font-bold uppercase tracking-wide text-purple-300">Today</span>
                                <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-[10px] font-bold uppercase text-emerald-300">Live</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-300">Revenue</p>
                                    <p class="mt-2 text-2xl font-black text-white">₱<?php echo e(number_format($revenue, 0)); ?></p>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-300">Orders</p>
                                    <p class="mt-2 text-2xl font-black text-white"><?php echo e($todayOrders); ?></p>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-300">Low Stock</p>
                                    <p class="mt-2 text-2xl font-black text-red-400"><?php echo e($lowStock); ?></p>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-300">Sales Today</p>
                                    <p class="mt-2 text-2xl font-black text-white">₱<?php echo e(number_format($todaySales, 0)); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="grid gap-4 md:grid-cols-4">
                    <?php
                        $stats = [
                            ['label' => 'Products', 'value' => number_format($products), 'tone' => 'text-gray-900'],
                            ['label' => 'Low Stock', 'value' => number_format($lowStock), 'tone' => 'text-red-600'],
                            ['label' => 'Sales Today', 'value' => number_format($todayOrders), 'tone' => 'text-emerald-600'],
                            ['label' => 'Revenue', 'value' => '₱' . number_format($revenue, 0), 'tone' => 'text-purple-600'],
                        ];
                    ?>

                    <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500"><?php echo e($stat['label']); ?></p>
                            <p class="mt-3 text-3xl font-black <?php echo e($stat['tone']); ?>"><?php echo e($stat['value']); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-xl font-black text-gray-900">Top Selling Products</h2>
                            <span class="text-xs font-semibold uppercase tracking-wide text-purple-600">This month</span>
                        </div>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                    <div>
                                        <p class="font-bold text-gray-900"><?php echo e($product->name); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e($product->stock_quantity); ?> units in stock</p>
                                    </div>
                                    <span class="font-bold text-purple-600">₱<?php echo e(number_format($product->price, 0)); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-gray-900">Quick Actions</h2>
                        <div class="mt-5 space-y-3">
                            <a href="<?php echo e(route('inventory.index')); ?>" class="block rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Inventory</p>
                                <p class="text-sm text-gray-500">Check stock levels</p>
                            </a>
                            <a href="<?php echo e(route('pos.index')); ?>" class="block rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">POS</p>
                                <p class="text-sm text-gray-500">Open cashier</p>
                            </a>
                            <a href="<?php echo e(route('quotation.index')); ?>" class="block rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Quotations</p>
                                <p class="text-sm text-gray-500">Create customer quotes</p>
                            </a>
                            <a href="<?php echo e(route('buildpc.index')); ?>" class="block rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Build PC</p>
                                <p class="text-sm text-gray-500">Create custom desktop builds</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-gray-500">Welcome back</p>
                        <h1 class="mt-2 text-3xl font-black text-gray-900"><?php echo e(Auth::user()->name); ?></h1>
                    </div>
                    <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                        Back to Main Dashboard
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-2xl font-black text-gray-900">Purchase History</h2>
                            <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-purple-700">
                                <?php echo e($purchaseHistory->count()); ?> order(s)
                            </span>
                        </div>

                        <?php if($purchaseHistory->isEmpty()): ?>
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center">
                                <p class="text-lg font-semibold text-gray-700">No purchases yet.</p>
                                <p class="mt-2 text-sm text-gray-500">Your recent orders and product purchases will appear here.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php $__currentLoopData = $purchaseHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                                        <div class="flex flex-col gap-3 border-b border-gray-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Order #<?php echo e($sale->id); ?></p>
                                                <p class="mt-1 text-sm text-gray-600"><?php echo e($sale->created_at->format('F d, Y h:i A')); ?></p>
                                            </div>
                                            <div class="text-left sm:text-right">
                                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Total</p>
                                                <p class="mt-1 text-xl font-black text-gray-900">₱<?php echo e(number_format($sale->total_amount, 2)); ?></p>
                                            </div>
                                        </div>

                                        <div class="mt-4 space-y-3">
                                            <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="flex items-center justify-between gap-4 border-b border-gray-200 pb-2 last:border-0 last:pb-0">
                                                    <div>
                                                        <p class="font-semibold text-gray-900"><?php echo e($item->product_name); ?></p>
                                                        <p class="text-sm text-gray-500">Qty: <?php echo e($item->quantity); ?></p>
                                                    </div>
                                                    <p class="text-sm font-bold text-gray-700">₱<?php echo e(number_format($item->subtotal, 2)); ?></p>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Francisco Luis\Downloads\IT_12\resources\views/dashboard.blade.php ENDPATH**/ ?>