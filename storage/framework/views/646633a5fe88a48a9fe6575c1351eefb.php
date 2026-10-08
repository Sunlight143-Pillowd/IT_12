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
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Edit Inventory Item</h2>
            <a href="<?php echo e(route('inventory.index')); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Inventory</a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="<?php echo e(route('inventory.products.update', $product)); ?>" enctype="multipart/form-data" class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="text-sm font-semibold text-gray-700">Name
                        <input name="name" required value="<?php echo e(old('name', $product->name)); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Type
                        <select name="type" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                            <?php $__currentLoopData = $productTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php if(old('type', $product->type) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Category
                        <input name="category" required list="product-categories" value="<?php echo e(old('category', $product->category)); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        <datalist id="product-categories">
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </datalist>
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Selling price
                        <input type="number" name="price" min="0" step="1" required value="<?php echo e(old('price', $product->price)); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Low-stock threshold
                        <input type="number" name="low_stock_threshold" min="0" required value="<?php echo e(old('low_stock_threshold', $product->low_stock_threshold)); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Location
                        <select name="stock_location" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                            <?php $__currentLoopData = ['warehouse' => 'Warehouse', 'store' => 'Store', 'used_in_pc' => 'Used in PC']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php if(old('stock_location', $product->stock_location) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Warranty (months)
                        <input type="number" name="warranty_months" min="0" max="1200" value="<?php echo e(old('warranty_months', $product->warranty_months)); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                    </label>
                    <label class="flex items-center gap-2 self-end pb-2 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="requires_serial" value="1" <?php if(old('requires_serial', $product->requires_serial)): echo 'checked'; endif; ?> class="rounded border-gray-300">
                        Track serial number per unit
                    </label>
                    <label class="text-sm font-semibold text-gray-700 md:col-span-2">Description
                        <textarea name="description" rows="3" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal"><?php echo e(old('description', $product->description)); ?></textarea>
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <?php $__currentLoopData = ['image' => ['Product photo', $product->image_path], 'before_image' => ['Before photo', $product->before_image_path], 'after_image' => ['After photo', $product->after_image_path]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => [$label, $path]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="text-sm font-semibold text-gray-700">
                            <p><?php echo e($label); ?></p>
                            <?php if($path): ?>
                                <img src="<?php echo e(asset('storage/'.$path)); ?>" alt="<?php echo e($label); ?> for <?php echo e($product->name); ?>" class="mt-2 mb-2 aspect-video w-full rounded border border-gray-200 object-cover">
                            <?php endif; ?>
                            <label class="mt-2 inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">
                                <span aria-hidden="true" class="text-lg leading-none">+</span>
                                <span>Add Photo</span>
                                <input type="file" name="<?php echo e($field); ?>" accept="image/jpeg,image/png,image/webp" class="sr-only">
                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <p class="text-sm text-gray-500">Current stock: <strong class="text-gray-700"><?php echo e($product->stock_quantity); ?></strong>. Record quantity changes from a supplier delivery on the Stock In page.</p>
                <div class="flex justify-end gap-2">
                    <a href="<?php echo e(route('inventory.index')); ?>" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
                    <button type="submit" class="rounded bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">Save changes</button>
                </div>
            </form>
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
    <!-- Order your soul. Reduce your wants. - Augustine -->
</div>
<?php /**PATH C:\Users\MYPC\Downloads\IT_12-main (2)\IT_12-main\resources\views\inventory-edit.blade.php ENDPATH**/ ?>