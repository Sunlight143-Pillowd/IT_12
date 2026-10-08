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
            <?php echo e(__('Categories')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <?php if(session('status')): ?>
                <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->has('image')): ?>
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    <?php echo e($errors->first('image')); ?>

                </div>
            <?php endif; ?>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-purple-600 hover:shadow-md">
                        <a href="<?php echo e($category['route']); ?>" class="block">
                            <div class="relative mb-4 flex h-20 items-center justify-center overflow-hidden rounded-lg bg-gray-100 text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400">
                                <?php if($category['image_path']): ?>
                                    <img src="<?php echo e(asset('storage/'.$category['image_path'])); ?>" alt="<?php echo e($category['label']); ?>" class="h-full w-full object-contain">
                                <?php else: ?>
                                    [ Category ]
                                <?php endif; ?>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900"><?php echo e($category['label']); ?></h3>
                            <p class="mt-2 text-sm text-gray-500">Explore available products in this category.</p>
                        </a>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH C:\Users\MYPC\Downloads\IT_12-main (2)\IT_12-main\resources\views/store/categories.blade.php ENDPATH**/ ?>