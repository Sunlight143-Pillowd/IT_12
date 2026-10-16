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
        $buildAmount = $pcBuild->user_id !== null && $pcBuild->estimated_cost !== null
            ? (float) $pcBuild->estimated_cost
            : (float) $pcBuild->total_cost;
        $buildVat = \App\Support\PhilippineVat::breakdown($buildAmount);
    ?>

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">PC Build Details</h2>
            <a href="<?php echo e(route('buildpc.index')); ?>" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                Back to Build PC
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500"><?php echo e($pcBuild->build_number); ?></p>
                        <h3 class="mt-2 text-2xl font-black text-gray-900"><?php echo e($pcBuild->customer_name ?: 'Customer not recorded'); ?></h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-purple-700"><?php echo e($pcBuild->status); ?></span>
                        <?php if($pcBuild->user_id !== null): ?>
                            <span class="rounded-full bg-gray-200 px-3 py-1 text-xs font-bold uppercase tracking-wide text-gray-700">
                                <?php if($pcBuild->estimated_cost !== null): ?>
                                    Estimated price: ₱<?php echo e(number_format($pcBuild->estimated_cost, 2)); ?>

                                    <span class="mt-1 block text-[10px] font-medium normal-case tracking-normal text-gray-500">Includes ₱<?php echo e(number_format($buildVat['vat'], 2)); ?> Philippine VAT (12%).</span>
                                <?php else: ?>
                                    Quote pending
                                <?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span class="rounded-full bg-gray-200 px-3 py-1 text-xs font-bold uppercase tracking-wide text-gray-700">Total: ₱<?php echo e(number_format($pcBuild->total_cost, 2)); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-xl border border-gray-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-3 py-3 font-semibold">Product</th>
                                <th class="px-3 py-3 font-semibold">Qty</th>
                                <th class="px-3 py-3 font-semibold">Unit Price</th>
                                <th class="px-3 py-3 font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $pcBuild->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-t border-gray-200">
                                    <td class="px-3 py-3 font-semibold text-gray-900"><?php echo e($item->product->name); ?></td>
                                    <td class="px-3 py-3 text-gray-700"><?php echo e($item->quantity); ?></td>
                                    <td class="px-3 py-3 text-gray-700">₱<?php echo e(number_format($item->unit_price, 2)); ?></td>
                                    <td class="px-3 py-3 font-bold text-purple-700">₱<?php echo e(number_format($item->subtotal, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <?php if($pcBuild->user_id === null || $pcBuild->estimated_cost !== null): ?>
                    <div class="mt-4 rounded-xl border border-purple-100 bg-purple-50 p-4 text-sm">
                        <p class="mb-2 text-xs font-semibold text-purple-800">Prices include 12% Philippine VAT.</p>
                        <dl class="space-y-2">
                            <div class="flex justify-between gap-4 text-gray-700"><dt>Amount before VAT</dt><dd>₱<?php echo e(number_format($buildVat['net'], 2)); ?></dd></div>
                            <div class="flex justify-between gap-4 text-gray-700"><dt>VAT (12%, included)</dt><dd>₱<?php echo e(number_format($buildVat['vat'], 2)); ?></dd></div>
                            <div class="flex justify-between gap-4 border-t border-purple-200 pt-2 font-bold text-gray-900"><dt><?php echo e($pcBuild->user_id !== null ? 'Estimated total' : 'Total'); ?> (VAT included)</dt><dd>₱<?php echo e(number_format($buildVat['total'], 2)); ?></dd></div>
                        </dl>
                    </div>
                <?php endif; ?>

                <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Notes</p>
                    <p class="mt-2 text-sm text-gray-700"><?php echo e($pcBuild->notes ?: 'No notes provided.'); ?></p>
                </div>
            </div>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views\build-pc-show.blade.php ENDPATH**/ ?>