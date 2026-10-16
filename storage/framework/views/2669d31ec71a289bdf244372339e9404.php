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
            <h2 class="text-xl font-semibold text-gray-800">Customers, Credit & Feedback</h2>
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
                <h3 class="mb-4 text-lg font-bold text-gray-900">Register Customer</h3>
                <form method="POST" action="<?php echo e(route('customers.store')); ?>" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <?php echo csrf_field(); ?>
                    <input name="name" value="<?php echo e(old('name')); ?>" required maxlength="150" placeholder="Customer name" class="rounded border-gray-300 text-sm">
                    <input name="email" type="email" value="<?php echo e(old('email')); ?>" placeholder="Email" class="rounded border-gray-300 text-sm">
                    <input name="phone" value="<?php echo e(old('phone')); ?>" placeholder="Phone" class="rounded border-gray-300 text-sm">
                    <select name="loyalty_tier" class="rounded border-gray-300 text-sm">
                        <?php $__currentLoopData = ['Regular', 'Silver', 'Gold', 'Platinum']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tier); ?>"><?php echo e($tier); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input name="address" value="<?php echo e(old('address')); ?>" placeholder="Address" class="rounded border-gray-300 text-sm sm:col-span-2">
                    <input name="registered_at" type="date" value="<?php echo e(old('registered_at', today()->toDateString())); ?>" class="rounded border-gray-300 text-sm">
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Add customer</button>
                    <textarea name="notes" rows="2" placeholder="Notes" class="rounded border-gray-300 text-sm sm:col-span-2 xl:col-span-4"></textarea>
                </form>
            </section>

            <section class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900">Customer Records (latest 100)</h3>
                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="space-y-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="grid gap-4 xl:grid-cols-2">
                            <form method="POST" action="<?php echo e(route('customers.update', $customer)); ?>" class="grid gap-2 sm:grid-cols-2">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <input name="name" value="<?php echo e($customer->name); ?>" required aria-label="Customer name" class="rounded border-gray-300 text-sm">
                                <input name="email" type="email" value="<?php echo e($customer->email); ?>" aria-label="Customer email" class="rounded border-gray-300 text-sm">
                                <input name="phone" value="<?php echo e($customer->phone); ?>" aria-label="Customer phone" class="rounded border-gray-300 text-sm">
                                <select name="loyalty_tier" class="rounded border-gray-300 text-sm">
                                    <?php $__currentLoopData = ['Regular', 'Silver', 'Gold', 'Platinum']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($tier); ?>" <?php if($customer->loyalty_tier === $tier): echo 'selected'; endif; ?>><?php echo e($tier); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <input name="address" value="<?php echo e($customer->address); ?>" aria-label="Customer address" class="rounded border-gray-300 text-sm sm:col-span-2">
                                <input name="registered_at" type="date" value="<?php echo e($customer->registered_at?->toDateString()); ?>" aria-label="Registration date" class="rounded border-gray-300 text-sm">
                                <button class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-800 hover:bg-purple-100">Save details</button>
                                <textarea name="notes" rows="2" aria-label="Customer notes" class="rounded border-gray-300 text-sm sm:col-span-2"><?php echo e($customer->notes); ?></textarea>
                            </form>

                            <div class="space-y-3 rounded-lg bg-gray-50 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h4 class="font-bold text-gray-900">Credit balance</h4>
                                    <p class="text-sm font-bold text-purple-800">₱<?php echo e(number_format((float) $customer->credits->sum('balance_due'), 2)); ?></p>
                                </div>
                                <form method="POST" action="<?php echo e(route('customers.credits.store', $customer)); ?>" class="grid gap-2 sm:grid-cols-2">
                                    <?php echo csrf_field(); ?>
                                    <input name="credit_date" type="date" required value="<?php echo e(today()->toDateString()); ?>" class="rounded border-gray-300 text-sm">
                                    <input name="amount_due" type="number" min="0.01" step="0.01" required placeholder="Amount due" class="rounded border-gray-300 text-sm">
                                    <input name="description" maxlength="255" placeholder="Credit purchase details" class="rounded border-gray-300 text-sm sm:col-span-2">
                                    <button class="rounded bg-purple-700 px-3 py-2 text-sm font-semibold text-white hover:bg-purple-800 sm:col-span-2">Record credit purchase</button>
                                </form>
                                <?php $__currentLoopData = $customer->credits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $credit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="border-t border-gray-200 pt-3 text-sm">
                                        <div class="flex flex-wrap justify-between gap-2">
                                            <span><?php echo e($credit->credit_date->format('M j, Y')); ?> · <?php echo e($credit->description ?: 'Credit purchase'); ?></span>
                                            <span class="font-semibold">Due ₱<?php echo e(number_format((float) $credit->balance_due, 2)); ?> / ₱<?php echo e(number_format((float) $credit->amount_due, 2)); ?></span>
                                        </div>
                                        <?php if((float) $credit->balance_due > 0): ?>
                                            <form method="POST" action="<?php echo e(route('credits.payments.store', $credit)); ?>" class="mt-2 flex flex-wrap gap-2">
                                                <?php echo csrf_field(); ?>
                                                <input name="amount" type="number" min="0.01" max="<?php echo e($credit->balance_due); ?>" step="0.01" required placeholder="Payment amount" class="w-44 rounded border-gray-300 text-sm">
                                                <button class="rounded border border-green-200 bg-green-50 px-3 py-2 text-xs font-bold text-green-800 hover:bg-green-100">Record payment</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php $__currentLoopData = $credit->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p class="mt-1 text-xs text-gray-500">Payment ₱<?php echo e(number_format((float) $payment->amount, 2)); ?> · <?php echo e($payment->paid_at->format('M j, Y g:i A')); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <h4 class="mb-3 font-bold text-gray-900">Record Product Feedback</h4>
                            <form method="POST" action="<?php echo e(route('customers.feedback.store', $customer)); ?>" class="grid gap-2 sm:grid-cols-4">
                                <?php echo csrf_field(); ?>
                                <select name="product_id" required class="rounded border-gray-300 text-sm sm:col-span-2">
                                    <option value="">Select product</option>
                                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($product->id); ?>"><?php echo e($product->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <select name="rating" required class="rounded border-gray-300 text-sm">
                                    <option value="">Rating</option>
                                    <?php $__currentLoopData = range(1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($rating); ?>"><?php echo e($rating); ?> / 5</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <button class="rounded bg-purple-700 px-3 py-2 text-sm font-semibold text-white hover:bg-purple-800">Save feedback</button>
                                <textarea name="comment" maxlength="2000" rows="2" placeholder="Customer comment" class="rounded border-gray-300 text-sm sm:col-span-4"></textarea>
                            </form>
                            <?php $__currentLoopData = $customer->feedback; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedback): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="mt-2 text-sm text-gray-600"><?php echo e($feedback->product->name); ?> · <?php echo e($feedback->rating); ?>/5 — <?php echo e($feedback->comment); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="rounded-lg bg-white p-5 text-sm text-gray-500">No customer records yet.</p>
                <?php endif; ?>
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views\customers.blade.php ENDPATH**/ ?>