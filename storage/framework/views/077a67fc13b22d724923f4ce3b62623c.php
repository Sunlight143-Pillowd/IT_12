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
            <h2 class="text-xl font-semibold text-gray-800">Employee Accounts</h2>
            <div class="flex gap-2">
                <a href="<?php echo e(route('roles.index')); ?>" class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-800 hover:bg-purple-100">Manage Roles</a>
                <a href="<?php echo e(route('dashboard')); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Dashboard</a>
            </div>
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
                <h3 class="mb-4 text-lg font-bold text-gray-900">Create Employee Account</h3>
                <form method="POST" action="<?php echo e(route('employees.store')); ?>" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <?php echo csrf_field(); ?>
                    <input name="name" value="<?php echo e(old('name')); ?>" required placeholder="Employee name" class="rounded border-gray-300 text-sm">
                    <input name="email" type="email" value="<?php echo e(old('email')); ?>" required placeholder="Email" class="rounded border-gray-300 text-sm">
                    <select name="role_id" required class="rounded border-gray-300 text-sm">
                        <option value="">Assign role</option>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input name="password" type="password" required minlength="8" placeholder="Temporary password" class="rounded border-gray-300 text-sm">
                    <input name="password_confirmation" type="password" required minlength="8" placeholder="Confirm password" class="rounded border-gray-300 text-sm">
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Create account</button>
                </form>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-5">
                    <h3 class="text-lg font-bold text-gray-900">Staff Directory</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <form method="POST" action="<?php echo e(route('employees.update', $employee)); ?>" class="grid gap-3 p-5 sm:grid-cols-2 xl:grid-cols-4">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <input name="name" value="<?php echo e($employee->name); ?>" required aria-label="Employee name" class="rounded border-gray-300 text-sm">
                            <input name="email" type="email" value="<?php echo e($employee->email); ?>" required aria-label="Employee email" class="rounded border-gray-300 text-sm">
                            <select name="role_id" required aria-label="Employee role" class="rounded border-gray-300 text-sm">
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php if($employee->role_id === $role->id): echo 'selected'; endif; ?>><?php echo e($role->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" <?php if($employee->is_active): echo 'checked'; endif; ?> class="rounded border-gray-300 text-purple-700">
                                Active
                            </label>
                            <input name="password" type="password" minlength="8" placeholder="New password (optional)" class="rounded border-gray-300 text-sm">
                            <input name="password_confirmation" type="password" minlength="8" placeholder="Confirm new password" class="rounded border-gray-300 text-sm">
                            <button class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-800 hover:bg-purple-100 xl:col-span-2">Save account</button>
                        </form>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="p-5 text-sm text-gray-500">No employee accounts yet.</p>
                    <?php endif; ?>
                </div>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/employees.blade.php ENDPATH**/ ?>