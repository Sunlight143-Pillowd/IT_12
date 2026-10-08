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
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Build PC</h2>
            <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                Back to dashboard
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc space-y-1 pl-5">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-6 grid gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Build Information</p>
                        <p class="mt-2 text-lg font-black text-gray-900">Build #: <?php echo e('PC-' . str_pad((string) (count($builds) + 1), 6, '0', STR_PAD_LEFT)); ?></p>
                    </div>
                    <div>
                        <label for="customer_name" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Customer</label>
                        <input id="customer_name" name="customer_name" value="<?php echo e(old('customer_name')); ?>" form="build-pc-form" class="w-full rounded border border-gray-300 px-3 py-2 text-sm" placeholder="John Doe" />
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Status</p>
                        <p class="mt-2 text-sm font-semibold text-purple-600">Reserved</p>
                    </div>
                </div>

                <form id="build-pc-form" method="POST" action="<?php echo e(route('buildpc.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-6 grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="customer_email" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Customer Email</label>
                            <input id="customer_email" name="customer_email" value="<?php echo e(old('customer_email')); ?>" class="w-full rounded border border-gray-300 px-3 py-2 text-sm" placeholder="customer@example.com" />
                        </div>
                        <div>
                            <label for="notes" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Build Notes</label>
                            <input id="notes" name="notes" value="<?php echo e(old('notes')); ?>" class="w-full rounded border border-gray-300 px-3 py-2 text-sm" placeholder="Premium gaming setup" />
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <?php $__currentLoopData = $componentGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <label class="mb-2 block text-sm font-semibold text-gray-700"><?php echo e($label); ?></label>
                                <select name="items[<?php echo e($type); ?>][product_id]" data-key="<?php echo e($type); ?>" class="component-select w-full rounded border border-gray-300 px-3 py-2 text-sm">
                                    <option value="">Select <?php echo e($label); ?></option>
                                    <?php $__currentLoopData = $groupedProducts[$type] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($product->id); ?>"
                                            data-name="<?php echo e($product->name); ?>"
                                            data-price="<?php echo e($product->price); ?>"
                                            data-available="<?php echo e(max(0, (int) $product->stock_quantity - (int) $product->reservations()->where('status', 'active')->sum('quantity'))); ?>">
                                            <?php echo e($product->name); ?> (Avail: <?php echo e(max(0, (int) $product->stock_quantity - (int) $product->reservations()->where('status', 'active')->sum('quantity'))); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <div class="mt-3 flex items-center gap-2">
                                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Qty</label>
                                    <input type="number" name="items[<?php echo e($type); ?>][quantity]" data-key="<?php echo e($type); ?>" value="1" min="1" max="20" class="w-20 rounded border border-gray-300 px-2 py-1.5 text-sm" />
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Selected components</h3>
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Summary</div>
                        </div>

                        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="px-3 py-3 font-semibold">Product</th>
                                        <th class="px-3 py-3 font-semibold">Qty</th>
                                        <th class="px-3 py-3 font-semibold">Price</th>
                                        <th class="px-3 py-3 font-semibold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="build-summary-body">
                                    <tr>
                                        <td colspan="4" class="px-3 py-6 text-center text-gray-500">No components selected yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center justify-between rounded-lg border border-purple-200 bg-purple-50 px-4 py-3">
                            <span class="text-sm font-bold uppercase tracking-[0.2em] text-purple-700">Total</span>
                            <span id="build-total" class="text-2xl font-black text-purple-700">₱0</span>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center rounded bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-700">Save Build</button>
                        <button type="reset" class="inline-flex items-center rounded border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-gray-400">Reset</button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-black text-gray-900">Saved Builds</h3>
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500"><?php echo e($builds->count()); ?> build(s)</span>
                </div>

                <?php $__empty_1 = true; $__currentLoopData = $builds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="mb-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-bold uppercase tracking-[0.2em] text-gray-500"><?php echo e($build->build_number); ?></p>
                                <p class="mt-1 text-lg font-black text-gray-900"><?php echo e($build->customer_name ?: 'Walk-in Customer'); ?></p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-purple-700"><?php echo e($build->status); ?></span>
                                <span class="rounded-full bg-gray-200 px-3 py-1 text-xs font-bold uppercase tracking-wide text-gray-700">₱<?php echo e(number_format($build->total_cost, 2)); ?></span>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <ul class="space-y-2 text-sm text-gray-700">
                                <?php $__currentLoopData = $build->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="flex items-center justify-between gap-3 border-b border-gray-200 pb-1 last:border-0">
                                        <span><?php echo e($item->product->name); ?></span>
                                        <span class="font-semibold">x<?php echo e($item->quantity); ?></span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>

                            <div class="flex flex-wrap gap-2 md:justify-end">
                                <a href="<?php echo e(route('buildpc.show', $build)); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 hover:border-gray-400">View</a>
                                <a href="<?php echo e(route('buildpc.print', $build)); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 hover:border-gray-400">Print</a>
                                <?php if(! in_array($build->status, ['sold', 'cancelled', 'expired'], true)): ?>
                                    <form action="<?php echo e(route('buildpc.sell', $build)); ?>" method="POST" onsubmit="return confirm('Mark this build as sold?');">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="rounded bg-emerald-600 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-emerald-700">Sell</button>
                                    </form>
                                    <form action="<?php echo e(route('buildpc.cancel', $build)); ?>" method="POST" onsubmit="return confirm('Cancel this build and release the stock hold?');">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="rounded bg-red-600 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-red-700">Cancel</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-gray-500">
                        No saved PC builds yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const summaryBody = document.getElementById('build-summary-body');
        const totalEl = document.getElementById('build-total');
        const selects = document.querySelectorAll('.component-select');

        function formatMoney(value) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP',
                maximumFractionDigits: 2,
            }).format(value);
        }

        function renderBuildSummary() {
            const rows = [];
            let total = 0;

            selects.forEach((select) => {
                const key = select.dataset.key;
                const option = select.selectedOptions[0];
                const quantityInput = document.querySelector(`input[data-key="${key}"]`);

                if (!option || !option.value) {
                    return;
                }

                const quantity = Number(quantityInput?.value || 1);
                const price = Number(option.dataset.price || 0);
                const subtotal = price * quantity;

                total += subtotal;
                rows.push(`
                    <tr>
                        <td class="px-3 py-3 font-semibold text-gray-900">${option.dataset.name}</td>
                        <td class="px-3 py-3 text-gray-700">${quantity}</td>
                        <td class="px-3 py-3 text-gray-700">${formatMoney(price)}</td>
                        <td class="px-3 py-3 font-bold text-purple-700">${formatMoney(subtotal)}</td>
                    </tr>
                `);
            });

            if (rows.length === 0) {
                summaryBody.innerHTML = '<tr><td colspan="4" class="px-3 py-6 text-center text-gray-500">No components selected yet.</td></tr>';
            } else {
                summaryBody.innerHTML = rows.join('');
            }

            totalEl.textContent = formatMoney(total);
        }

        selects.forEach((select) => {
            select.addEventListener('change', renderBuildSummary);
        });

        document.querySelectorAll('input[data-key]').forEach((input) => {
            input.addEventListener('input', renderBuildSummary);
        });

        renderBuildSummary();
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
<?php /**PATH C:\Users\Francisco Luis\Downloads\IT_12\resources\views/build-pc.blade.php ENDPATH**/ ?>