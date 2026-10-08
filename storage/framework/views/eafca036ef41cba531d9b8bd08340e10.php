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
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex h-52 items-center justify-center bg-gray-100 text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">
                            [ Image Placeholder ]
                        </div>
                        <div class="p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-500"><?php echo e(ucfirst($product->category)); ?></p>
                            <h3 class="mt-2 text-xl font-bold text-gray-900"><?php echo e($product->name); ?></h3>
                            <p class="mt-2 text-sm text-gray-600"><?php echo e($product->description ?? 'High-performance product for your setup.'); ?></p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-lg font-black text-purple-600">₱<?php echo e(number_format($product->price, 0)); ?></span>
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700"><?php echo e($product->stock_quantity); ?> in stock</span>
                            </div>
                            <?php if($product->stock_quantity > 0): ?>
                                <form method="POST" action="<?php echo e(route('cart.items.store', $product)); ?>" class="mt-4 flex items-end gap-3">
                                    <?php echo csrf_field(); ?>
                                    <label class="text-xs font-medium text-gray-700">
                                        Quantity
                                        <input type="number" name="quantity" value="<?php echo e(old('quantity', 1)); ?>" min="1" max="<?php echo e($product->stock_quantity); ?>" required
                                               class="mt-1 block w-20 rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    </label>
                                    <button type="submit" class="rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">
                                        Add to cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <button type="button" disabled class="mt-4 cursor-not-allowed rounded-md bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-600">
                                    Out of stock
                                </button>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/store/catalog.blade.php ENDPATH**/ ?>