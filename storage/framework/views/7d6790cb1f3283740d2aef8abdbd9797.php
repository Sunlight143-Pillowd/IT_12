<?php
    $currentFilter = $filter ?? 'all';
?>

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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e($title); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="mb-6 text-sm text-gray-500"><?php echo e($description); ?></p>

            <?php if(session('status')): ?>
                <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->has('quantity')): ?>
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    <?php echo e($errors->first('quantity')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->has('image')): ?>
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    <?php echo e($errors->first('image')); ?>

                </div>
            <?php endif; ?>

            <div class="mb-8 flex flex-wrap gap-2">
                <?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $routeName = match ($type) {
                            'desktop' => 'store.desktops',
                            'laptop' => 'store.laptops',
                            'accessory' => 'store.accessories',
                            default => 'store.special-offers',
                        };
                    ?>

                    <a href="<?php echo e(route($routeName, $value === 'all' ? [] : ['filter' => $value])); ?>"
                       class="rounded-full border px-3 py-1.5 text-xs font-semibold <?php echo e($currentFilter === $value ? 'border-purple-600 bg-purple-600 text-white' : 'border-gray-300 text-gray-700 hover:border-purple-400'); ?>">
                        <?php echo e($label); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php ($availableStock = $product->availableStock()); ?>
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div data-photo-card class="relative h-52 bg-gray-100">
                            <?php if($product->image_path): ?>
                                <img src="<?php echo e(asset('storage/'.$product->image_path)); ?>" alt="<?php echo e($product->name); ?>" class="h-full w-full object-contain">
                            <?php else: ?>
                                <img data-product-photo-preview alt="<?php echo e($product->name); ?>" class="hidden h-full w-full object-contain">
                                <div data-product-photo-placeholder class="flex h-full items-center justify-center text-sm text-gray-500" aria-label="No product photo available">
                                    Photo unavailable
                                </div>
                                <?php if(Auth::user()?->isAdmin()): ?>
                                    <form method="POST" action="<?php echo e(route('inventory.products.image', $product)); ?>" enctype="multipart/form-data" class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-3">
                                        <?php echo csrf_field(); ?>
                                        <label class="inline-flex cursor-pointer items-center gap-2 rounded bg-white px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-gray-800 shadow transition hover:bg-purple-100 hover:text-purple-800">
                                            <span>Upload Photo</span>
                                            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/bmp,image/webp" required class="sr-only" onchange="previewProductPhoto(this)">
                                        </label>
                                        <button type="submit" data-product-photo-submit class="hidden rounded bg-purple-700 px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-white shadow transition hover:bg-purple-800">Save Photo</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h3 class="text-xl font-bold text-gray-900"><?php echo e($product->name); ?></h3>
                            <?php if(! empty(trim((string) $product->description))): ?>
                                <p class="mt-2 text-sm leading-6 text-gray-600"><?php echo e($product->description); ?></p>
                            <?php endif; ?>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-lg font-black text-purple-600">₱<?php echo e(number_format($product->price, 0)); ?></span>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($availableStock <= $product->low_stock_threshold ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'); ?>"><?php echo e($availableStock); ?> in stock</span>
                            </div>
                            <?php if (isset($component)) { $__componentOriginal7eb846a9d6661322cec973b548c4c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb846a9d6661322cec973b548c4c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.store.cart-controls','data' => ['product' => $product,'availableStock' => $availableStock,'showQuantityControls' => request()->routeIs('store.laptops', 'store.top-selling')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('store.cart-controls'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'available-stock' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableStock),'show-quantity-controls' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('store.laptops', 'store.top-selling'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb846a9d6661322cec973b548c4c18b)): ?>
<?php $attributes = $__attributesOriginal7eb846a9d6661322cec973b548c4c18b; ?>
<?php unset($__attributesOriginal7eb846a9d6661322cec973b548c4c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb846a9d6661322cec973b548c4c18b)): ?>
<?php $component = $__componentOriginal7eb846a9d6661322cec973b548c4c18b; ?>
<?php unset($__componentOriginal7eb846a9d6661322cec973b548c4c18b); ?>
<?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full rounded border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center text-gray-500">
                        No stock available.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<script>
    function previewProductPhoto(input) {
        const file = input.files?.[0];
        const card = input.closest('[data-photo-card]');
        const image = card?.querySelector('[data-product-photo-preview]');
        const placeholder = card?.querySelector('[data-product-photo-placeholder]');
        const submitButton = card?.querySelector('[data-product-photo-submit]');

        if (!file || !image || !placeholder || !submitButton) {
            return;
        }

        image.src = URL.createObjectURL(file);
        image.classList.remove('hidden');
        placeholder.classList.add('hidden');
        submitButton.classList.remove('hidden');
    }
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
<?php /**PATH C:\Users\MYPC\Downloads\IT_12-main (2)\IT_12-main\resources\views/store/catalog.blade.php ENDPATH**/ ?>