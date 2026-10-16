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
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Business Reports</h2>
            <a href="<?php echo e(route('dashboard')); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Dashboard</a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if($errors->any()): ?>
                <ul class="list-disc rounded border border-red-200 bg-red-50 px-4 py-3 pl-8 text-sm text-red-700">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>

            <form method="GET" action="<?php echo e(route('reports.index')); ?>" class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <label class="grid gap-1 text-xs font-bold uppercase text-gray-500">From
                    <input type="date" name="start" value="<?php echo e($start); ?>" required class="rounded border-gray-300 text-sm font-normal normal-case">
                </label>
                <label class="grid gap-1 text-xs font-bold uppercase text-gray-500">Through
                    <input type="date" name="end" value="<?php echo e($end); ?>" required class="rounded border-gray-300 text-sm font-normal normal-case">
                </label>
                <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Generate reports</button>
            </form>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase text-gray-500">Sales revenue</p><p class="mt-2 text-2xl font-bold text-gray-900">₱<?php echo e(number_format((float) $salesTotal, 2)); ?></p><p class="mt-1 text-xs text-gray-500"><?php echo e($sales->count()); ?> POS transactions</p></article>
                <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase text-gray-500">Recorded expenses</p><p class="mt-2 text-2xl font-bold text-gray-900">₱<?php echo e(number_format((float) $expensesTotal, 2)); ?></p><p class="mt-1 text-xs text-gray-500"><?php echo e($expenses->count()); ?> expense entries</p></article>
                <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase text-gray-500">Credit issued</p><p class="mt-2 text-2xl font-bold text-gray-900">₱<?php echo e(number_format((float) $credits->sum('amount_due'), 2)); ?></p><p class="mt-1 text-xs text-gray-500">Outstanding on period credits: ₱<?php echo e(number_format((float) $creditBalanceTotal, 2)); ?></p></article>
                <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase text-gray-500">Low-stock products</p><p class="mt-2 text-2xl font-bold text-red-700"><?php echo e($lowStock->count()); ?></p><p class="mt-1 text-xs text-gray-500">Current on-hand count</p></article>
            </section>

            <section class="grid gap-6 xl:grid-cols-2">
                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-4"><h3 class="font-bold text-gray-900">Sales</h3></div>
                    <div class="max-h-96 overflow-auto">
                        <table class="w-full text-left text-sm"><thead class="sticky top-0 bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-3">Date</th><th class="p-3">Customer</th><th class="p-3 text-right">Total</th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr><td class="p-3"><?php echo e($sale->created_at->format('M j, Y')); ?></td><td class="p-3"><?php echo e($sale->customer_name ?: 'Walk-in'); ?></td><td class="p-3 text-right">₱<?php echo e(number_format((float) $sale->total_amount, 2)); ?></td></tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="3" class="p-4 text-center text-gray-500">No sales in this period.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-4"><h3 class="font-bold text-gray-900">Expenses</h3></div>
                    <div class="max-h-96 overflow-auto">
                        <table class="w-full text-left text-sm"><thead class="sticky top-0 bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-3">Date</th><th class="p-3">Type</th><th class="p-3 text-right">Amount</th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr><td class="p-3"><?php echo e($expense->expense_date->format('M j, Y')); ?></td><td class="p-3"><?php echo e($expense->type); ?></td><td class="p-3 text-right">₱<?php echo e(number_format((float) $expense->amount, 2)); ?></td></tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="3" class="p-4 text-center text-gray-500">No expenses in this period.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-4"><h3 class="font-bold text-gray-900">Credit Balances</h3></div>
                    <div class="max-h-96 overflow-auto">
                        <table class="w-full text-left text-sm"><thead class="sticky top-0 bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-3">Date</th><th class="p-3">Customer</th><th class="p-3 text-right">Balance</th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $credits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $credit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr><td class="p-3"><?php echo e($credit->credit_date->format('M j, Y')); ?></td><td class="p-3"><?php echo e($credit->customer->name); ?></td><td class="p-3 text-right">₱<?php echo e(number_format((float) $credit->balance_due, 2)); ?></td></tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="3" class="p-4 text-center text-gray-500">No credit purchases in this period.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-4"><h3 class="font-bold text-gray-900">Inventory & Low-Stock Alerts</h3></div>
                    <div class="max-h-96 overflow-auto">
                        <table class="w-full text-left text-sm"><thead class="sticky top-0 bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-3">Product</th><th class="p-3">Stock</th><th class="p-3">Last reorder</th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php $__currentLoopData = $inventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="<?php echo e($product->stock_quantity <= $product->low_stock_threshold ? 'bg-red-50 text-red-800' : ''); ?>">
                                        <td class="p-3"><?php echo e($product->name); ?></td><td class="p-3"><?php echo e($product->stock_quantity); ?> / alert at <?php echo e($product->low_stock_threshold); ?></td>
                                        <td class="p-3"><?php echo e($product->last_reorder_date?->format('M j, Y') ?: 'Never recorded'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        </div>
    </div>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views\reports.blade.php ENDPATH**/ ?>