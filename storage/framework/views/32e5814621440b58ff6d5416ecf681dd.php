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
    <?php
        $todaySalesVat = \App\Support\PhilippineVat::breakdown((float) $todaySales);
        $revenueVat = \App\Support\PhilippineVat::breakdown((float) $revenue);
    ?>

    <?php if($isStaff): ?>
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
                <?php if(session('status')): ?>
                    <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"><?php echo e(session('status')); ?></div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                    <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><?php echo e($errors->first()); ?></div>
                <?php endif; ?>
                <section class="relative overflow-hidden rounded-2xl bg-black text-white shadow-xl">
                    <div class="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_top,rgba(168,85,247,0.55),transparent_55%)]"></div>
                    <div class="relative p-8 lg:p-10">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm sm:p-5">
                                <div class="mb-3 flex items-center justify-between lg:mb-4">
                                    <span class="text-sm font-bold uppercase tracking-wide text-purple-300">Today</span>
                                    <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-[10px] font-bold uppercase text-emerald-300">Live</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3">
                                    <div class="flex min-w-0 flex-col justify-center gap-2 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-3 transition-colors hover:border-purple-300/30 lg:gap-3 lg:p-4">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Sales Today</p>
                                        <p class="break-words text-xl font-black leading-tight tracking-tight tabular-nums text-white sm:text-2xl">₱<?php echo e(number_format($todaySales, 0)); ?></p>
                                        <p class="text-[10px] text-gray-400">Includes ₱<?php echo e(number_format($todaySalesVat['vat'], 2)); ?> VAT (12%)</p>
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-center gap-2 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-3 transition-colors hover:border-purple-300/30 lg:gap-3 lg:p-4">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Pending Orders Today</p>
                                        <p class="break-words text-xl font-black leading-tight tracking-tight tabular-nums text-white sm:text-2xl"><?php echo e($todayOrders); ?></p>
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-center gap-2 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-3 transition-colors hover:border-red-300/30 lg:gap-3 lg:p-4">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Low Stock</p>
                                        <p class="break-words text-xl font-black leading-tight tracking-tight tabular-nums text-red-400 sm:text-2xl"><?php echo e($lowStock); ?></p>
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-center gap-2 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-3 transition-colors hover:border-purple-300/30 lg:gap-3 lg:p-4">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Total Revenue</p>
                                        <p class="break-words text-xl font-black leading-tight tracking-tight tabular-nums text-white sm:text-2xl">₱<?php echo e(number_format($revenue, 0)); ?></p>
                                        <p class="text-[10px] text-gray-400">Includes ₱<?php echo e(number_format($revenueVat['vat'], 2)); ?> VAT (12%)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex min-w-0 flex-col rounded-xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                    <h1 class="text-xl font-black text-white">Customer Orders</h1>
                                    <span class="rounded-full bg-purple-500/20 px-3 py-1 text-xs font-bold text-purple-200"><?php echo e($totalOrders); ?> order(s)</span>
                                </div>
                                <div class="max-h-[22rem] overflow-y-auto rounded-lg border border-white/10">
                                    <table class="w-full table-fixed text-left text-xs text-gray-200 sm:text-sm">
                                        <thead class="sticky top-0 z-10 bg-gray-900 text-[10px] uppercase text-gray-300 sm:text-xs">
                                            <tr><th class="w-[8%] px-2 py-3 sm:px-4">Order</th><th class="w-[16%] px-2 py-3 sm:px-4">Customer</th><th class="w-[16%] px-2 py-3 sm:px-4">Items</th><th class="w-[10%] px-2 py-3 sm:px-4">Date</th><th class="w-[10%] px-2 py-3 text-right sm:px-4">Total</th><th class="w-[17%] px-2 py-3 sm:px-4">Payment / Fulfillment</th><th class="w-[23%] px-2 py-3 sm:px-4">Status / Action</th></tr>
                                        </thead>
                                        <tbody class="break-words">
                                            <?php $__empty_1 = true; $__currentLoopData = $storeOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <?php
                                                    $orderVat = \App\Support\PhilippineVat::breakdown((float) $order->total_amount);
                                                ?>
                                                <tr class="border-t border-white/10">
                                                    <td class="px-4 py-3 font-semibold text-white">#<?php echo e($order->id); ?></td>
                                                    <td class="px-4 py-3"><?php echo e($order->customer_name); ?><br><span class="text-xs text-gray-400"><?php echo e($order->customer_email); ?></span><?php if($order->customer_phone): ?><br><span class="text-xs text-gray-400"><?php echo e($order->customer_phone); ?></span><?php endif; ?></td>
                                                    <td class="px-4 py-3">
                                                        <ul class="space-y-1">
                                                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <li><?php echo e($item->product_name); ?> × <?php echo e($item->quantity); ?></li>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </ul>
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-300"><?php echo e($order->created_at->format('M j, Y')); ?></td>
                                                    <td class="px-4 py-3 text-right font-semibold text-white">
                                                        <?php if($order->shipping_zone === 'outside_davao' && $order->shipping_fee === null): ?>
                                                            Subtotal<br>₱<?php echo e(number_format($order->total_amount, 2)); ?>

                                                        <?php else: ?>
                                                            ₱<?php echo e(number_format($order->total_amount, 2)); ?>

                                                        <?php endif; ?>
                                                        <span class="block text-[10px] font-normal text-gray-400">Includes ₱<?php echo e(number_format($orderVat['vat'], 2)); ?> VAT (12%)</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <p class="font-semibold"><?php echo e(ucwords(str_replace('_', ' ', $order->payment_method))); ?></p>
                                                        <p class="text-xs text-gray-400"><?php echo e(ucfirst($order->fulfillment_method)); ?></p>
                                                        <?php if($order->shipping_zone === 'outside_davao'): ?>
                                                            <p class="mt-1 text-xs text-gray-400">
                                                                Shipping: <?php echo e($order->shipping_fee === null ? 'Fee needs confirmation' : '₱'.number_format($order->shipping_fee, 2)); ?>

                                                            </p>
                                                        <?php elseif($order->shipping_fee !== null && $order->shipping_fee > 0): ?>
                                                            <p class="mt-1 text-xs text-gray-400">Shipping: ₱<?php echo e(number_format($order->shipping_fee, 2)); ?></p>
                                                        <?php endif; ?>
                                                        <?php if($order->shipping_zone === 'davao_city'): ?>
                                                            <p class="mt-1 text-xs text-gray-400">Distance: <?php echo e(number_format($order->shipping_distance_km, 1)); ?> km</p>
                                                        <?php endif; ?>
                                                        <?php if($order->delivery_address): ?><p class="mt-1 max-w-48 text-xs text-gray-400"><?php echo e($order->fulfillment_method === 'pickup' ? 'Pickup at: ' : ''); ?><?php echo e($order->delivery_address); ?></p><?php endif; ?>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex flex-col items-start gap-2">
                                                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold text-black <?php echo e($order->status === 'accepted' ? 'bg-emerald-100' : 'bg-amber-100'); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                                                            <?php if($order->status === 'pending' && $order->shipping_zone === 'outside_davao' && $order->shipping_fee === null && Auth::user()?->canManageOrders()): ?>
                                                                <form method="POST" action="<?php echo e(route('dashboard.orders.shipping-fee', $order)); ?>" class="flex flex-wrap items-end gap-2">
                                                                    <?php echo csrf_field(); ?>
                                                                    <label class="text-xs text-gray-300">
                                                                        Shipping fee
                                                                        <input type="number" name="shipping_fee" min="0" max="100000" step="0.01" value="<?php echo e($order->shipping_fee); ?>" required
                                                                               class="mt-1 block w-28 rounded-md border-gray-300 text-sm text-gray-900 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                                                    </label>
                                                                    <button type="submit" class="rounded-lg bg-purple-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-purple-800"><?php echo e($order->status === 'accepted' ? 'Update fee' : 'Confirm fee'); ?></button>
                                                                </form>
                                                            <?php elseif($order->status === 'pending' && Auth::user()?->canManageOrders()): ?>
                                                                <form method="POST" action="<?php echo e(route('dashboard.orders.accept', $order)); ?>" class="inline-block" onsubmit="const button = this.querySelector('button[type=submit]'); button.disabled = true; button.textContent = 'Accepting...';">
                                                                    <?php echo csrf_field(); ?>
                                                                    <button type="submit" class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-800">Accept</button>
                                                                </form>
                                                            <?php elseif($order->status === 'accepted'): ?>
                                                                <div class="flex flex-wrap items-center gap-2">
                                                                    <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-black" aria-disabled="true">Order accepted</span>
                                                                    <?php if($order->sale): ?>
                                                                        <a href="<?php echo e(route('pos.receipt', $order->sale)); ?>" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:border-purple-600 hover:text-purple-700">View receipt</a>
                                                                    <?php endif; ?>
                                                                    <?php if($order->shipping_zone === 'outside_davao' && Auth::user()?->canManageOrders()): ?>
                                                                        <form method="POST" action="<?php echo e(route('dashboard.orders.shipping-fee', $order)); ?>" class="flex flex-wrap items-end gap-2">
                                                                            <?php echo csrf_field(); ?>
                                                                            <label class="text-xs text-gray-300">
                                                                                Shipping fee
                                                                                <input type="number" name="shipping_fee" min="0" max="100000" step="0.01" value="<?php echo e($order->shipping_fee); ?>" required
                                                                                       class="mt-1 block w-28 rounded-md border-gray-300 text-sm text-gray-900 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                                                            </label>
                                                                            <button type="submit" class="rounded-lg bg-purple-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-purple-800">Update fee</button>
                                                                        </form>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <details class="mt-2 w-full max-w-56 rounded-lg border border-white/20 p-2">
                                                                    <summary class="cursor-pointer text-xs font-bold text-white">Edit accepted order</summary>
                                                                    <form method="POST" action="<?php echo e(route('dashboard.orders.update', $order)); ?>" class="mt-3 space-y-2">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('PATCH'); ?>
                                                                        <label class="block text-xs text-gray-300">Customer name
                                                                            <input type="text" name="customer_name" value="<?php echo e($order->customer_name); ?>" maxlength="255" required class="mt-1 block w-full rounded-md border-gray-300 text-sm text-gray-900">
                                                                        </label>
                                                                        <label class="block text-xs text-gray-300">Email (optional)
                                                                            <input type="email" name="customer_email" value="<?php echo e($order->customer_email); ?>" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 text-sm text-gray-900">
                                                                        </label>
                                                                        <label class="block text-xs text-gray-300">Phone
                                                                            <input type="tel" name="customer_phone" value="<?php echo e($order->customer_phone); ?>" maxlength="40" class="mt-1 block w-full rounded-md border-gray-300 text-sm text-gray-900">
                                                                        </label>
                                                                        <label class="block text-xs text-gray-300">Delivery / pickup address
                                                                            <textarea name="delivery_address" rows="2" maxlength="2000" class="mt-1 block w-full rounded-md border-gray-300 text-sm text-gray-900"><?php echo e($order->delivery_address); ?></textarea>
                                                                        </label>
                                                                        <button type="submit" class="rounded-lg bg-purple-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-purple-800">Save details</button>
                                                                    </form>
                                                                </details>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No customer orders yet.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <?php if(Auth::user()->hasPermission('inventory.manage')): ?>
                                <a href="<?php echo e(route('inventory.index')); ?>" class="inline-block rounded-lg bg-purple-600 px-5 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-purple-700">VIEW INVENTORY</a>
                            <?php endif; ?>
                            <?php if(Auth::user()->hasPermission('sales.create')): ?>
                                <a href="<?php echo e(route('pos.index')); ?>" class="inline-block rounded-lg border border-white/30 bg-white/5 px-5 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-white/10">OPEN POS</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h2 class="text-xl font-black text-gray-900">Quick Actions</h2>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <?php $__currentLoopData = [
                                ['inventory.manage', 'inventory.index', 'Inventory', 'Check stock levels'],
                                ['sales.create', 'pos.index', 'POS', 'Process sales and print receipts'],
                                ['quotations.manage', 'quotation.index', 'Quotations', 'Create customer quotes'],
                                ['builds.manage', 'buildpc.index', 'Build PC', 'Create custom desktop builds'],
                                ['customers.manage', 'customers.index', 'Customers', 'Manage customer records and feedback'],
                                ['suppliers.manage', 'suppliers.index', 'Suppliers', 'Maintain supplier contacts'],
                                ['warranty.verify', 'warranty.index', 'Warranty & Service', 'Verify coverage and update cases'],
                                ['expenses.manage', 'expenses.index', 'Expenses', 'Record business expenses'],
                                ['reports.view', 'reports.index', 'Reports', 'Generate period reports'],
                                ['employees.manage', 'employees.index', 'Employees & Roles', 'Manage accounts and permissions'],
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$permission, $routeName, $title, $description]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(Auth::user()->hasPermission($permission)): ?>
                                    <a href="<?php echo e(route($routeName)); ?>" class="rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                        <p class="font-bold text-gray-900"><?php echo e($title); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e($description); ?></p>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                </section>

                <section aria-label="Store summary" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    <?php $__currentLoopData = [
                        ['Total Products', $products, 'text-purple-700'],
                        ['Total Orders', $totalOrders, 'text-gray-900'],
                        ['Pending Orders', $pendingOrders, 'text-amber-700'],
                        ['Accepted Orders', $acceptedOrders, 'text-emerald-700'],
                        ['Low-stock Products', $lowStock, 'text-red-700'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $count, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-500"><?php echo e($label); ?></p>
                            <p class="mt-2 text-3xl font-black tabular-nums <?php echo e($color); ?>"><?php echo e($count); ?></p>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-black text-gray-900">Recent customer build requests</h2>
                            <p class="text-sm text-gray-500"><?php echo e($pendingBuildRequests); ?> pending request(s)</p>
                        </div>
                        <a href="<?php echo e(route('buildpc.index')); ?>" class="text-sm font-semibold text-purple-700 hover:underline">Manage requests</a>
                    </div>
                    <?php $__empty_1 = true; $__currentLoopData = $recentCustomerBuilds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="flex flex-col gap-2 border-t border-gray-100 py-3 first:border-0 first:pt-0 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900"><?php echo e($build->build_number); ?> · <?php echo e($build->customer_name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($build->user?->email); ?> · <?php echo e($build->created_at->format('M j, Y')); ?></p>
                                <?php if($build->notes): ?>
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700"><?php echo e($build->notes); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-bold uppercase <?php echo e($build->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'); ?>"><?php echo e(ucfirst($build->status)); ?></span>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="border-t border-gray-100 pt-3 text-sm text-gray-500">No customer build requests yet.</p>
                    <?php endif; ?>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 p-5">
                        <h2 class="text-xl font-black text-gray-900">Recent Sales</h2>
                        <a href="<?php echo e(route('pos.index')); ?>" class="text-sm font-semibold text-purple-700 hover:underline">All receipts</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Receipt</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Date</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3">Document</th></tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $saleVat = \App\Support\PhilippineVat::breakdown((float) $sale->total_amount);
                                    ?>
                                    <tr class="border-t border-gray-200"><td class="px-4 py-3 font-semibold text-gray-900">#<?php echo e($sale->id); ?></td><td class="px-4 py-3"><?php echo e($sale->customer_name ?: 'Customer not recorded'); ?></td><td class="px-4 py-3"><?php echo e($sale->items_count); ?><?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="block text-xs text-gray-500"><?php echo e($item->product_name); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></td><td class="px-4 py-3"><?php echo e($sale->created_at->format('M j, Y')); ?></td><td class="px-4 py-3 text-right font-semibold">₱<?php echo e(number_format($saleVat['total'], 2)); ?><span class="block text-[10px] font-normal text-gray-500">VAT included · ₱<?php echo e(number_format($saleVat['vat'], 2)); ?></span></td><td class="px-4 py-3"><a href="<?php echo e(route('pos.receipt', $sale)); ?>" class="rounded border border-gray-300 px-3 py-1.5 text-xs font-semibold hover:border-purple-600">View receipt</a></td></tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No sales recorded.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 p-5">
                        <h2 class="text-xl font-black text-gray-900">Stock-In Transactions</h2>
                        <a href="<?php echo e(route('stock-in.index')); ?>" class="text-sm font-semibold text-purple-700 hover:underline">All deliveries</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Supplier</th><th class="px-4 py-3">Invoice Number</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Document</th></tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentStockIns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stockIn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="border-t border-gray-200"><td class="px-4 py-3"><?php echo e($stockIn->received_at->format('M j, Y')); ?></td><td class="px-4 py-3 font-semibold"><?php echo e($stockIn->supplier_name); ?></td><td class="px-4 py-3"><?php echo e($stockIn->invoice_number); ?></td><td class="px-4 py-3"><?php echo e($stockIn->items_count); ?></td><td class="px-4 py-3"><?php if($stockIn->delivery_document_path): ?><a href="<?php echo e(asset('storage/'.$stockIn->delivery_document_path)); ?>" target="_blank" rel="noopener" class="font-semibold text-purple-700 hover:underline">View document</a><?php else: ?><span class="text-gray-400">None</span><?php endif; ?></td></tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No delivery transactions recorded.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    <?php else: ?>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <?php if(session('status')): ?>
                    <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"><?php echo e(session('status')); ?></div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                    <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><?php echo e($errors->first()); ?></div>
                <?php endif; ?>
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-gray-500">Welcome back</p>
                        <h1 class="mt-2 text-3xl font-black text-gray-900"><?php echo e(Auth::user()->name); ?></h1>
                    </div>
                    <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                        Back to Main Dashboard
                    </a>
                </div>

<?php
    $allOrders = $customerStoreOrders->concat($purchaseHistory)->sortByDesc('created_at');
?>

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-gray-900">My Store Orders</h2>
                <p class="mt-1 text-sm font-medium text-gray-500">Purchase History</p>
            </div>
            <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-purple-700">
                <?php echo e($allOrders->count()); ?> order(s)
            </span>
        </div>

        <?php if($allOrders->isEmpty()): ?>
            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center">
                <p class="text-lg font-semibold text-gray-700">No purchases yet.</p>
                <p class="mt-2 text-sm text-gray-500">Your recent orders and product purchases will appear here.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $allOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $orderVat = \App\Support\PhilippineVat::breakdown((float) $order->total_amount);
                    ?>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                        <div class="flex flex-col gap-3 border-b border-gray-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">
                                    <?php echo e(isset($order->status) ? 'Order' : 'Receipt'); ?> #<?php echo e($order->id); ?>

                                </p>
                                <p class="mt-1 text-sm text-gray-600"><?php echo e($order->created_at->format('F d, Y h:i A')); ?></p>
                            </div>
                            <div class="text-left sm:text-right">
                                <?php if(isset($order->status)): ?>
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase text-black <?php echo e($order->status === 'accepted' ? 'bg-emerald-100' : 'bg-amber-100'); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                                    <p class="mt-2 text-xs text-gray-500">Payment: <?php echo e(ucwords(str_replace('_', ' ', $order->payment_method))); ?> · <?php echo e(ucfirst($order->fulfillment_method)); ?></p>
                                    <?php if($order->delivery_address): ?><p class="mt-1 text-xs text-gray-500"><?php echo e($order->fulfillment_method === 'pickup' ? 'Pickup at: ' : 'Delivery to: '); ?><?php echo e($order->delivery_address); ?></p><?php endif; ?>
                                    <?php if($order->shipping_zone === 'outside_davao' && $order->shipping_fee === null): ?>
                                        <p class="mt-1 text-xs text-amber-700">Shipping fee will be confirmed by staff before acceptance.</p>
                                    <?php elseif($order->shipping_fee !== null && $order->shipping_fee > 0): ?>
                                        <p class="mt-1 text-xs text-gray-500">Shipping: ₱<?php echo e(number_format($order->shipping_fee, 2)); ?></p>
                                    <?php endif; ?>
                                    <?php if($order->sale): ?>
                                        <a href="<?php echo e(route('pos.receipt', $order->sale)); ?>" class="mt-2 inline-flex text-xs font-semibold text-purple-700 hover:underline">View receipt #<?php echo e($order->sale->id); ?></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <p class="mt-1 text-xl font-black text-gray-900">
                                    <?php if(isset($order->status) && $order->shipping_zone === 'outside_davao' && $order->shipping_fee === null): ?>
                                        Subtotal ₱<?php echo e(number_format($order->total_amount, 2)); ?>

                                    <?php else: ?>
                                        ₱<?php echo e(number_format($order->total_amount, 2)); ?>

                                    <?php endif; ?>
                                </p>
                                <p class="mt-1 text-xs text-gray-500">Includes ₱<?php echo e(number_format($orderVat['vat'], 2)); ?> Philippine VAT (12%).</p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between gap-4 border-b border-gray-200 pb-2 last:border-0 last:pb-0">
                                    <div>
                                        <p class="font-semibold text-gray-900"><?php echo e($item->product_name); ?> × <?php echo e($item->quantity); ?></p>
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

<section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900">My PC Builds</h2>
            <p class="mt-1 text-sm text-gray-500">Follow each custom build from review through completion.</p>
        </div>
        <a href="<?php echo e(route('buildpc.customer')); ?>" class="rounded-lg bg-purple-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-purple-800">Build a PC</a>
    </div>
    <div class="space-y-4 p-6">
        <?php $__empty_1 = true; $__currentLoopData = $customerBuilds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="rounded-xl border border-gray-200 p-4 sm:p-5">
                <div class="flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Build ID · <?php echo e($build->build_number); ?></p>
                        <h3 class="mt-1 text-lg font-bold text-gray-900"><?php echo e($build->customer_name); ?></h3>
                        <p class="text-sm text-gray-500">Build date: <?php echo e($build->created_at->format('M j, Y')); ?></p>
                    </div>
                    <span class="w-fit rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide <?php echo e(in_array($build->status, ['ready', 'completed'], true) ? 'bg-emerald-100 text-emerald-800' : ($build->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')); ?>"><?php echo e(ucfirst($build->status)); ?></span>
                </div>
                <div class="mt-4 grid gap-5 lg:grid-cols-[minmax(0,1fr)_18rem]">
                    <div class="grid gap-x-6 gap-y-2 sm:grid-cols-2">
                        <?php $__currentLoopData = $build->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="text-sm">
                                <span class="font-semibold text-gray-500"><?php echo e($item->product->category); ?>:</span>
                                <span class="text-gray-800"><?php echo e($item->product->name); ?> × <?php echo e($item->quantity); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($build->notes): ?>
                            <p class="col-span-full whitespace-pre-line text-sm text-gray-700"><?php echo e($build->notes); ?></p>
                        <?php endif; ?>
                        <div class="text-sm font-black text-gray-900">
                            <?php if($build->estimated_cost !== null): ?>
                                <?php
                                    $buildVat = \App\Support\PhilippineVat::breakdown((float) $build->estimated_cost);
                                ?>
                                Estimated price: ₱<?php echo e(number_format($buildVat['total'], 2)); ?>

                                <span class="block text-xs font-normal text-gray-500">Includes ₱<?php echo e(number_format($buildVat['vat'], 2)); ?> Philippine VAT (12%).</span>
                            <?php else: ?>
                                Quote pending
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <?php $__currentLoopData = ['Product photo' => $build->product_photo_path, 'Before photo' => $build->before_photo_path, 'After photo' => $build->after_photo_path]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <p class="mb-1 text-[10px] font-bold uppercase tracking-wide text-gray-500"><?php echo e($label); ?></p>
                                <?php if($path): ?>
                                    <a href="<?php echo e(asset('storage/'.$path)); ?>" target="_blank" rel="noopener"><img src="<?php echo e(asset('storage/'.$path)); ?>" alt="<?php echo e($label); ?> for <?php echo e($build->build_number); ?>" class="aspect-square w-full rounded-lg object-cover"></a>
                                <?php else: ?>
                                    <div class="flex aspect-square items-center justify-center rounded-lg bg-gray-100 text-center text-[10px] text-gray-400">Photo pending</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-7 text-center">
                <p class="font-semibold text-gray-800">No PC builds yet.</p>
                <p class="mt-1 text-sm text-gray-500">Create a custom build and its progress will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views\dashboard.blade.php ENDPATH**/ ?>