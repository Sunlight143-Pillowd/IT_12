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
            <a href="<?php echo e(route('dashboard')); ?>" class="text-sm font-semibold text-purple-700 hover:underline">Back to dashboard</a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-4">
        <div class="mx-auto max-w-7xl space-y-7 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <?php echo e(session('success')); ?>

                </div>
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

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.6fr)_minmax(20rem,1fr)] lg:items-start">
                <form id="build-pc-form" method="POST" action="<?php echo e(route('buildpc.store')); ?>" class="min-w-0 space-y-5">
                <?php echo csrf_field(); ?>

                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="customer_name" class="mb-2 block text-sm font-bold text-gray-800">Customer name</label>
                            <input id="customer_name" name="customer_name" type="text" required value="<?php echo e(old('customer_name', auth()->user()?->name ?? '')); ?>" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="John Doe">
                        </div>
                        <div>
                            <label for="customer_email" class="mb-2 block text-sm font-bold text-gray-800">Customer email</label>
                            <input id="customer_email" name="customer_email" type="email" value="<?php echo e(old('customer_email', auth()->user()?->email ?? '')); ?>" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="customer@example.com">
                        </div>
                    </div>
                    <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-gray-900">Choose components</h2>
                            <p class="mt-1 text-sm text-gray-500">Select available parts and review the estimated total before saving.</p>
                        </div>
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
                            <p class="mt-1 text-sm text-gray-500">Review the selected parts and their current availability.</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-500">Build #: <?php echo e('PC-' . str_pad((string) (count($builds) + 1), 6, '0', STR_PAD_LEFT)); ?></p>
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
                        <button type="submit" class="inline-flex justify-center rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800">Save Build</button>
                        <button type="reset" class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm transition hover:border-gray-400">Reset</button>
                    </div>
                </section>
                </form>

                <section class="flex min-h-0 flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:sticky lg:top-6 lg:max-h-[calc(100vh-3rem)]">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-5">
                        <div>
                            <h2 class="text-xl font-black text-gray-900">Customer Build / PC Build Management</h2>
                            <p class="mt-1 text-sm text-gray-500">Review components, update the build workflow, and attach progress photos.</p>
                        </div>
                        <a href="<?php echo e(route('buildpc.customer')); ?>" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">Customer build page</a>
                    </div>
                    <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
                        <?php $__empty_1 = true; $__currentLoopData = $customerBuildManagement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <article class="rounded-xl border border-gray-200 p-4">
                                <div class="flex flex-col gap-4">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-lg font-black text-gray-900"><?php echo e($build->build_number); ?></h3>
                                            <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase text-purple-800"><?php echo e(ucfirst($build->status)); ?></span>
                                        </div>
                                        <p class="mt-1 text-sm font-semibold text-gray-800"><?php echo e($build->customer_name); ?></p>
                                        <p class="text-xs text-gray-500">Build date: <?php echo e($build->created_at->format('M j, Y')); ?></p>
                                        <ul class="mt-3 grid gap-x-6 gap-y-1 text-sm text-gray-700 sm:grid-cols-2">
                                            <?php $__currentLoopData = $build->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($item->product->category); ?>: <?php echo e($item->product->name); ?> × <?php echo e($item->quantity); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                        <p class="mt-3 font-black text-gray-900">Total price: ₱<?php echo e(number_format($build->total_cost, 2)); ?></p>
                                    </div>

                                    <form method="POST" action="<?php echo e(route('dashboard.customer-builds.update', $build)); ?>" enctype="multipart/form-data" class="w-full space-y-4 rounded-xl bg-gray-50 p-4">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">
                                            <label class="block text-sm font-semibold text-gray-700">
                                                Build status
                                                <select name="status" class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-purple-500 focus:ring-purple-500">
                                                    <option value="<?php echo e($build->status); ?>"><?php echo e(ucfirst($build->status)); ?> (current)</option>
                                                    <?php $__currentLoopData = $buildStatusOptions[$build->status] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nextStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($nextStatus); ?>"><?php echo e(ucfirst($nextStatus)); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </label>
                                            <button type="submit" class="rounded-lg bg-purple-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-purple-800">Save updates</button>
                                        </div>
                                        <div class="grid grid-cols-3 gap-3">
                                            <?php $__currentLoopData = [
                                                'product_photo' => ['Product photo', $build->product_photo_path],
                                                'before_photo' => ['Before photo', $build->before_photo_path],
                                                'after_photo' => ['After photo', $build->after_photo_path],
                                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => [$label, $path]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $previewId = 'build-'.$build->id.'-'.$field;
                                                ?>
                                                <div class="min-w-0">
                                                    <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-gray-500"><?php echo e($label); ?></p>
                                                    <?php if($path): ?>
                                                        <img id="<?php echo e($previewId); ?>" src="<?php echo e(asset('storage/'.$path)); ?>" alt="<?php echo e($label); ?> for <?php echo e($build->build_number); ?>" class="mb-2 aspect-square w-full rounded-lg bg-white object-cover">
                                                    <?php else: ?>
                                                        <div id="<?php echo e($previewId); ?>" class="mb-2 flex aspect-square items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white text-xs text-gray-400">No photo</div>
                                                    <?php endif; ?>
                                                    <label class="flex cursor-pointer items-center justify-center gap-1 rounded-lg border border-gray-300 bg-white px-2 py-2 text-center text-xs font-bold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">
                                                        <span aria-hidden="true" class="text-base leading-none">+</span> Add Photo
                                                        <input type="file" name="<?php echo e($field); ?>" accept="image/jpeg,image/png,image/webp" class="build-photo-input sr-only" data-preview="<?php echo e($previewId); ?>">
                                                    </label>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">No customer PC builds have been submitted.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-black text-gray-900">Saved Builds</h3>
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500"><?php echo e($builds->count()); ?> build(s)</span>
                </div>

                <?php $__empty_1 = true; $__currentLoopData = $builds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="mb-4 rounded-xl border border-gray-200 p-4 last:mb-0">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Build ID · <?php echo e($build->build_number); ?></p>
                                <h4 class="mt-1 text-lg font-bold text-gray-900"><?php echo e($build->customer_name ?: 'Customer not recorded'); ?></h4>
                                <p class="text-sm text-gray-500"><?php echo e($build->created_at->format('M j, Y')); ?></p>
                            </div>
                            <span class="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800"><?php echo e(ucfirst($build->status)); ?></span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="<?php echo e(route('buildpc.show', $build)); ?>" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">View</a>
                            <a href="<?php echo e(route('buildpc.print', $build)); ?>" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Print</a>
                            <a href="<?php echo e(route('buildpc.edit', $build)); ?>" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Edit</a>
                            <form action="<?php echo e(route('buildpc.destroy', $build)); ?>" method="POST" onsubmit="return confirm('Delete this build?');" class="inline-block">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="inline-flex items-center justify-center rounded border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-red-700 transition hover:border-red-300 hover:bg-red-100">Delete</button>
                            </form>
                            <form action="<?php echo e(route('buildpc.cancel', $build)); ?>" method="POST" class="inline-block">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="inline-flex items-center justify-center rounded border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-amber-700 transition hover:border-amber-300 hover:bg-amber-100">Cancel order</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">No builds yet.</div>
                <?php endif; ?>
            </section>

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
        document.getElementById('build-pc-form').addEventListener('reset', () => {
            window.setTimeout(updateBuildSummary, 0);
        });
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
<?php /**PATH C:\Users\MYPC\Downloads\IT_12-main (3)\IT_12-main\resources\views/build-pc.blade.php ENDPATH**/ ?>