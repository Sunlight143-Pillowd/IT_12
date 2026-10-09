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
        /** @var \Illuminate\Support\ViewErrorBag $errors */
    ?>

     <?php $__env->slot('header', null, []); ?> 
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-600">Your selections</p>
            <h2 class="mt-1 text-2xl font-black text-gray-900"><?php echo e(__('Your Cart')); ?></h2>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-4">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if(session('status')): ?>
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>

            <?php if(count($items) === 0): ?>
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
                    <h3 class="text-xl font-bold text-gray-900">Your cart is empty</h3>
                    <p class="mt-2 text-sm text-gray-500">Browse the shop and add products to get started.</p>
                    <a href="<?php echo e(route('store.accessories')); ?>" class="mt-5 inline-flex rounded-lg bg-purple-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-purple-800">
                        Browse products
                    </a>
                </div>
            <?php else: ?>
                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
                    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4">
                            <h3 class="font-bold text-gray-900">Order items</h3>
                        </div>
                        <div class="divide-y divide-gray-100 px-5">
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="flex flex-col gap-4 py-5 sm:flex-row sm:items-center">
                                    <?php if($item['product']->image_path): ?>
                                        <img src="<?php echo e(asset('storage/'.$item['product']->image_path)); ?>" alt="<?php echo e($item['product']->name); ?>" class="h-24 w-24 shrink-0 rounded-xl bg-gray-100 object-cover">
                                    <?php else: ?>
                                        <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-3xl font-light text-purple-500" aria-label="No product photo available">＋</div>
                                    <?php endif; ?>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-gray-900"><?php echo e($item['product']->name); ?></h4>
                                        <p class="mt-1 text-sm text-gray-500">₱<?php echo e(number_format($item['product']->price, 2)); ?> each</p>
                                        <p class="mt-1 text-xs text-gray-500"><?php echo e($item['available_stock']); ?> available</p>
                                        <?php if(! $item['product']->is_active || $item['available_stock'] < 1): ?>
                                            <p class="mt-1 text-sm text-red-600">Currently unavailable. Remove this item to continue.</p>
                                        <?php elseif($item['quantity'] > $item['available_stock']): ?>
                                            <p class="mt-1 text-sm text-amber-700">Only <?php echo e($item['available_stock']); ?> available; this cart item cannot be ordered.</p>
                                        <?php endif; ?>
                                    </div>
                                    <form x-data="{ quantity: <?php echo e($item['quantity']); ?>, available: <?php echo e($item['available_stock']); ?> }" x-ref="quantityForm" method="POST" action="<?php echo e(route('cart.items.update', $item['product'])); ?>" class="flex items-center gap-2">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <input type="hidden" name="quantity" x-model="quantity">
                                        <button type="button"
                                                aria-label="Decrease quantity of <?php echo e($item['product']->name); ?>"
                                                @click="quantity = Math.max(1, Math.min(available, quantity - 1)); $nextTick(() => $refs.quantityForm.requestSubmit())"
                                                :disabled="quantity <= 1 || available < 1"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">−</button>
                                        <span x-text="quantity" aria-live="polite" class="min-w-6 text-center text-sm font-semibold text-gray-900"><?php echo e($item['quantity']); ?></span>
                                        <button type="button"
                                                aria-label="Increase quantity of <?php echo e($item['product']->name); ?>"
                                                @click="quantity = Math.min(available, quantity + 1); $nextTick(() => $refs.quantityForm.requestSubmit())"
                                                :disabled="quantity >= available"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">+</button>
                                    </form>
                                    <div class="sm:text-right">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Subtotal</p>
                                        <p class="mt-1 font-black text-gray-900">₱<?php echo e(number_format($item['line_total'], 2)); ?></p>
                                    </div>
                                    <form method="POST" action="<?php echo e(route('cart.items.destroy', $item['product'])); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" aria-label="Remove <?php echo e($item['product']->name); ?> from cart" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                                            Remove
                                        </button>
                                    </form>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>

                    <aside class="h-fit rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h3 class="text-lg font-black text-gray-900">Checkout</h3>
                        <div class="mt-4 space-y-2 border-b border-gray-100 pb-4 text-sm">
                            <div class="flex justify-between gap-4 text-gray-600">
                                <span>Subtotal</span>
                                <span>₱<?php echo e(number_format($subtotal, 2)); ?></span>
                            </div>
                            <div class="flex justify-between gap-4 text-base font-black text-gray-900">
                                <span>Total</span>
                                <span>₱<?php echo e(number_format($subtotal, 2)); ?></span>
                            </div>
                        </div>

                        <form method="POST" action="<?php echo e(route('cart.order')); ?>" class="mt-5 space-y-5" x-data="{ fulfillment: '<?php echo e(old('fulfillment_method', 'pickup')); ?>' }">
                            <?php echo csrf_field(); ?>
                            <fieldset class="space-y-3">
                                <legend class="text-sm font-bold text-gray-900">Customer information</legend>
                                <label class="block text-sm font-medium text-gray-700">
                                    Full name
                                    <input type="text" name="customer_name" value="<?php echo e(old('customer_name', auth()->user()?->name)); ?>" maxlength="255" required autocomplete="name"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>
                                <label class="block text-sm font-medium text-gray-700">
                                    Email
                                    <input type="email" name="customer_email" value="<?php echo e(old('customer_email', auth()->user()?->email)); ?>" maxlength="255" required autocomplete="email"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>
                                <label class="block text-sm font-medium text-gray-700">
                                    Contact number <span class="font-normal text-gray-400">(optional)</span>
                                    <input type="tel" name="customer_phone" value="<?php echo e(old('customer_phone')); ?>" maxlength="40" autocomplete="tel"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>
                            </fieldset>

                            <fieldset class="space-y-3">
                                <legend class="text-sm font-bold text-gray-900">Delivery or pickup</legend>
                                <div class="grid grid-cols-2 gap-3">
                                    <?php $__currentLoopData = ['pickup' => 'Store pickup', 'delivery' => 'Delivery']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-3 py-3 text-sm font-semibold text-gray-700 has-checked:border-purple-600 has-checked:bg-purple-50 has-checked:text-purple-800">
                                            <input type="radio" name="fulfillment_method" value="<?php echo e($value); ?>" x-model="fulfillment" <?php if(old('fulfillment_method', 'pickup') === $value): echo 'checked'; endif; ?> class="border-gray-300 text-purple-700 focus:ring-purple-500">
                                            <?php echo e($label); ?>

                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <label x-cloak x-show="fulfillment === 'delivery'" class="block text-sm font-medium text-gray-700">
                                    Delivery address
                                    <textarea name="delivery_address" rows="3" maxlength="2000" :required="fulfillment === 'delivery'" autocomplete="street-address"
                                              class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500"><?php echo e(old('delivery_address')); ?></textarea>
                                </label>
                            </fieldset>

                            <fieldset class="space-y-3">
                                <legend class="text-sm font-bold text-gray-900">Mode of payment</legend>
                                <div class="grid grid-cols-2 gap-3">
                                    <?php $__currentLoopData = ['cash' => 'Cash', 'gcash' => 'GCash', 'bank_transfer' => 'Bank transfer', 'other' => 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-3 py-3 text-sm font-semibold text-gray-700 has-checked:border-purple-600 has-checked:bg-purple-50 has-checked:text-purple-800">
                                            <input type="radio" name="payment_method" value="<?php echo e($value); ?>" <?php if(old('payment_method', 'cash') === $value): echo 'checked'; endif; ?> class="border-gray-300 text-purple-700 focus:ring-purple-500">
                                            <?php echo e($label); ?>

                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <p class="text-xs text-gray-500">Payment is recorded as a preference only; online payment is not yet enabled.</p>
                            </fieldset>

                            <button type="submit" class="w-full rounded-xl bg-purple-700 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                Proceed to checkout
                            </button>
                            <p class="text-center text-xs leading-relaxed text-gray-500">Your order will be saved as pending until staff confirms it. Stock is deducted after acceptance.</p>
                        </form>
                    </aside>
                </div>
            <?php endif; ?>
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
<?php /**PATH C:\Users\MYPC\Downloads\IT_12-main (3)\IT_12-main\resources\views/store/cart.blade.php ENDPATH**/ ?>