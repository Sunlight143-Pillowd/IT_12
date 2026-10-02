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
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Stock In from Delivery</h2>
            <a href="<?php echo e(route('inventory.index')); ?>" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600 hover:text-purple-700">Back to Inventory</a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <p class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"><?php echo e(session('success')); ?></p>
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

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-gray-900">Record Supplier Delivery</h3>
                <form method="POST" action="<?php echo e(route('stock-in.store')); ?>" enctype="multipart/form-data" class="space-y-5">
                    <?php echo csrf_field(); ?>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <label class="text-sm font-semibold text-gray-700">Supplier
                            <input name="supplier_name" required value="<?php echo e(old('supplier_name')); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">Supplier contact
                            <input name="supplier_contact" value="<?php echo e(old('supplier_contact')); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">Invoice number
                            <input name="invoice_number" required value="<?php echo e(old('invoice_number')); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">Date received
                            <input type="date" name="received_at" required value="<?php echo e(old('received_at', now()->toDateString())); ?>" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="text-sm font-semibold text-gray-700">Delivery document
                            <input type="file" name="delivery_document" accept=".pdf,.jpg,.jpeg,.png,.webp" required class="mt-1 block w-full text-sm font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">Before delivery photo
                            <input type="file" name="before_photo" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">After delivery photo
                            <input type="file" name="after_photo" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm font-normal">
                        </label>
                    </div>

                    <div>
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <h4 class="font-bold text-gray-900">Delivered items</h4>
                            <button id="add-delivery-item" type="button" class="rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Add item</button>
                        </div>
                        <div id="delivery-items" class="space-y-3">
                            <div class="delivery-item grid gap-3 rounded border border-gray-200 bg-gray-50 p-3 md:grid-cols-2 xl:grid-cols-5">
                                <label class="text-xs font-semibold text-gray-600">Product
                                    <select name="items[0][product_id]" required class="delivery-product mt-1 w-full rounded border border-gray-300 bg-white px-2 py-2 text-sm">
                                        <option value="">Select product</option>
                                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($product->id); ?>" data-requires-serial="<?php echo e($product->requires_serial ? '1' : '0'); ?>"><?php echo e($product->name); ?><?php echo e($product->requires_serial ? ' · serial required' : ''); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </label>
                                <label class="text-xs font-semibold text-gray-600">Quantity
                                    <input type="number" name="items[0][quantity]" min="1" value="1" required class="delivery-quantity mt-1 w-full rounded border border-gray-300 bg-white px-2 py-2 text-sm">
                                </label>
                                <label class="text-xs font-semibold text-gray-600">Unit cost
                                    <input type="number" name="items[0][unit_cost]" min="0" step="0.01" required class="mt-1 w-full rounded border border-gray-300 bg-white px-2 py-2 text-sm">
                                </label>
                                <label class="text-xs font-semibold text-gray-600">Warranty (months)
                                    <input type="number" name="items[0][warranty_months]" min="0" max="1200" class="mt-1 w-full rounded border border-gray-300 bg-white px-2 py-2 text-sm">
                                </label>
                                <label class="text-xs font-semibold text-gray-600 md:col-span-2 xl:col-span-1">Serial numbers, one per line
                                    <textarea name="items[0][serial_numbers]" rows="2" class="delivery-serials mt-1 w-full rounded border border-gray-300 bg-white px-2 py-2 text-sm"></textarea>
                                </label>
                            </div>
                        </div>
                    </div>

                    <label class="block text-sm font-semibold text-gray-700">Delivery notes
                        <textarea name="notes" rows="2" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal"></textarea>
                    </label>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-purple-700">Record delivery</button>
                    </div>
                </form>

                <template id="delivery-item-template">
                    <div class="delivery-item grid gap-3 rounded border border-gray-200 bg-gray-50 p-3 md:grid-cols-2 xl:grid-cols-5">
                        <label class="text-xs font-semibold text-gray-600">Product
                            <select required class="delivery-product mt-1 w-full rounded border border-gray-300 bg-white px-2 py-2 text-sm">
                                <option value="">Select product</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($product->id); ?>" data-requires-serial="<?php echo e($product->requires_serial ? '1' : '0'); ?>"><?php echo e($product->name); ?><?php echo e($product->requires_serial ? ' · serial required' : ''); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                        <label class="text-xs font-semibold text-gray-600">Quantity
                            <input type="number" min="1" value="1" required class="delivery-quantity mt-1 w-full rounded border border-gray-300 bg-white px-2 py-2 text-sm">
                        </label>
                        <label class="text-xs font-semibold text-gray-600">Unit cost
                            <input type="number" min="0" step="0.01" required class="mt-1 w-full rounded border border-gray-300 bg-white px-2 py-2 text-sm">
                        </label>
                        <label class="text-xs font-semibold text-gray-600">Warranty (months)
                            <input type="number" min="0" max="1200" class="mt-1 w-full rounded border border-gray-300 bg-white px-2 py-2 text-sm">
                        </label>
                        <label class="text-xs font-semibold text-gray-600 md:col-span-2 xl:col-span-1">Serial numbers, one per line
                            <textarea rows="2" class="delivery-serials mt-1 w-full rounded border border-gray-300 bg-white px-2 py-2 text-sm"></textarea>
                        </label>
                        <button type="button" class="remove-delivery-item justify-self-start text-xs font-semibold text-red-600">Remove item</button>
                    </div>
                </template>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 p-5">
                    <h3 class="text-lg font-bold text-gray-900">Recent deliveries</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-3">Received</th><th class="px-4 py-3">Supplier</th><th class="px-4 py-3">Invoice</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Proof</th></tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $stockIns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stockIn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="border-t border-gray-200 align-top">
                                    <td class="px-4 py-3"><?php echo e($stockIn->received_at->format('M j, Y')); ?></td>
                                    <td class="px-4 py-3 font-semibold"><?php echo e($stockIn->supplier_name); ?></td>
                                    <td class="px-4 py-3"><?php echo e($stockIn->invoice_number); ?></td>
                                    <td class="px-4 py-3"><?php echo e($stockIn->items->map(fn ($item) => $item->product->name.' × '.$item->quantity)->join(', ')); ?></td>
                                    <td class="px-4 py-3">
                                        <?php if($stockIn->delivery_document_path): ?>
                                            <a class="font-semibold text-purple-700 hover:underline" href="<?php echo e(asset('storage/'.$stockIn->delivery_document_path)); ?>" target="_blank" rel="noopener">Document</a>
                                        <?php endif; ?>
                                        <?php $__currentLoopData = ['before_photo_path' => 'Before photo', 'after_photo_path' => 'After photo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($stockIn->{$field}): ?>
                                                <a class="ml-2 font-semibold text-purple-700 hover:underline" href="<?php echo e(asset('storage/'.$stockIn->{$field})); ?>" target="_blank" rel="noopener"><?php echo e($label); ?></a>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No supplier deliveries recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script>
        (() => {
            const itemsContainer = document.getElementById('delivery-items');
            const itemTemplate = document.getElementById('delivery-item-template');
            let nextItemIndex = 1;

            function updateSerialRequirement(item) {
                const product = item.querySelector('.delivery-product');
                const serials = item.querySelector('.delivery-serials');
                const requiresSerial = product.selectedOptions[0]?.dataset.requiresSerial === '1';
                serials.required = requiresSerial;
                serials.placeholder = requiresSerial ? 'Required: one serial per unit' : 'Optional: one serial per unit';
            }

            function addItem() {
                const item = itemTemplate.content.firstElementChild.cloneNode(true);
                const fieldNames = {
                    '.delivery-product': 'product_id',
                    '.delivery-quantity': 'quantity',
                    'input[type="number"][min="0"]': 'unit_cost',
                    'input[type="number"][max="1200"]': 'warranty_months',
                    '.delivery-serials': 'serial_numbers',
                };

                Object.entries(fieldNames).forEach(([selector, name]) => {
                    item.querySelector(selector).name = `items[${nextItemIndex}][${name}]`;
                });

                nextItemIndex += 1;
                itemsContainer.appendChild(item);
                updateSerialRequirement(item);
            }

            document.getElementById('add-delivery-item').addEventListener('click', addItem);
            itemsContainer.addEventListener('change', (event) => {
                if (event.target.matches('.delivery-product')) {
                    updateSerialRequirement(event.target.closest('.delivery-item'));
                }
            });
            itemsContainer.addEventListener('click', (event) => {
                if (event.target.matches('.remove-delivery-item')) {
                    event.target.closest('.delivery-item').remove();
                }
            });
            updateSerialRequirement(itemsContainer.querySelector('.delivery-item'));
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
<?php endif; ?><div>
    <!-- Well begun is half done. - Aristotle -->
</div>
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/stock-in.blade.php ENDPATH**/ ?>