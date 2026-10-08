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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight"><?php echo e(__('Inventory')); ?></h2>
            <div class="flex gap-2">
                <a href="<?php echo e(route('dashboard')); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Dashboard</a>
                <a href="<?php echo e(route('stock-in.index')); ?>" class="rounded bg-purple-600 px-3 py-2 text-sm font-semibold text-white hover:bg-purple-700">Stock In Delivery</a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <?php if(session('success')): ?>
                <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Add New Item</h3>
                    <p class="text-sm text-gray-500">Create and manage stock items below.</p>
                </div>

                <form method="POST" action="<?php echo e(route('inventory.categories.store')); ?>" class="mb-5 flex flex-col gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 md:flex-row md:items-end">
                    <?php echo csrf_field(); ?>
                    <div class="flex-1">
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Add Category</label>
                        <input type="text" name="name" placeholder="e.g. GPU, Monitor, SSD" required class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="rounded bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Add Category</button>
                </form>

                <?php if($categoryRecords->isNotEmpty()): ?>
                    <div class="mb-5 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <h4 class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Manage Categories</h4>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = $categoryRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <form method="POST" action="<?php echo e(route('inventory.categories.update', $category->id)); ?>" class="flex items-center gap-2 rounded border border-gray-300 bg-white px-2 py-1.5">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <select name="name" aria-label="Editable category name for <?php echo e($category->name); ?>" class="w-36 rounded border border-gray-300 px-2 py-1 text-sm">
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($categoryOption); ?>" <?php if($category->name === $categoryOption): echo 'selected'; endif; ?>><?php echo e($categoryOption); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <button type="submit" class="rounded bg-purple-600 px-2 py-1 text-xs font-semibold text-white hover:bg-purple-700">Save</button>
                                </form>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                    $productTypeOptions = \App\Http\Controllers\InventoryController::productTypeOptions();
                ?>

                <form method="POST" action="<?php echo e(route('inventory.store')); ?>" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-2 xl:grid-cols-7">
                    <?php echo csrf_field(); ?>
                    <div class="xl:col-span-2">
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Name</label>
                        <input type="text" name="name" required class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Type</label>
                        <select name="type" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                            <?php $__currentLoopData = $productTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Category</label>
                        <select name="category" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                            <option value="general" selected>general</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category); ?>"><?php echo e($category); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Price</label>
                        <input type="number" name="price" min="0" value="0" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Location</label>
                        <select name="stock_location" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                            <option value="warehouse">Warehouse</option>
                            <option value="store">Store</option>
                            <option value="used_in_pc">Used in PC</option>
                        </select>
                    </div>
                    <div class="xl:col-span-5">
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Low Stock Alert</label>
                        <input type="number" name="low_stock_threshold" min="0" value="5" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="xl:col-span-2">
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Description</label>
                        <input type="text" name="description" class="w-full rounded border border-gray-300 px-3 py-2 text-sm" placeholder="Optional description">
                    </div>
                    <?php $__currentLoopData = ['image' => 'Product photo', 'before_image' => 'Before photo', 'after_image' => 'After photo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['xl:col-span-2' => $field === 'image']); ?>">
                            <p class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500"><?php echo e($label); ?></p>
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">
                                <span aria-hidden="true" class="text-lg leading-none">+</span>
                                <span>Add Photo</span>
                                <input type="file" name="<?php echo e($field); ?>" accept="image/jpeg,image/png,image/webp" class="sr-only">
                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Warranty (months)</label>
                        <input type="number" name="warranty_months" min="0" max="1200" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 xl:col-span-2">
                        <input type="checkbox" name="requires_serial" value="1" class="rounded border-gray-300">
                        Track serial number for each unit
                    </label>
                    <div class="xl:col-span-7 flex justify-end">
                        <button type="submit" class="rounded bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">Add Item</button>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase tracking-[0.2em] text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Photo</th>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Price</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Stock status</th>
                                <th class="px-4 py-3">Location</th>
                                <th class="px-4 py-3">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-t border-gray-200">
                                    <td class="px-4 py-3">
                                        <?php if($product->image_path): ?>
                                            <img src="<?php echo e(asset('storage/'.$product->image_path)); ?>" alt="<?php echo e($product->name); ?>" class="h-12 w-12 rounded-lg bg-gray-100 object-cover">
                                        <?php else: ?>
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-2xl font-light text-purple-500" aria-label="No product photo available">＋</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-900"><?php echo e($product->name); ?></td>
                                    <td class="px-4 py-3 capitalize"><?php echo e($product->type); ?></td>
                                    <td class="px-4 py-3"><?php echo e($product->category); ?></td>
                                    <td class="px-4 py-3">₱<?php echo e(number_format($product->price, 2)); ?></td>
                                    <td class="px-4 py-3 <?php echo e($product->stock_quantity <= $product->low_stock_threshold ? 'font-bold text-red-600' : ''); ?>">
                                        <?php echo e($product->stock_quantity); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if($product->stock_quantity <= 0): ?>
                                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-800">OUT OF STOCK</span>
                                        <?php elseif($product->stock_quantity <= $product->low_stock_threshold): ?>
                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">LOW STOCK</span>
                                            <p class="mt-1 text-xs font-medium text-amber-700">Low-stock warning · threshold <?php echo e($product->low_stock_threshold); ?></p>
                                        <?php else: ?>
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">IN STOCK</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 capitalize"><?php echo e(str_replace('_', ' ', $product->stock_location)); ?></td>
                                    <td class="px-4 py-3">
                                        <a href="<?php echo e(route('inventory.products.edit', $product)); ?>" class="rounded border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-purple-600 hover:text-purple-700">Edit item</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
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
<?php /**PATH C:\Users\MYPC\Downloads\IT_12-main (2)\IT_12-main\resources\views/inventory.blade.php ENDPATH**/ ?>