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
            <h2 class="text-xl font-semibold text-gray-800">Business Expenses</h2>
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
                <h3 class="mb-4 text-lg font-bold text-gray-900">Record Expense</h3>
                <form method="POST" action="<?php echo e(route('expenses.store')); ?>" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <?php echo csrf_field(); ?>
                    <input name="type" required maxlength="100" placeholder="Expense type" class="rounded border-gray-300 text-sm">
                    <input name="amount" type="number" min="0.01" step="0.01" required placeholder="Amount" class="rounded border-gray-300 text-sm">
                    <input name="expense_date" type="date" required value="<?php echo e(today()->toDateString()); ?>" class="rounded border-gray-300 text-sm">
                    <input name="budget_code" maxlength="100" placeholder="Budget code" class="rounded border-gray-300 text-sm">
                    <textarea name="description" maxlength="2000" rows="2" placeholder="Description" class="rounded border-gray-300 text-sm sm:col-span-2 xl:col-span-3"></textarea>
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Save expense</button>
                </form>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-5">
                    <h3 class="text-lg font-bold text-gray-900">Recent Expenses</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="p-3">Date</th><th class="p-3">Type / Description</th><th class="p-3">Budget code</th><th class="p-3">Recorded by</th><th class="p-3 text-right">Amount</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="p-3"><?php echo e($expense->expense_date->format('M j, Y')); ?></td>
                                    <td class="p-3"><strong><?php echo e($expense->type); ?></strong><div class="text-xs text-gray-500"><?php echo e($expense->description); ?></div></td>
                                    <td class="p-3"><?php echo e($expense->budget_code ?: '—'); ?></td>
                                    <td class="p-3"><?php echo e($expense->recorder?->name ?: 'Account removed'); ?></td>
                                    <td class="p-3 text-right font-semibold">₱<?php echo e(number_format((float) $expense->amount, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" class="p-5 text-center text-gray-500">No expenses recorded.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/expenses.blade.php ENDPATH**/ ?>