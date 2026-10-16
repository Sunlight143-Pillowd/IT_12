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
            <h2 class="text-xl font-semibold text-gray-800">Roles & Permissions</h2>
            <a href="<?php echo e(route('employees.index')); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Employees</a>
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
                <h3 class="mb-4 text-lg font-bold text-gray-900">Create Role</h3>
                <form method="POST" action="<?php echo e(route('roles.store')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input name="name" required maxlength="100" placeholder="Role name" class="w-full rounded border-gray-300 text-sm sm:max-w-md">
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        <?php $__currentLoopData = $permissionOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="permissions[]" value="<?php echo e($permission); ?>" class="rounded border-gray-300 text-purple-700">
                                <?php echo e($label); ?>

                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Create role</button>
                </form>
            </section>

            <section class="grid gap-4 lg:grid-cols-2">
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <form method="POST" action="<?php echo e(route('roles.update', $role)); ?>" class="space-y-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="font-bold text-gray-900"><?php echo e($role->name); ?></h3>
                                <p class="text-xs text-gray-500"><?php echo e($role->users_count); ?> assigned account(s) · <?php echo e($role->is_system ? 'System role' : 'Custom role'); ?></p>
                            </div>
                            <input name="name" value="<?php echo e($role->name); ?>" required maxlength="100" aria-label="Role name" class="w-40 rounded border-gray-300 text-sm">
                        </div>
                        <div class="grid gap-2 sm:grid-cols-2">
                            <?php $__currentLoopData = $permissionOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-start gap-2 text-xs text-gray-700">
                                    <input type="checkbox" name="permissions[]" value="<?php echo e($permission); ?>" <?php if(in_array($permission, $role->permissions ?? [], true)): echo 'checked'; endif; ?> class="mt-0.5 rounded border-gray-300 text-purple-700">
                                    <?php echo e($label); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <button class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-800 hover:bg-purple-100">Save permissions</button>
                    </form>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views\roles.blade.php ENDPATH**/ ?>