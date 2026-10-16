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
            <h2 class="text-xl font-semibold text-gray-800">Warranty & Service Transactions</h2>
            <a href="<?php echo e(route('dashboard')); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Dashboard</a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <p class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"><?php echo e(session('success')); ?></p>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <ul class="list-disc rounded border border-red-200 bg-red-50 px-4 py-3 pl-8 text-sm text-red-700">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-lg font-bold text-gray-900">Verify Coverage</h3>
                <p class="mb-4 text-sm text-gray-600">Search by serial number, lot code, SKU, or product name to view the receipt and warranty dates.</p>
                <form method="GET" action="<?php echo e(route('warranty.index')); ?>" class="flex flex-col gap-2 sm:flex-row">
                    <input name="q" value="<?php echo e($search); ?>" autofocus placeholder="Serial, lot code, SKU, or product" class="min-w-0 flex-1 rounded border-gray-300 text-sm">
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Look up product</button>
                    <a href="<?php echo e(route('warranty.index')); ?>" class="rounded border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Clear</a>
                </form>
            </section>

            <?php if($search !== ''): ?>
                <section class="space-y-3">
                    <h3 class="text-lg font-bold text-gray-900">Matching Product Units</h3>
                    <?php $__empty_1 = true; $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $sale = $unit->saleItem?->sale;
                            $covered = $unit->warranty_ends_at && $unit->warranty_ends_at->gte(today());
                        ?>
                        <article class="grid gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:grid-cols-[1fr_auto]">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-bold text-gray-900"><?php echo e($unit->product->name); ?></h4>
                                    <span class="rounded-full px-2 py-1 text-xs font-bold <?php echo e($covered ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>"><?php echo e($covered ? 'Coverage active' : 'Coverage expired / unknown'); ?></span>
                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700"><?php echo e(ucfirst(str_replace('_', ' ', $unit->status))); ?></span>
                                </div>
                                <dl class="mt-3 grid gap-2 text-sm sm:grid-cols-2 xl:grid-cols-3">
                                    <div><dt class="text-xs text-gray-500">SKU</dt><dd><?php echo e($unit->product->sku); ?></dd></div>
                                    <div><dt class="text-xs text-gray-500">Serial number</dt><dd><?php echo e($unit->serial_number ?: 'Not recorded'); ?></dd></div>
                                    <div><dt class="text-xs text-gray-500">Lot code</dt><dd><?php echo e($unit->lot_code ?: 'Not recorded'); ?></dd></div>
                                    <div><dt class="text-xs text-gray-500">Manufactured</dt><dd><?php echo e($unit->manufactured_at?->format('M j, Y') ?: 'Not recorded'); ?></dd></div>
                                    <div><dt class="text-xs text-gray-500">Warranty through</dt><dd><?php echo e($unit->warranty_ends_at?->format('M j, Y') ?: 'Not recorded'); ?></dd></div>
                                    <div><dt class="text-xs text-gray-500">Supplier / invoice</dt><dd><?php echo e($unit->stockInItem?->stockIn?->supplier?->name ?: $unit->stockInItem?->stockIn?->supplier_name ?: 'Not recorded'); ?> · <?php echo e($unit->stockInItem?->stockIn?->invoice_number ?: '—'); ?></dd></div>
                                    <div><dt class="text-xs text-gray-500">Sale / customer</dt><dd><?php echo e($sale?->id ? 'Receipt #'.$sale->id : 'Not sold in POS'); ?> · <?php echo e($sale?->customer_name ?: '—'); ?></dd></div>
                                </dl>
                            </div>
                            <?php if($unit->status === 'sold'): ?>
                                <form method="POST" action="<?php echo e(route('warranty.cases.store')); ?>" class="grid min-w-64 gap-2 rounded-lg bg-gray-50 p-3">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="product_unit_id" value="<?php echo e($unit->id); ?>">
                                    <select name="customer_id" class="rounded border-gray-300 text-sm">
                                        <option value="">Customer (optional)</option>
                                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <select name="case_type" class="rounded border-gray-300 text-sm">
                                        <option value="warranty">Warranty case</option>
                                        <option value="replacement">Replacement</option>
                                    </select>
                                    <textarea name="description" required maxlength="3000" rows="2" placeholder="Issue or request" class="rounded border-gray-300 text-sm"></textarea>
                                    <button class="rounded bg-purple-700 px-3 py-2 text-xs font-bold text-white hover:bg-purple-800">Open service transaction</button>
                                </form>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="rounded-lg bg-white p-5 text-sm text-gray-500">No matching stock or purchase records found.</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <section class="space-y-3">
                <h3 class="text-lg font-bold text-gray-900">Recent Service Transactions</h3>
                <?php $__empty_1 = true; $__currentLoopData = $serviceCases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="grid gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:grid-cols-[1fr_auto]">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="font-bold text-gray-900"><?php echo e($case->case_number); ?></h4>
                                <span class="rounded-full bg-purple-50 px-2 py-1 text-xs font-semibold text-purple-800"><?php echo e(ucfirst(str_replace('_', ' ', $case->status))); ?></span>
                                <span class="text-xs text-gray-500"><?php echo e(ucfirst($case->case_type)); ?></span>
                            </div>
                            <p class="mt-2 text-sm text-gray-700"><?php echo e($case->description); ?></p>
                            <p class="mt-1 text-xs text-gray-500">
                                <?php echo e($case->customer?->name ?: 'Customer not linked'); ?> ·
                                <?php echo e($case->productUnit?->product?->name ?: 'Product unavailable'); ?> ·
                                <?php echo e($case->created_at->format('M j, Y')); ?>

                            </p>
                            <?php if($case->resolution): ?>
                                <p class="mt-2 text-sm text-gray-600">Resolution: <?php echo e($case->resolution); ?></p>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="<?php echo e(route('warranty.cases.update', $case)); ?>" class="grid min-w-56 gap-2">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <select name="status" class="rounded border-gray-300 text-sm">
                                <?php $__currentLoopData = ['open', 'under_review', 'replacement_in_progress', 'completed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($status); ?>" <?php if($case->status === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst(str_replace('_', ' ', $status))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <textarea name="resolution" rows="2" maxlength="3000" placeholder="Resolution / update" class="rounded border-gray-300 text-sm"><?php echo e($case->resolution); ?></textarea>
                            <button class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-xs font-bold text-purple-800 hover:bg-purple-100">Update status</button>
                        </form>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="rounded-lg bg-white p-5 text-sm text-gray-500">No service transactions have been opened.</p>
                <?php endif; ?>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views\warranty.blade.php ENDPATH**/ ?>