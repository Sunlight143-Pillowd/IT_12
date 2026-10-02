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
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Quotation #<?php echo e($quotation->id); ?></h2>
            <div class="flex gap-2">
                <a href="<?php echo e(route('quotation.index')); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Quotations</a>
                <button type="button" onclick="window.print()" class="rounded bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700">Print</button>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm print:border-0 print:shadow-none">
                <div class="flex flex-col gap-4 border-b border-gray-200 pb-5 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-500">Quotation #<?php echo e($quotation->id); ?></p>
                        <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($quotation->customer_name ?: 'Customer not recorded'); ?></p>
                        <p class="mt-1 text-sm text-gray-600"><?php echo e($quotation->customer_contact ?: 'No contact recorded'); ?></p>
                    </div>
                    <div class="sm:text-right">
                        <p class="text-xs font-bold uppercase text-gray-500">Created</p>
                        <p class="mt-1 text-sm text-gray-700"><?php echo e($quotation->created_at->format('M j, Y g:i A')); ?></p>
                    </div>
                </div>

                <section class="py-5">
                    <h3 class="text-sm font-bold uppercase text-gray-500">Purpose / Notes</h3>
                    <p class="mt-2 whitespace-pre-line text-sm text-gray-800"><?php echo e($quotation->notes ?: 'No purpose or notes were provided.'); ?></p>
                </section>

                <div class="overflow-x-auto border-y border-gray-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-3 py-3">Item</th><th class="px-3 py-3">Unit Price</th><th class="px-3 py-3">Qty</th><th class="px-3 py-3 text-right">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $quotation->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-3 font-semibold text-gray-900"><?php echo e($item->product_name); ?></td>
                                    <td class="px-3 py-3">₱<?php echo e(number_format($item->unit_price, 2)); ?></td>
                                    <td class="px-3 py-3"><?php echo e($item->quantity); ?></td>
                                    <td class="px-3 py-3 text-right font-semibold">₱<?php echo e(number_format($item->subtotal, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-5">
                    <div class="flex w-full max-w-xs justify-between text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span>₱<?php echo e(number_format($quotation->total_amount, 2)); ?></span>
                    </div>
                </div>
            </article>
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
<?php endif; ?><div>
    <!-- People find pleasure in different ways. I find it in keeping my mind clear. - Marcus Aurelius -->
</div>
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/quotation-show.blade.php ENDPATH**/ ?>