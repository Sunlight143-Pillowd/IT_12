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
                        <div class="grid gap-8 lg:grid-cols-2">
                            <div class="min-w-0 overflow-hidden rounded-xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                    <h1 class="text-xl font-black text-white">Customer Orders</h1>
                                    <span class="rounded-full bg-purple-500/20 px-3 py-1 text-xs font-bold text-purple-200"><?php echo e($storeOrders->count()); ?> order(s)</span>
                                </div>
                                <div class="overflow-x-auto rounded-lg border border-white/10">
                                    <table class="min-w-[760px] text-left text-sm text-gray-200">
                                        <thead class="bg-white/10 text-xs uppercase text-gray-300">
                                            <tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Date</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3">Payment / Fulfillment</th><th class="px-4 py-3">Status / Action</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $storeOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                                                    <td class="px-4 py-3 text-right font-semibold text-white">₱<?php echo e(number_format($order->total_amount, 2)); ?></td>
                                                    <td class="px-4 py-3">
                                                        <p class="font-semibold"><?php echo e(ucwords(str_replace('_', ' ', $order->payment_method))); ?></p>
                                                        <p class="text-xs text-gray-400"><?php echo e(ucfirst($order->fulfillment_method)); ?></p>
                                                        <?php if($order->delivery_address): ?><p class="mt-1 max-w-48 text-xs text-gray-400"><?php echo e($order->delivery_address); ?></p><?php endif; ?>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center gap-2">
                                                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold text-black <?php echo e($order->status === 'accepted' ? 'bg-emerald-100' : 'bg-amber-100'); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                                                            <?php if($order->status === 'pending' && Auth::user()?->canManageOrders()): ?>
                                                                <form method="POST" action="<?php echo e(route('dashboard.orders.accept', $order)); ?>" class="inline-block">
                                                                    <?php echo csrf_field(); ?>
                                                                    <button type="submit" class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-800">Accept order</button>
                                                                </form>
                                                            <?php elseif($order->status === 'accepted'): ?>
                                                                <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-black" aria-disabled="true">Order accepted</span>
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
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="<?php echo e(route('inventory.index')); ?>" class="inline-block rounded-lg bg-purple-600 px-5 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-purple-700">VIEW INVENTORY</a>
                            <a href="<?php echo e(route('pos.index')); ?>" class="inline-block rounded-lg border border-white/30 bg-white/5 px-5 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-white/10">OPEN POS</a>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h2 class="text-xl font-black text-gray-900">Quick Actions</h2>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <a href="<?php echo e(route('inventory.index')); ?>" class="rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Inventory</p>
                                <p class="text-sm text-gray-500">Check stock levels</p>
                            </a>
                            <a href="<?php echo e(route('pos.index')); ?>" class="rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">POS</p>
                                <p class="text-sm text-gray-500">Open cashier</p>
                            </a>
                            <a href="<?php echo e(route('quotation.index')); ?>" class="rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Quotations</p>
                                <p class="text-sm text-gray-500">Create customer quotes</p>
                            </a>
                            <a href="<?php echo e(route('buildpc.index')); ?>" class="rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Build PC</p>
                                <p class="text-sm text-gray-500">Create custom desktop builds</p>
                            </a>
                        </div>
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
                                    <tr class="border-t border-gray-200"><td class="px-4 py-3 font-semibold text-gray-900">#<?php echo e($sale->id); ?></td><td class="px-4 py-3"><?php echo e($sale->customer_name ?: 'Customer not recorded'); ?></td><td class="px-4 py-3"><?php echo e($sale->items_count); ?><?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="block text-xs text-gray-500"><?php echo e($item->product_name); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></td><td class="px-4 py-3"><?php echo e($sale->created_at->format('M j, Y')); ?></td><td class="px-4 py-3 text-right font-semibold">₱<?php echo e(number_format($sale->total_amount, 2)); ?></td><td class="px-4 py-3"><a href="<?php echo e(route('pos.receipt', $sale)); ?>" class="rounded border border-gray-300 px-3 py-1.5 text-xs font-semibold hover:border-purple-600">View receipt</a></td></tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No sales recorded.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-5">
                        <div>
                            <h2 class="text-xl font-black text-gray-900">Customer Build / PC Build Management</h2>
                            <p class="mt-1 text-sm text-gray-500">Review components, update the build workflow, and attach progress photos.</p>
                        </div>
                        <a href="<?php echo e(route('buildpc.customer')); ?>" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">Customer build page</a>
                    </div>
                    <div class="space-y-4 p-5">
                        <?php $__empty_1 = true; $__currentLoopData = $customerBuildManagement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <article class="rounded-xl border border-gray-200 p-4">
                                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-lg font-black text-gray-900"><?php echo e($build->build_number); ?></h3>
                                            <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase text-purple-800"><?php echo e(ucfirst($build->status)); ?></span>
                                        </div>
                                        <p class="mt-1 text-sm font-semibold text-gray-800"><?php echo e($build->customer_name); ?></p>
                                        <p class="text-xs text-gray-500">Build date: <?php echo e($build->created_at->format('M j, Y')); ?></p>
                                        <ul class="mt-3 grid gap-x-6 gap-y-1 text-sm text-gray-700 sm:grid-cols-2">
                                            <?php $__currentLoopData = $build->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($item->product->category); ?>: <?php echo e($item->product->name); ?> × <?php echo e($item->quantity); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                        <p class="mt-3 font-black text-gray-900">Total price: ₱<?php echo e(number_format($build->total_cost, 2)); ?></p>
                                    </div>

                                    <form method="POST" action="<?php echo e(route('dashboard.customer-builds.update', $build)); ?>" enctype="multipart/form-data" class="w-full space-y-4 rounded-xl bg-gray-50 p-4 xl:max-w-2xl">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">
                                            <label class="block text-sm font-semibold text-gray-700">
                                                Build status
                                                <select name="status" class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-purple-500 focus:ring-purple-500">
                                                    <option value="<?php echo e($build->status); ?>"><?php echo e(ucfirst($build->status)); ?> (current)</option>
                                                    <?php $__currentLoopData = $buildStatusOptions[$build->status] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nextStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($nextStatus); ?>"><?php echo e(ucfirst($nextStatus)); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </label>
                                            <button type="submit" class="rounded-lg bg-purple-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-purple-800">Save updates</button>
                                        </div>
                                        <div class="grid grid-cols-3 gap-3">
                                            <?php $__currentLoopData = [
                                                'product_photo' => ['Product photo', $build->product_photo_path],
                                                'before_photo' => ['Before photo', $build->before_photo_path],
                                                'after_photo' => ['After photo', $build->after_photo_path],
                                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => [$label, $path]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $previewId = 'build-'.$build->id.'-'.$field;
                                                ?>
                                                <div class="min-w-0">
                                                    <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-gray-500"><?php echo e($label); ?></p>
                                                    <?php if($path): ?>
                                                        <img id="<?php echo e($previewId); ?>" src="<?php echo e(asset('storage/'.$path)); ?>" alt="<?php echo e($label); ?> for <?php echo e($build->build_number); ?>" class="mb-2 aspect-square w-full rounded-lg bg-white object-cover">
                                                    <?php else: ?>
                                                        <div id="<?php echo e($previewId); ?>" class="mb-2 flex aspect-square items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white text-xs text-gray-400">No photo</div>
                                                    <?php endif; ?>
                                                    <label class="flex cursor-pointer items-center justify-center gap-1 rounded-lg border border-gray-300 bg-white px-2 py-2 text-center text-xs font-bold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">
                                                        <span aria-hidden="true" class="text-base leading-none">+</span> Add Photo
                                                        <input type="file" name="<?php echo e($field); ?>" accept="image/jpeg,image/png,image/webp" class="build-photo-input sr-only" data-preview="<?php echo e($previewId); ?>">
                                                    </label>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">No customer PC builds have been submitted.</p>
                        <?php endif; ?>
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
                                    <?php if($order->delivery_address): ?><p class="mt-1 text-xs text-gray-500">Delivery to: <?php echo e($order->delivery_address); ?></p><?php endif; ?>
                                <?php endif; ?>
                                <p class="mt-1 text-xl font-black text-gray-900">₱<?php echo e(number_format($order->total_amount, 2)); ?></p>
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
                        <div class="text-sm font-black text-gray-900">Total Price: ₱<?php echo e(number_format($build->total_cost, 2)); ?></div>
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

    <script>
        document.querySelectorAll('.build-photo-input').forEach((input) => {
            input.addEventListener('change', () => {
                const file = input.files?.[0];
                const preview = document.getElementById(input.dataset.preview);
                if (!file || !preview) {
                    return;
                }

                const imageUrl = URL.createObjectURL(file);
                if (preview.tagName === 'IMG') {
                    preview.src = imageUrl;
                    return;
                }

                const image = document.createElement('img');
                image.id = preview.id;
                image.src = imageUrl;
                image.alt = 'Selected photo preview';
                image.className = 'mb-2 aspect-square w-full rounded-lg bg-white object-cover';
                preview.replaceWith(image);
            });
        });
    </script>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/dashboard.blade.php ENDPATH**/ ?>