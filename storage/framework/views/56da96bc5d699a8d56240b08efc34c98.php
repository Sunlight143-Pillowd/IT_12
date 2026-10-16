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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit supplier delivery</h2>
            <a href="<?php echo e(route('stock-in.index')); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600 hover:text-purple-700">Back to Stock In</a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if($errors->any()): ?>
                <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc space-y-1 pl-5">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-gray-900">Delivery details</h3>
                <form method="POST" action="<?php echo e(route('stock-in.update', $stockIn)); ?>" enctype="multipart/form-data" class="space-y-5">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <label class="text-sm font-semibold text-gray-700">Supplier
                            <select name="supplier_id" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                                <option value="">No linked supplier</option>
                                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($supplier->id); ?>" <?php if(old('supplier_id', $stockIn->supplier_id) == $supplier->id): echo 'selected'; endif; ?>><?php echo e($supplier->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <input name="supplier_name" required value="<?php echo e(old('supplier_name', $stockIn->supplier_name)); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">Supplier contact
                            <input name="supplier_contact" value="<?php echo e(old('supplier_contact', $stockIn->supplier_contact)); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">Invoice number
                            <input name="invoice_number" required value="<?php echo e(old('invoice_number', $stockIn->invoice_number)); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">Date received
                            <input type="date" name="received_at" required value="<?php echo e(old('received_at', $stockIn->received_at->toDateString())); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <?php $__currentLoopData = [
                            'delivery_document' => ['Delivery document', $stockIn->delivery_document_path, '.pdf,.jpg,.jpeg,.png,.webp'],
                            'before_photo' => ['Before delivery photo', $stockIn->before_photo_path, 'image/jpeg,image/png,image/webp'],
                            'after_photo' => ['After delivery photo', $stockIn->after_photo_path, 'image/jpeg,image/png,image/webp'],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => [$label, $path, $accept]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="text-sm font-semibold text-gray-700"><?php echo e($label); ?>

                                <?php if($path): ?>
                                    <a href="<?php echo e(asset('storage/'.$path)); ?>" target="_blank" rel="noopener" class="mt-1 block text-xs font-medium text-purple-700 hover:underline">View current file</a>
                                <?php endif; ?>
                                <input type="file" name="<?php echo e($field); ?>" accept="<?php echo e($accept); ?>" class="mt-1 block w-full text-sm font-normal">
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="rounded border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        Delivered products, quantities, unit costs, warranties, and serial numbers are read-only. You can still update supplier details, notes, and attached files.
                    </div>
                    <div class="space-y-2">
                        <h4 class="font-bold text-gray-900">Delivered items <span class="font-normal text-gray-500">(read-only)</span></h4>
                        <?php $__currentLoopData = $stockIn->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $itemVat = \App\Support\PhilippineVat::breakdown((float) $item->unit_cost * $item->quantity);
                            ?>
                            <div class="rounded border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                                <span class="font-semibold"><?php echo e($item->product->name); ?></span>
                                <span> · <?php echo e($item->quantity); ?> units · ₱<?php echo e(number_format((float) $item->unit_cost, 2)); ?> each · ₱<?php echo e(number_format($itemVat['total'], 2)); ?> total (VAT included)</span>
                                <span class="mt-1 block text-xs text-gray-500">VAT (12%, included): ₱<?php echo e(number_format($itemVat['vat'], 2)); ?></span>
                                <?php $__currentLoopData = $item->units->pluck('serial_number')->filter(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serialNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="mt-1 block text-xs text-gray-500">Serial: <?php echo e($serialNumber); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <label class="block text-sm font-semibold text-gray-700">Delivery notes
                        <textarea name="notes" rows="2" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal"><?php echo e(old('notes', $stockIn->notes)); ?></textarea>
                    </label>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-purple-700">Save changes</button>
                    </div>
                </form>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views\stock-in-edit.blade.php ENDPATH**/ ?>