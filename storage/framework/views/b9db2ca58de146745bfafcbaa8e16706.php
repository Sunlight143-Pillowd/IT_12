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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Quotations')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="mb-6 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-xl font-bold text-gray-900">Build a Quotation</h3>

                    <div class="mb-4">
                        <label for="quote-category-filter" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Filter by category</label>
                        <select id="quote-category-filter" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                            <option value="all">All Categories</option>
                            <?php $__currentLoopData = $products->pluck('category')->filter()->unique()->sort()->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(strtolower($category)); ?>"><?php echo e($category); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <input id="quote-product-search" type="text" placeholder="Search products…" class="mb-4 w-full rounded border border-gray-300 px-3 py-2 text-sm">

                    <div id="quote-product-grid" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button"
                                    class="quote-product-btn rounded border border-gray-200 bg-gray-50 p-3 text-left transition hover:border-purple-600 hover:bg-purple-50"
                                    data-search="<?php echo e(strtolower($product->name . ' ' . $product->category)); ?>"
                                    data-category="<?php echo e(strtolower($product->category)); ?>"
                                    data-id="<?php echo e($product->id); ?>"
                                    data-name="<?php echo e($product->name); ?>"
                                    data-price="<?php echo e($product->price); ?>">
                                <p class="text-sm font-bold text-gray-900"><?php echo e($product->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($product->category); ?></p>
                                <p class="mt-2 text-sm font-bold text-purple-600">₱<?php echo e(number_format($product->price, 2)); ?></p>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-xl font-bold text-gray-900">Quotation Details</h3>
                    <form method="POST" action="<?php echo e(route('quotation.store')); ?>" id="quote-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="cart_json" id="quote-cart-json">
                        <input type="text" name="customer_name" placeholder="Customer name" class="mb-3 w-full rounded border border-gray-300 px-3 py-2 text-sm">
                        <input type="text" name="customer_contact" placeholder="Phone or email" class="mb-3 w-full rounded border border-gray-300 px-3 py-2 text-sm">
                        <textarea name="notes" rows="3" placeholder="Notes (optional)" class="mb-4 w-full rounded border border-gray-300 px-3 py-2 text-sm"></textarea>

                        <div id="quote-cart-items" class="mb-4 min-h-[120px] space-y-2 text-sm">
                            <p id="quote-cart-empty-msg" class="text-gray-400">No items yet.</p>
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span id="quote-cart-total">₱0.00</span>
                        </div>

                        <button type="submit" id="quote-submit" disabled class="mt-4 w-full rounded bg-purple-600 px-4 py-3 text-sm font-semibold text-white hover:bg-purple-700 disabled:cursor-not-allowed disabled:opacity-40">
                            Save &amp; Print Quotation
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-8 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-xl font-bold text-gray-900">Recent Quotations</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase tracking-[0.2em] text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Contact</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Items</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="border-t border-gray-200">
                                    <td class="px-4 py-3 font-semibold text-gray-900"><?php echo e($quotation->customer_name ?? 'Walk-in'); ?></td>
                                    <td class="px-4 py-3"><?php echo e($quotation->customer_contact ?? 'N/A'); ?></td>
                                    <td class="px-4 py-3 font-bold text-purple-600">₱<?php echo e(number_format($quotation->total_amount, 2)); ?></td>
                                    <td class="px-4 py-3"><?php echo e($quotation->items->count()); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">No quotations yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const cart = new Map();
            const cartItemsEl = document.getElementById('quote-cart-items');
            const emptyMsg = document.getElementById('quote-cart-empty-msg');
            const totalEl = document.getElementById('quote-cart-total');
            const cartJson = document.getElementById('quote-cart-json');
            const submitBtn = document.getElementById('quote-submit');
            const categoryFilter = document.getElementById('quote-category-filter');
            const productSearch = document.getElementById('quote-product-search');

            function applyProductFilters() {
                const categoryValue = categoryFilter ? categoryFilter.value : 'all';
                const searchValue = (productSearch ? productSearch.value : '').trim().toLowerCase();

                document.querySelectorAll('.quote-product-btn').forEach((btn) => {
                    const matchesCategory = categoryValue === 'all' || (btn.dataset.category || '') === categoryValue;
                    const matchesSearch = (btn.dataset.search || '').includes(searchValue);
                    btn.style.display = (matchesCategory && matchesSearch) ? '' : 'none';
                });
            }

            function money(value) {
                return '₱' + Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function render() {
                cartItemsEl.innerHTML = '';
                if (cart.size === 0) {
                    cartItemsEl.appendChild(emptyMsg);
                    totalEl.textContent = money(0);
                    cartJson.value = '';
                    submitBtn.disabled = true;
                    return;
                }

                let total = 0;

                cart.forEach((item, id) => {
                    const subtotal = item.price * item.qty;
                    total += subtotal;

                    const row = document.createElement('div');
                    row.className = 'flex items-center justify-between gap-2 rounded border border-gray-200 p-2';
                    row.innerHTML = `
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-gray-900">${item.name}</p>
                            <p class="text-xs text-gray-500">${money(item.price)} × ${item.qty} = ${money(subtotal)}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" data-id="${id}" data-delta="-1" class="qty-btn h-6 w-6 rounded border border-gray-300 text-sm">−</button>
                            <button type="button" data-id="${id}" data-delta="1" class="qty-btn h-6 w-6 rounded border border-gray-300 text-sm">+</button>
                            <button type="button" data-id="${id}" class="remove-btn ml-2 text-xs font-semibold text-red-500">Remove</button>
                        </div>
                    `;
                    cartItemsEl.appendChild(row);
                });

                totalEl.textContent = money(total);
                cartJson.value = JSON.stringify(Array.from(cart, ([id, item]) => ({
                    product_id: Number(id),
                    quantity: item.qty,
                })));
                submitBtn.disabled = false;
            }

            document.querySelectorAll('.quote-product-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const existing = cart.get(id);
                    if (existing) {
                        existing.qty += 1;
                    } else {
                        cart.set(id, {
                            name: btn.dataset.name,
                            price: Number(btn.dataset.price),
                            qty: 1,
                        });
                    }
                    render();
                });
            });

            cartItemsEl.addEventListener('click', (event) => {
                const qtyBtn = event.target.closest('.qty-btn');
                const removeBtn = event.target.closest('.remove-btn');

                if (qtyBtn) {
                    const id = qtyBtn.dataset.id;
                    const item = cart.get(id);
                    const nextQty = item.qty + Number(qtyBtn.dataset.delta);
                    if (nextQty <= 0) {
                        cart.delete(id);
                    } else {
                        item.qty = nextQty;
                    }
                    render();
                }

                if (removeBtn) {
                    cart.delete(removeBtn.dataset.id);
                    render();
                }
            });

            if (categoryFilter) {
                categoryFilter.addEventListener('change', applyProductFilters);
            }

            if (productSearch) {
                productSearch.addEventListener('input', applyProductFilters);
            }
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
<?php /**PATH C:\Users\Francisco Luis\Downloads\IT_12\resources\views/quotations.blade.php ENDPATH**/ ?>