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
        $productTypeOptions = \App\Http\Controllers\InventoryController::productTypeOptions();
        $lowStockCount = $products->filter(fn ($product) => $product->stock_quantity <= $product->low_stock_threshold)->count();
        $outOfStockCount = $products->filter(fn ($product) => $product->stock_quantity <= 0)->count();
        $unitsOnHand = $products->sum('stock_quantity');
    ?>

     <?php $__env->slot('header', null, []); ?> 
        <div class="mx-auto flex max-w-screen-2xl flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-100 text-purple-700" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.4 7.7 7.6 4.4 7.6-4.4M12 12v8.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-purple-700">Back office</p>
                    <h1 class="text-xl font-bold tracking-tight text-gray-950"><?php echo e(__('Inventory')); ?></h1>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M11.78 3.22a.75.75 0 0 1 0 1.06L7.06 9h9.19a.75.75 0 0 1 0 1.5H7.06l4.72 4.72a.75.75 0 1 1-1.06 1.06l-6-6a.75.75 0 0 1 0-1.06l6-6a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                    Dashboard
                </a>
                <a href="<?php echo e(route('stock-in.index')); ?>" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-purple-700 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 3.5a.75.75 0 0 0-1.5 0v5.75H3.5a.75.75 0 0 0 0 1.5h5.75v5.75a.75.75 0 0 0 1.5 0v-5.75h5.75a.75.75 0 0 0 0-1.5h-5.75V3.5Z"/></svg>
                    Record stock-in
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="min-h-full bg-gray-50 py-6 sm:py-8">
        <div class="mx-auto max-w-screen-2xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div role="status" class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-sm">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.78-9.22a.75.75 0 0 0-1.06-1.06L9 11.44 7.28 9.72a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.06 0l4.25-4.25Z" clip-rule="evenodd"/></svg>
                    <p><?php echo e(session('success')); ?></p>
                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
                    <p class="font-semibold">Please review the following:</p>
                    <ul class="mt-1 list-inside list-disc">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <section aria-label="Inventory overview" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Products listed</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-gray-950"><?php echo e(number_format($products->count())); ?></p>
                    <p class="mt-1 text-xs text-gray-500">Items matching the current filters</p>
                </article>
                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Units on hand</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-gray-950"><?php echo e(number_format($unitsOnHand)); ?></p>
                    <p class="mt-1 text-xs text-gray-500">Across listed products</p>
                </article>
                <article class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm sm:p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-800">Low stock</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-amber-950"><?php echo e(number_format($lowStockCount)); ?></p>
                    <p class="mt-1 text-xs text-amber-800">At or below reorder threshold</p>
                </article>
                <article class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm sm:p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-red-800">Out of stock</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-red-950"><?php echo e(number_format($outOfStockCount)); ?></p>
                    <p class="mt-1 text-xs text-red-800">No units currently available</p>
                </article>
            </section>

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-5 sm:px-6">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-purple-50 text-purple-700" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4.75A1.75 1.75 0 0 1 4.75 3h10.5A1.75 1.75 0 0 1 17 4.75v10.5A1.75 1.75 0 0 1 15.25 17H4.75A1.75 1.75 0 0 1 3 15.25V4.75ZM5.5 6a.75.75 0 0 0 0 1.5h9a.75.75 0 0 0 0-1.5h-9Zm0 3.25a.75.75 0 0 0 0 1.5h5a.75.75 0 0 0 0-1.5h-5Z"/></svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-gray-950">Product catalog</h2>
                            <p class="text-sm text-gray-500">Create products, set prices, and configure stock alerts.</p>
                        </div>
                    </div>
                </div>

                <details class="group border-b border-gray-200" <?php if($errors->any()): ?> open <?php endif; ?>>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-purple-500 sm:px-6">
                        <span class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 text-lg font-medium leading-none text-purple-800" aria-hidden="true">+</span>
                            <span>
                                <span class="block text-sm font-bold text-gray-900">Add a product</span>
                                <span class="mt-0.5 block text-xs text-gray-500">New items start with zero stock. Record quantities through Stock In.</span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 shrink-0 text-gray-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                    </summary>
                    <div class="border-t border-gray-100 bg-gray-50/70 px-5 py-5 sm:px-6">
                        <form method="POST" action="<?php echo e(route('inventory.store')); ?>" enctype="multipart/form-data" class="space-y-6">
                            <?php echo csrf_field(); ?>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Product details</h3>
                                <p class="mt-1 text-xs text-gray-500">SKU is generated automatically if you leave it blank.</p>
                                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    <label class="text-xs font-semibold text-gray-700 lg:col-span-2">Product name
                                        <input type="text" name="name" value="<?php echo e(old('name')); ?>" required maxlength="150" class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    </label>
                                    <label class="text-xs font-semibold text-gray-700">SKU / barcode
                                        <input type="text" name="sku" value="<?php echo e(old('sku')); ?>" maxlength="100" placeholder="Auto-generated" class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    </label>
                                    <label class="text-xs font-semibold text-gray-700">Type
                                        <select name="type" required class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                            <?php $__currentLoopData = $productTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>" <?php if(old('type', 'accessory') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </label>
                                    <label class="text-xs font-semibold text-gray-700">Category
                                        <select name="category" required class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                            <option value="general" <?php if(old('category', 'general') === 'general'): echo 'selected'; endif; ?>>General</option>
                                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($category); ?>" <?php if(old('category') === $category): echo 'selected'; endif; ?>><?php echo e($category); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </label>
                                    <label class="text-xs font-semibold text-gray-700">Selling price
                                        <span class="relative mt-1.5 block">
                                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-gray-500">₱</span>
                                            <input type="number" name="price" min="0" step="1" value="<?php echo e(old('price', 0)); ?>" required class="block w-full rounded-lg border-gray-300 bg-white pl-8 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        </span>
                                    </label>
                                    <label class="text-xs font-semibold text-gray-700">Stock location
                                        <select name="stock_location" required class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                            <?php $__currentLoopData = ['warehouse' => 'Warehouse', 'store' => 'Store', 'used_in_pc' => 'Used in PC']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>" <?php if(old('stock_location', 'warehouse') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </label>
                                    <label class="text-xs font-semibold text-gray-700">Low-stock alert level
                                        <input type="number" name="low_stock_threshold" min="0" value="<?php echo e(old('low_stock_threshold', 5)); ?>" class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    </label>
                                    <label class="text-xs font-semibold text-gray-700">Warranty (months)
                                        <input type="number" name="warranty_months" min="0" max="1200" value="<?php echo e(old('warranty_months')); ?>" class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    </label>
                                    <label class="text-xs font-semibold text-gray-700 sm:col-span-2 lg:col-span-4">Description
                                        <textarea name="description" rows="2" maxlength="255" placeholder="Optional product details" class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500"><?php echo e(old('description')); ?></textarea>
                                    </label>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-5">
                                <h3 class="text-sm font-bold text-gray-900">Product photos</h3>
                                <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                    <?php $__currentLoopData = ['image' => 'Main product photo', 'before_image' => 'Before photo', 'after_image' => 'After photo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-gray-300 bg-white p-3 transition hover:border-purple-400 hover:bg-purple-50/40">
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500" aria-hidden="true">
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 5.75A2.75 2.75 0 0 1 3.75 3h12.5A2.75 2.75 0 0 1 19 5.75v8.5A2.75 2.75 0 0 1 16.25 17H3.75A2.75 2.75 0 0 1 1 14.25v-8.5ZM4.5 6a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm12.28 8.06-3.2-4.27a.75.75 0 0 0-1.2 0l-1.64 2.18-1.2-1.2a.75.75 0 0 0-1.06 0l-3.3 3.3h10.07c.63 0 1.17-.39 1.4-.94l.13-.07Z" clip-rule="evenodd"/></svg>
                                            </span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block text-xs font-semibold text-gray-800"><?php echo e($label); ?></span>
                                                <span data-file-name class="mt-0.5 block truncate text-xs text-gray-500">Choose an image</span>
                                            </span>
                                            <input type="file" name="<?php echo e($field); ?>" accept="image/jpeg,image/png,image/webp" class="sr-only" data-file-input>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            <div class="flex flex-col gap-4 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                                <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700">
                                    <input type="checkbox" name="requires_serial" value="1" <?php if(old('requires_serial')): echo 'checked'; endif; ?> class="h-4 w-4 rounded border-gray-300 text-purple-700 focus:ring-purple-500">
                                    <span>
                                        <span class="block font-semibold">Track serial numbers</span>
                                        <span class="block text-xs text-gray-500">Require a serial number for each unit.</span>
                                    </span>
                                </label>
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-purple-700 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10.75 3.5a.75.75 0 0 0-1.5 0v5.75H3.5a.75.75 0 0 0 0 1.5h5.75v5.75a.75.75 0 0 0 1.5 0v-5.75h5.75a.75.75 0 0 0 0-1.5h-5.75V3.5Z" clip-rule="evenodd"/></svg>
                                    Add product
                                </button>
                            </div>
                        </form>
                    </div>
                </details>

                <div class="bg-white px-5 py-5 sm:px-6">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Categories</h3>
                            <p class="mt-1 text-xs text-gray-500">Rename, add, or reorder the categories used in your catalog.</p>
                        </div>
                        <span class="w-fit rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold text-purple-800"><?php echo e($categoryRecords->count()); ?> categories</span>
                    </div>

                    <form method="POST" action="<?php echo e(route('inventory.categories.store')); ?>" class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-end">
                        <?php echo csrf_field(); ?>
                        <label for="new-category-name" class="min-w-0 flex-1 text-xs font-semibold text-gray-700">Add category
                            <input id="new-category-name" type="text" name="name" value="<?php echo e(old('name')); ?>" placeholder="For example: Graphics cards" required class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </label>
                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-purple-200 bg-purple-50 px-4 text-sm font-semibold text-purple-800 transition hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">Add category</button>
                    </form>

                    <?php if($categoryRecords->isNotEmpty()): ?>
                        <div class="mt-4 border-t border-gray-100 pt-4">
                            <form id="category-order-form" method="POST" action="<?php echo e(route('inventory.categories.reorder')); ?>" class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <?php echo csrf_field(); ?>
                                <div data-category-order-inputs></div>
                                <p class="text-xs text-gray-500">Drag a category card to change the display order.</p>
                                <button type="submit" class="min-h-9 rounded-lg border border-gray-300 bg-white px-3 text-xs font-semibold text-gray-700 transition hover:bg-gray-50">Save category order</button>
                            </form>
                            <div data-category-list class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                                <?php $__currentLoopData = $categoryRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div draggable="true" data-category-row data-category-id="<?php echo e($category->id); ?>" class="flex min-w-0 items-center gap-2 rounded-xl border border-gray-200 bg-gray-50/70 p-2 transition duration-150 motion-reduce:transition-none">
                                        <span aria-hidden="true" class="cursor-grab select-none rounded-md px-1.5 py-2 text-gray-400 active:cursor-grabbing">⠿</span>
                                        <form method="POST" action="<?php echo e(route('inventory.categories.update', $category->id)); ?>" class="flex min-w-0 flex-1 items-center gap-2">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <input type="text" name="name" value="<?php echo e($category->name); ?>" aria-label="Editable category name for <?php echo e($category->name); ?>" required class="min-w-0 flex-1 rounded-lg border-gray-300 bg-white px-2.5 py-2 text-sm focus:border-purple-500 focus:ring-purple-500">
                                            <button type="submit" class="rounded-lg px-2.5 py-2 text-xs font-bold text-purple-800 transition hover:bg-purple-100" aria-label="Save <?php echo e($category->name); ?> category">Save</button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('inventory.categories.destroy', $category->id)); ?>" onsubmit="return confirm(<?php echo \Illuminate\Support\Js::from('Delete the '.$category->name.' category?')->toHtml() ?>)">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500" aria-label="Delete <?php echo e($category->name); ?> category">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.5 2.75A1.75 1.75 0 0 0 6.75 4.5v.25h-2a.75.75 0 0 0 0 1.5h.3l.68 9.03A2.25 2.25 0 0 0 7.97 17.4h4.06a2.25 2.25 0 0 0 2.24-2.12l.68-9.03h.3a.75.75 0 0 0 0-1.5h-2V4.5a1.75 1.75 0 0 0-1.75-1.75h-3Zm3.25 2h-3.5V4.5a.25.25 0 0 1 .25-.25h3a.25.25 0 0 1 .25.25v.25Z" clip-rule="evenodd"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="mt-4 rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-500">No categories yet. Add a category to organize products.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-5 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-950">Stock overview</h2>
                            <p class="mt-1 text-sm text-gray-500">Search the catalog and monitor on-hand quantities and reorder alerts.</p>
                        </div>
                        <span class="w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600"><?php echo e($products->count()); ?> <?php echo e(\Illuminate\Support\Str::plural('product', $products->count())); ?></span>
                    </div>

                    <form method="GET" action="<?php echo e(route('inventory.index')); ?>" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(16rem,1.5fr)_minmax(10rem,1fr)_minmax(12rem,1fr)_auto]">
                        <label class="text-xs font-semibold text-gray-700">
                            Search products
                            <span class="relative mt-1.5 block">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 1 0 3.45 9.78l3.64 3.64a.75.75 0 1 0 1.06-1.06l-3.64-3.64A5.5 5.5 0 0 0 8.5 3ZM4.5 8.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
                                <input type="search" name="search" value="<?php echo e($search); ?>" placeholder="Name, SKU, type, category" class="block w-full rounded-lg border-gray-300 pl-9 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </span>
                        </label>
                        <label class="text-xs font-semibold text-gray-700">Category
                            <select name="category" class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="all">All categories</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category); ?>" <?php if($selectedCategory === $category): echo 'selected'; endif; ?>><?php echo e($category); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                        <label class="text-xs font-semibold text-gray-700">Sort by stock
                            <select name="stock_sort" class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="name" <?php if($stockSort === 'name'): echo 'selected'; endif; ?>>Default order</option>
                                <option value="ascending" <?php if($stockSort === 'ascending'): echo 'selected'; endif; ?>>Lowest stock first</option>
                                <option value="descending" <?php if($stockSort === 'descending'): echo 'selected'; endif; ?>>Highest stock first</option>
                            </select>
                            <span class="mt-1 block text-[11px] font-normal text-gray-500">Low-stock products are prioritized.</span>
                        </label>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-lg bg-gray-900 px-4 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 xl:flex-none">Apply filters</button>
                            <a href="<?php echo e(route('inventory.index')); ?>" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">Clear</a>
                        </div>
                    </form>
                </div>

                <?php if($products->isEmpty()): ?>
                    <div class="px-6 py-16 text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 4.75A1.75 1.75 0 0 1 4.75 3h10.5A1.75 1.75 0 0 1 17 4.75v10.5A1.75 1.75 0 0 1 15.25 17H4.75A1.75 1.75 0 0 1 3 15.25V4.75Z"/></svg>
                        </span>
                        <h3 class="mt-3 text-sm font-bold text-gray-900">No products found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try another search or clear the selected filters.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-[1050px] w-full text-left text-sm text-gray-700">
                            <thead class="bg-gray-50 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th scope="col" class="px-5 py-3">Product</th>
                                    <th scope="col" class="px-4 py-3">SKU / barcode</th>
                                    <th scope="col" class="px-4 py-3">Category</th>
                                    <th scope="col" class="px-4 py-3 text-right">Price</th>
                                    <th scope="col" class="px-4 py-3 text-center">On hand</th>
                                    <th scope="col" class="px-4 py-3">Stock status</th>
                                    <th scope="col" class="px-4 py-3">Location</th>
                                    <th scope="col" class="px-5 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $lastStockIn = $product->stockInItems
                                            ->filter(fn ($item) => $item->stockIn)
                                            ->sortByDesc(fn ($item) => $item->stockIn->received_at)
                                            ->first()?->stockIn?->received_at;
                                    ?>
                                    <tr class="transition hover:bg-gray-50/80">
                                        <td class="px-5 py-3.5">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <?php if($product->image_path): ?>
                                                    <img src="<?php echo e(asset('storage/'.$product->image_path)); ?>" alt="" class="h-11 w-11 shrink-0 rounded-lg border border-gray-200 bg-gray-100 object-cover">
                                                <?php else: ?>
                                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400" aria-hidden="true">
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 5.75A2.75 2.75 0 0 1 3.75 3h12.5A2.75 2.75 0 0 1 19 5.75v8.5A2.75 2.75 0 0 1 16.25 17H3.75A2.75 2.75 0 0 1 1 14.25v-8.5Zm3.5.25a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z" clip-rule="evenodd"/></svg>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold text-gray-950"><?php echo e($product->name); ?></p>
                                                    <p class="mt-0.5 text-xs capitalize text-gray-500"><?php echo e(str_replace('_', ' ', $product->type)); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5"><span class="rounded bg-gray-100 px-2 py-1 font-mono text-xs text-gray-700"><?php echo e($product->sku); ?></span></td>
                                        <td class="px-4 py-3.5"><?php echo e($product->category); ?></td>
                                        <td class="px-4 py-3.5 text-right font-semibold tabular-nums text-gray-900">₱<?php echo e(number_format($product->price, 2)); ?></td>
                                        <td class="px-4 py-3.5 text-center">
                                            <span class="<?php echo e($product->stock_quantity <= $product->low_stock_threshold ? 'font-bold text-red-700' : 'font-semibold text-gray-900'); ?> tabular-nums"><?php echo e(number_format($product->stock_quantity)); ?></span>
                                            <span class="block text-[11px] text-gray-400">alert at <?php echo e($product->low_stock_threshold); ?></span>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <?php if($product->stock_quantity <= 0): ?>
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-bold tracking-wide text-red-700 ring-1 ring-inset ring-red-200"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>OUT OF STOCK</span>
                                                <p class="mt-1.5 text-[11px] text-gray-500">Last reorder: <?php echo e($lastStockIn?->format('M j, Y') ?? 'Never recorded'); ?></p>
                                            <?php elseif($product->stock_quantity <= $product->low_stock_threshold): ?>
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold tracking-wide text-amber-800 ring-1 ring-inset ring-amber-200"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>LOW STOCK</span>
                                                <p class="mt-1.5 text-[11px] text-gray-500">Last reorder: <?php echo e($lastStockIn?->format('M j, Y') ?? 'Never recorded'); ?></p>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold tracking-wide text-emerald-800 ring-1 ring-inset ring-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>IN STOCK</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3.5 capitalize text-gray-600"><?php echo e(str_replace('_', ' ', $product->stock_location)); ?></td>
                                        <td class="px-5 py-3.5 text-right">
                                            <a href="<?php echo e(route('inventory.products.edit', $product)); ?>" class="inline-flex min-h-9 items-center rounded-lg border border-gray-300 bg-white px-3 text-xs font-semibold text-gray-700 transition hover:border-purple-300 hover:bg-purple-50 hover:text-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">Edit item</a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-100 bg-gray-50/70 px-5 py-3 text-xs text-gray-500">
                    <span>Stock levels update when a stock-in delivery or sale is recorded.</span>
                    <span>All prices in Philippine pesos (₱).</span>
                </div>
            </section>
        </div>
    </div>

    <script>
        (() => {
            const categoryList = document.querySelector('[data-category-list]');
            const orderForm = document.querySelector('#category-order-form');

            document.querySelectorAll('[data-file-input]').forEach((input) => {
                input.addEventListener('change', () => {
                    const fileName = input.closest('label')?.querySelector('[data-file-name]');
                    if (fileName) {
                        fileName.textContent = input.files?.[0]?.name ?? 'Choose an image';
                    }
                });
            });

            if (!categoryList || !orderForm) {
                return;
            }

            let draggedCategory = null;

            categoryList.addEventListener('dragstart', (event) => {
                const row = event.target.closest('[data-category-row]');
                if (!row) {
                    return;
                }

                draggedCategory = row;
                row.classList.add('opacity-50', 'border-purple-500');
                event.dataTransfer.effectAllowed = 'move';
            });

            categoryList.addEventListener('dragover', (event) => {
                if (!draggedCategory) {
                    return;
                }

                event.preventDefault();
                const rows = [...categoryList.querySelectorAll('[data-category-row]:not(.opacity-50)')];
                const nextRow = rows.find((row) => event.clientY <= row.getBoundingClientRect().top + row.offsetHeight / 2);
                categoryList.insertBefore(draggedCategory, nextRow ?? null);
            });

            categoryList.addEventListener('dragend', () => {
                draggedCategory?.classList.remove('opacity-50', 'border-purple-500');
                draggedCategory = null;
            });

            orderForm.addEventListener('submit', () => {
                const inputs = orderForm.querySelector('[data-category-order-inputs]');
                inputs.replaceChildren();

                categoryList.querySelectorAll('[data-category-row]').forEach((row) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'category_ids[]';
                    input.value = row.dataset.categoryId;
                    inputs.append(input);
                });
            });
        })();
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/inventory.blade.php ENDPATH**/ ?>