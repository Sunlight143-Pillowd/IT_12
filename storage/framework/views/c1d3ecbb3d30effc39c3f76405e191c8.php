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
        $saleVat = \App\Support\PhilippineVat::breakdown((float) $sale->total_amount);
    ?>

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Receipt #<?php echo e($sale->id); ?></h2>
            <div class="flex gap-2">
                <a href="<?php echo e(route('pos.index')); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to POS</a>
                <button type="button" onclick="window.print()" class="rounded bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700">Print receipt</button>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <p class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"><?php echo e(session('success')); ?></p>
            <?php endif; ?>
            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm print:border-0 print:shadow-none">
                <div class="border-b border-gray-200 pb-4">
                    <p class="text-xs font-bold uppercase text-gray-500">Sales receipt #<?php echo e($sale->id); ?></p>
                    <?php if($sale->store_order_id): ?>
                        <p class="mt-1 text-xs font-semibold text-purple-700">Customer order #<?php echo e($sale->store_order_id); ?></p>
                    <?php endif; ?>
                    <p class="mt-2 text-xl font-bold text-gray-900"><?php echo e($sale->customer_name ?: 'Customer not recorded'); ?></p>
                    <p class="mt-1 text-sm text-gray-600"><?php echo e($sale->created_at->format('M j, Y g:i A')); ?></p>
                </div>

                <div class="space-y-4 py-5">
                    <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start justify-between gap-4 text-sm">
                            <div class="min-w-0">
                                <p class="wrap-break-word font-semibold text-gray-900"><?php echo e($item->product_name); ?></p>
                                <p class="mt-1 text-gray-500">₱<?php echo e(number_format($item->unit_price, 2)); ?> × <?php echo e($item->quantity); ?></p>
                                <?php if($item->product?->description): ?>
                                    <p class="mt-1 wrap-break-word text-xs text-gray-600"><?php echo e($item->product->description); ?></p>
                                <?php endif; ?>
                                <?php if($item->product?->sku): ?>
                                    <p class="mt-1 text-xs text-gray-500">SKU: <?php echo e($item->product->sku); ?></p>
                                <?php endif; ?>
                                <?php $__currentLoopData = $item->units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <p class="mt-1 text-xs text-gray-500">Serial: <?php echo e($unit->serial_number ?? 'Not recorded'); ?> · Lot: <?php echo e($unit->lot_code ?: 'Not recorded'); ?> · Warranty through: <?php echo e($unit->warranty_ends_at?->format('M j, Y') ?: 'Not recorded'); ?></p>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <p class="shrink-0 font-semibold text-gray-900">₱<?php echo e(number_format($item->subtotal, 2)); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <?php if($sale->shipping_fee > 0): ?>
                    <div class="flex items-center justify-between border-t border-gray-200 py-4 text-sm">
                        <span class="font-semibold text-gray-700">Shipping</span>
                        <span class="font-semibold text-gray-900">₱<?php echo e(number_format($sale->shipping_fee, 2)); ?></span>
                    </div>
                <?php endif; ?>
                <?php $__currentLoopData = $sale->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="space-y-1 border-t border-gray-200 py-4 text-sm">
                        <div class="flex justify-between gap-3"><span>Payment method</span><strong class="capitalize"><?php echo e(str_replace('_', ' ', $payment->method)); ?></strong></div>
                        <div class="flex justify-between gap-3"><span>Paid</span><strong>₱<?php echo e(number_format((float) $payment->amount, 2)); ?></strong></div>
                        <?php if((float) $payment->amount_tendered > (float) $payment->amount): ?>
                            <div class="flex justify-between gap-3"><span>Cash received</span><span>₱<?php echo e(number_format((float) $payment->amount_tendered, 2)); ?></span></div>
                            <div class="flex justify-between gap-3"><span>Change</span><span>₱<?php echo e(number_format((float) $payment->amount_tendered - (float) $payment->amount, 2)); ?></span></div>
                        <?php endif; ?>
                        <?php if($payment->reference): ?>
                            <div class="flex justify-between gap-3"><span>Reference</span><span><?php echo e($payment->reference); ?></span></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="space-y-2 border-t border-gray-200 py-4 text-sm">
                    <p class="text-xs text-gray-500">Prices include 12% Philippine VAT.</p>
                    <div class="flex items-center justify-between text-gray-700">
                        <span>Amount before VAT</span>
                        <span>₱<?php echo e(number_format($saleVat['net'], 2)); ?></span>
                    </div>
                    <div class="flex items-center justify-between text-gray-700">
                        <span>VAT (12%, included)</span>
                        <span>₱<?php echo e(number_format($saleVat['vat'], 2)); ?></span>
                    </div>
                </div>
                <div class="flex items-center justify-between border-t border-gray-200 pt-4 text-lg font-bold text-gray-900">
                    <span>Total (VAT included)</span>
                    <span>₱<?php echo e(number_format($saleVat['total'], 2)); ?></span>
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
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->
</div>
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views\pos-receipt.blade.php ENDPATH**/ ?>