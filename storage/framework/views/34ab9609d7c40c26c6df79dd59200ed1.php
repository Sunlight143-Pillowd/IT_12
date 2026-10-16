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
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-700">Custom configuration</p>
                <h1 class="mt-1 text-2xl font-black text-gray-900">Build your PC</h1>
            </div>
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="text-sm font-semibold text-purple-700 hover:underline">View my orders and builds</a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-4">
        <div class="mx-auto max-w-7xl space-y-7 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div>
                <form id="customer-build-form" method="POST" action="<?php echo e(route('buildpc.customer.store')); ?>" class="space-y-5">
                    <?php echo csrf_field(); ?>

                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="mb-5 grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="customer_name" class="mb-2 block text-sm font-bold text-gray-800">Your name</label>
                                <input id="customer_name" name="customer_name" type="text" value="<?php echo e(old('customer_name', $pendingBuild['customer_name'] ?? auth()->user()?->name ?? '')); ?>" required maxlength="150" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="John Doe">
                            </div>
                            <div>
                                <label for="customer_email" class="mb-2 block text-sm font-bold text-gray-800">Email address <span class="font-normal text-gray-400">(optional)</span></label>
                                <input id="customer_email" name="customer_email" type="email" value="<?php echo e(old('customer_email', $pendingBuild['customer_email'] ?? auth()->user()?->email ?? '')); ?>" maxlength="150" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="you@example.com">
                            </div>
                        </div>
                        <div class="mb-5">
                            <h2 class="text-lg font-black text-gray-900">Choose your components</h2>
                            <p class="mt-1 text-sm text-gray-500">Choose from currently available products. Select at least one component; our team will review your request and provide a quote.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <?php $__currentLoopData = $componentGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <label for="component-<?php echo e($type); ?>" class="mb-2 block text-sm font-bold text-gray-800"><?php echo e($label); ?></label>
                                    <select id="component-<?php echo e($type); ?>" name="components[<?php echo e($type); ?>]" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm transition focus:border-purple-500 focus:ring-purple-500">
                                        <option value="">Select <?php echo e($label); ?> (optional)</option>
                                        <?php $__currentLoopData = $groupedProducts[$type] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($product->id); ?>" data-name="<?php echo e($product->name); ?>" data-price="<?php echo e($product->price); ?>" <?php if((string) old('components.'.$type, $pendingBuild['components'][$type] ?? '') === (string) $product->id): echo 'selected'; endif; ?>>
                                                <?php echo e($product->name); ?> — ₱<?php echo e(number_format($product->price, 2)); ?> · <?php echo e($product->availableStock()); ?> available
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <section id="customer-build-receipt" aria-live="polite" class="mt-5 rounded-xl border border-purple-200 bg-purple-50 p-4">
                            <div class="mb-3">
                                <h3 class="text-base font-black text-purple-900">Estimated receipt</h3>
                                <p class="mt-1 text-xs text-purple-800">Catalog prices include 12% Philippine VAT. Final quote will be confirmed by our team.</p>
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-purple-100 bg-white">
                                <table class="min-w-full text-left text-sm">
                                    <thead class="bg-purple-50 text-xs uppercase tracking-wide text-purple-800">
                                        <tr><th class="px-3 py-2">Component</th><th class="px-3 py-2 text-right">Amount</th></tr>
                                    </thead>
                                    <tbody id="customer-build-receipt-items">
                                        <tr><td colspan="2" class="px-3 py-4 text-center text-gray-500">Select components to see your estimate.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <dl class="mt-3 space-y-2 text-sm">
                                <div class="flex justify-between gap-4 text-gray-700">
                                    <dt>Amount before VAT</dt>
                                    <dd id="customer-build-net" class="font-semibold">₱0.00</dd>
                                </div>
                                <div class="flex justify-between gap-4 text-gray-700">
                                    <dt>VAT (12%, included)</dt>
                                    <dd id="customer-build-vat" class="font-semibold">₱0.00</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-t border-purple-200 pt-2 font-black text-purple-900">
                                    <dt>Estimated total (VAT included)</dt>
                                    <dd id="customer-build-total">₱0.00</dd>
                                </div>
                            </dl>
                        </section>
                        <label for="build-notes" class="mt-5 block text-sm font-bold text-gray-800">
                            Additional notes <span class="font-normal text-gray-400">(optional)</span>
                            <textarea id="build-notes" name="notes" rows="3" maxlength="2000" placeholder="Budget, intended use, or other preferences" class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500"><?php echo e(old('notes', $pendingBuild['notes'] ?? '')); ?></textarea>
                        </label>
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <button type="submit" class="inline-flex justify-center rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">Submit build request</button>
                            <p class="text-xs text-gray-500">
                                Estimated price uses the selected catalog parts. Additional notes are not included; our team will confirm the final quote.
                                <?php if(auth()->guard()->guest()): ?>
                                    <span class="font-semibold text-purple-700">You’ll be asked to sign in before submitting.</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </section>
                </form>

            </div>

            <?php if(auth()->guard()->check()): ?>
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-xl font-black text-gray-900">PC builds</h2>
                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold text-purple-800"><?php echo e($builds->count()); ?> build(s)</span>
                    </div>
                    <?php $__empty_1 = true; $__currentLoopData = $builds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="mb-4 rounded-xl border border-gray-200 p-4 last:mb-0">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Build ID · <?php echo e($build->build_number); ?></p>
                                    <h3 class="mt-1 text-lg font-bold text-gray-900"><?php echo e($build->customer_name); ?></h3>
                                    <p class="text-sm text-gray-500"><?php echo e($build->created_at->format('M j, Y')); ?></p>
                                    <?php if($build->notes): ?>
                                        <p class="mt-2 whitespace-pre-line text-sm text-gray-700"><?php echo e($build->notes); ?></p>
                                    <?php endif; ?>
                                    <p class="mt-2 text-sm font-semibold text-gray-700">
                                        <?php if($build->estimated_cost !== null): ?>
                                            <?php
                                                $buildVat = \App\Support\PhilippineVat::breakdown((float) $build->estimated_cost);
                                            ?>
                                            Estimated price (VAT included): ₱<?php echo e(number_format($buildVat['total'], 2)); ?>

                                            <span class="block text-xs font-normal text-gray-500">Includes ₱<?php echo e(number_format($buildVat['vat'], 2)); ?> Philippine VAT (12%).</span>
                                        <?php else: ?>
                                            Quote pending
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <span class="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800"><?php echo e(ucfirst($build->status)); ?></span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="<?php echo e(route('buildpc.show', $build)); ?>" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">View</a>
                                <a href="<?php echo e(route('buildpc.print', $build)); ?>" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Print</a>
                                <a href="<?php echo e(route('buildpc.customer.edit', $build)); ?>" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Edit</a>
                                <form action="<?php echo e(route('buildpc.customer.destroy', $build)); ?>" method="POST" onsubmit="return confirm('Delete this build?');" class="inline-block">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="inline-flex items-center justify-center rounded border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-red-700 transition hover:border-red-300 hover:bg-red-100">Delete</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">No builds yet. Start by building your first custom PC.</div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </div>

    <script>
        (() => {
            const componentSelects = document.querySelectorAll('#customer-build-form select[name^="components["]');
            const receiptItems = document.getElementById('customer-build-receipt-items');
            const netElement = document.getElementById('customer-build-net');
            const vatElement = document.getElementById('customer-build-vat');
            const totalElement = document.getElementById('customer-build-total');
            const moneyFormatter = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
            const vatRate = <?php echo e(\App\Support\PhilippineVat::RATE_PERCENT); ?>;

            function updateEstimatedReceipt() {
                const rows = [];
                let total = 0;

                componentSelects.forEach((select) => {
                    const option = select.selectedOptions[0];
                    if (!option || !option.value) {
                        return;
                    }

                    const price = Number(option.dataset.price || 0);
                    total += price;

                    const row = document.createElement('tr');
                    row.className = 'border-t border-gray-100';
                    const componentCell = document.createElement('td');
                    componentCell.className = 'px-3 py-2 font-medium text-gray-800';
                    componentCell.textContent = option.dataset.name;
                    const amountCell = document.createElement('td');
                    amountCell.className = 'px-3 py-2 text-right font-semibold text-gray-900';
                    amountCell.textContent = moneyFormatter.format(price);
                    row.append(componentCell, amountCell);
                    rows.push(row);
                });

                receiptItems.replaceChildren();
                if (rows.length === 0) {
                    const row = document.createElement('tr');
                    const cell = document.createElement('td');
                    cell.colSpan = 2;
                    cell.className = 'px-3 py-4 text-center text-gray-500';
                    cell.textContent = 'Select components to see your estimate.';
                    row.appendChild(cell);
                    receiptItems.appendChild(row);
                } else {
                    rows.forEach((row) => receiptItems.appendChild(row));
                }

                const vat = Math.round(total * vatRate / (100 + vatRate) * 100) / 100;
                netElement.textContent = moneyFormatter.format(total - vat);
                vatElement.textContent = moneyFormatter.format(vat);
                totalElement.textContent = moneyFormatter.format(total);
            }

            componentSelects.forEach((select) => select.addEventListener('change', updateEstimatedReceipt));
            updateEstimatedReceipt();
        })();
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views\store\build-pc.blade.php ENDPATH**/ ?>