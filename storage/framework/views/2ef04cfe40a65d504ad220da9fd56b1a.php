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
                        <div class="mb-5">
                            <h2 class="text-lg font-black text-gray-900">Choose your components</h2>
                            <p class="mt-1 text-sm text-gray-500">Choose available parts and see the estimated total before submitting.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <?php $__currentLoopData = $componentGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <label for="component-<?php echo e($type); ?>" class="mb-2 block text-sm font-bold text-gray-800"><?php echo e($label); ?></label>
                                    <select id="component-<?php echo e($type); ?>" name="items[<?php echo e($type); ?>][product_id]" data-key="<?php echo e($type); ?>" class="component-select w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        <option value="">Select <?php echo e($label); ?></option>
                                        <?php $__currentLoopData = $groupedProducts[$type] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($product->id); ?>" data-name="<?php echo e($product->name); ?>" data-price="<?php echo e($product->price); ?>" data-available="<?php echo e($product->availableStock()); ?>">
                                                <?php echo e($product->name); ?> — ₱<?php echo e(number_format($product->price, 2)); ?> (<?php echo e($product->availableStock()); ?> available)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-lg font-black text-gray-900">Selected components</h2>
                                <p class="mt-1 text-sm text-gray-500">Availability is checked again at checkout.</p>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">Build status: <span class="text-amber-700">Pending</span></p>
                        </div>
                        <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                    <tr><th class="px-4 py-3">Component</th><th class="px-4 py-3">Availability</th><th class="px-4 py-3 text-right">Price</th></tr>
                                </thead>
                                <tbody id="build-summary-body">
                                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">Select components to start your build.</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex items-center justify-between rounded-xl bg-purple-50 px-4 py-4">
                            <span class="text-sm font-bold uppercase tracking-wide text-purple-800">Estimated total</span>
                            <span id="build-total" class="text-2xl font-black text-purple-800">₱0.00</span>
                        </div>
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <?php if(auth()->guard()->check()): ?>
                                <button type="submit" style="background-color: #6b21a8; color: #fff;" class="inline-flex justify-center rounded-xl px-5 py-3 text-sm font-bold shadow-sm transition hover:brightness-90 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"><span>Checkout</span></button>
                            <?php else: ?>
                                <a href="<?php echo e(route('login')); ?>" class="inline-flex justify-center rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800">Sign in to checkout</a>
                            <?php endif; ?>
                            <p class="text-xs text-gray-500">Stock is deducted only after your build request is accepted.</p>
                        </div>
                    </section>
                </form>

            </div>

            <?php if(auth()->guard()->check()): ?>
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-xl font-black text-gray-900">My PC builds</h2>
                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold text-purple-800"><?php echo e($builds->count()); ?> build(s)</span>
                    </div>
                    <?php $__empty_1 = true; $__currentLoopData = $builds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="mb-4 rounded-xl border border-gray-200 p-4 last:mb-0">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Build ID · <?php echo e($build->build_number); ?></p>
                                    <h3 class="mt-1 text-lg font-bold text-gray-900"><?php echo e($build->customer_name); ?></h3>
                                    <p class="text-sm text-gray-500"><?php echo e($build->created_at->format('M j, Y')); ?></p>
                                </div>
                                <span class="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800"><?php echo e(ucfirst($build->status)); ?></span>
                            </div>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <dl class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-2 text-sm">
                                    <?php $__currentLoopData = $build->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <dt class="font-semibold text-gray-500"><?php echo e($item->product->category); ?></dt>
                                        <dd class="text-gray-800"><?php echo e($item->product->name); ?></dd>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <dt class="font-bold text-gray-900">Total price</dt>
                                    <dd class="font-black text-purple-800">₱<?php echo e(number_format($build->total_cost, 2)); ?></dd>
                                </dl>
                                <div class="grid grid-cols-3 gap-2">
                                    <?php $__currentLoopData = ['Product photo' => $build->product_photo_path, 'Before' => $build->before_photo_path, 'After' => $build->after_photo_path]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div>
                                            <p class="mb-1 text-[10px] font-bold uppercase tracking-wide text-gray-500"><?php echo e($label); ?></p>
                                            <?php if($path): ?>
                                                <a href="<?php echo e(asset('storage/'.$path)); ?>" target="_blank" rel="noopener"><img src="<?php echo e(asset('storage/'.$path)); ?>" alt="<?php echo e($label); ?> for <?php echo e($build->build_number); ?>" class="aspect-square w-full rounded-lg object-cover"></a>
                                            <?php else: ?>
                                                <div class="flex aspect-square items-center justify-center rounded-lg bg-gray-100 text-center text-[10px] text-gray-400">Photo pending</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">Your submitted builds will appear here.</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const summaryBody = document.getElementById('build-summary-body');
        const totalEl = document.getElementById('build-total');
        const componentSelects = document.querySelectorAll('.component-select');
        const moneyFormatter = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });

        function updateBuildSummary() {
            const rows = [];
            let total = 0;

            componentSelects.forEach((select) => {
                const option = select.selectedOptions[0];
                if (!option || !option.value) {
                    return;
                }

                const price = Number(option.dataset.price || 0);
                const available = Number(option.dataset.available || 0);
                total += price;

                const row = document.createElement('tr');
                row.className = 'border-t border-gray-100';
                [option.dataset.name, available >= 1 ? `${available} available` : 'Out of stock', moneyFormatter.format(price)].forEach((value, index) => {
                    const cell = document.createElement('td');
                    cell.className = `px-4 py-3 ${index === 2 ? 'text-right font-bold text-purple-800' : 'text-gray-700'}`;
                    cell.textContent = value;
                    row.appendChild(cell);
                });
                rows.push(row);
            });

            summaryBody.replaceChildren();
            if (rows.length === 0) {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 3;
                cell.className = 'px-4 py-6 text-center text-gray-500';
                cell.textContent = 'Select components to start your build.';
                row.appendChild(cell);
                summaryBody.appendChild(row);
            } else {
                rows.forEach((row) => summaryBody.appendChild(row));
            }
            totalEl.textContent = moneyFormatter.format(total);
        }

        componentSelects.forEach((select) => select.addEventListener('change', updateBuildSummary));
        updateBuildSummary();
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
<?php /**PATH C:\Users\MYPC\Downloads\IT_12-main (2)\IT_12-main\resources\views\store\build-pc.blade.php ENDPATH**/ ?>