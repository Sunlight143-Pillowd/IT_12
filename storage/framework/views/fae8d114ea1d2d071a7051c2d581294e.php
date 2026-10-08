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
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Edit PC Build</h2>
            <a href="<?php echo e($isCustomer ? route('buildpc.customer') : route('buildpc.index')); ?>" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                Back to builds
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Build</p>
                        <h3 class="mt-2 text-2xl font-black text-gray-900"><?php echo e($pcBuild->build_number); ?></h3>
                    </div>
                    <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-purple-700"><?php echo e($pcBuild->status); ?></span>
                </div>

                <form method="POST" action="<?php echo e($isCustomer ? route('buildpc.customer.update', $pcBuild) : route('buildpc.update', $pcBuild)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <?php $__currentLoopData = $componentGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <label class="mb-2 block text-sm font-semibold text-gray-700"><?php echo e($label); ?></label>
                                <select name="items[<?php echo e($type); ?>][product_id]" data-key="<?php echo e($type); ?>" class="component-select w-full rounded border border-gray-300 px-3 py-2 text-sm">
                                    <option value="">Remove / no selection</option>
                                    <?php $__currentLoopData = $groupedProducts[$type] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($product->id); ?>"
                                            data-name="<?php echo e($product->name); ?>"
                                            data-price="<?php echo e($product->price); ?>"
                                            data-available="<?php echo e(max(0, (int) $product->stock_quantity - (int) $product->reservations()->where('status', 'active')->sum('quantity'))); ?>"
                                            <?php echo e(($selectedProducts[$type] ?? null) == $product->id ? 'selected' : ''); ?>>
                                            <?php echo e($product->name); ?> (Avail: <?php echo e(max(0, (int) $product->stock_quantity - (int) $product->reservations()->where('status', 'active')->sum('quantity'))); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
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
                                        <th class="px-3 py-3 font-semibold">Availability</th>
                                        <th class="px-3 py-3 font-semibold text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody id="build-summary-body">
                                    <tr>
                                        <td colspan="3" class="px-3 py-6 text-center text-gray-500">No components selected yet.</td>
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
                        <button type="submit" class="inline-flex items-center rounded bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-700">Update Build</button>
                        <a href="<?php echo e($isCustomer ? route('buildpc.customer') : route('buildpc.index')); ?>" class="inline-flex items-center rounded border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-gray-400">Cancel</a>
                    </div>
                </form>
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
                const option = select.selectedOptions[0];
                if (!option || !option.value) {
                    return;
                }

                const price = Number(option.dataset.price || 0);
                const available = Number(option.dataset.available || 0);
                total += price;

                rows.push(`
                    <tr>
                        <td class="px-3 py-3 font-semibold text-gray-900">${option.dataset.name}</td>
                        <td class="px-3 py-3 text-gray-700">${available >= 1 ? `${available} available` : 'Out of stock'}</td>
                        <td class="px-3 py-3 text-right font-bold text-purple-700">${formatMoney(price)}</td>
                    </tr>
                `);
            });

            if (rows.length === 0) {
                summaryBody.innerHTML = '<tr><td colspan="3" class="px-3 py-6 text-center text-gray-500">No components selected yet.</td></tr>';
            } else {
                summaryBody.innerHTML = rows.join('');
            }

            totalEl.textContent = formatMoney(total);
        }

        selects.forEach((select) => select.addEventListener('change', renderBuildSummary));
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/build-pc-edit.blade.php ENDPATH**/ ?>