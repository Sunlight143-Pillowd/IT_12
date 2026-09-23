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
            <?php echo e(__('Point of Sale')); ?>

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
                    <h3 class="mb-4 text-xl font-bold text-gray-900">Products</h3>

                    <div class="mb-4">
                        <label for="product-category-filter" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Filter by category</label>
                        <select id="product-category-filter" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                            <option value="all">All Categories</option>
                            <?php $__currentLoopData = $products->pluck('category')->filter()->unique()->sort()->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(strtolower($category)); ?>"><?php echo e($category); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <input id="product-search" type="text" placeholder="Search products…" class="mb-4 w-full rounded border border-gray-300 px-3 py-2 text-sm">

                    <div id="product-grid" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button"
                                    class="pos-product-btn rounded border border-gray-200 bg-gray-50 p-3 text-left transition hover:border-purple-600 hover:bg-purple-50"
                                    data-search="<?php echo e(strtolower($product->name . ' ' . $product->category)); ?>"
                                    data-category="<?php echo e(strtolower($product->category)); ?>"
                                    data-id="<?php echo e($product->id); ?>"
                                    data-name="<?php echo e($product->name); ?>"
                                    data-price="<?php echo e($product->price); ?>"
                                    data-stock="<?php echo e($product->stock_quantity); ?>">
                                <p class="text-sm font-bold text-gray-900"><?php echo e($product->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($product->category); ?></p>
                                <p class="mt-2 text-sm font-bold text-purple-600">₱<?php echo e(number_format($product->price, 2)); ?></p>
                                <p class="text-[11px] text-gray-400"><?php echo e($product->stock_quantity); ?> in stock</p>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-xl font-bold text-gray-900">Cart</h3>
                    <form method="POST" action="<?php echo e(route('pos.checkout')); ?>" id="pos-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="cart_json" id="cart-json-input">
                        <input type="text" name="customer_name" placeholder="Customer name (optional)" class="mb-4 w-full rounded border border-gray-300 px-3 py-2 text-sm">

                        <div id="cart-items" class="mb-4 min-h-[120px] space-y-2 text-sm">
                            <p id="cart-empty-msg" class="text-gray-400">No items yet.</p>
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span id="cart-total">₱0.00</span>
                        </div>

                        <button type="submit" id="checkout-btn" disabled class="mt-4 w-full rounded bg-purple-600 px-4 py-3 text-sm font-semibold text-white hover:bg-purple-700 disabled:cursor-not-allowed disabled:opacity-40">
                            Complete Sale
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const cart = new Map();
            const cartItemsEl = document.getElementById('cart-items');
            const cartEmptyMsg = document.getElementById('cart-empty-msg');
            const cartTotalEl = document.getElementById('cart-total');
            const cartJsonInput = document.getElementById('cart-json-input');
            const checkoutBtn = document.getElementById('checkout-btn');
            const categoryFilter = document.getElementById('product-category-filter');
            const productSearch = document.getElementById('product-search');

            function applyProductFilters() {
                const categoryValue = categoryFilter ? categoryFilter.value : 'all';
                const searchValue = (productSearch ? productSearch.value : '').trim().toLowerCase();

                document.querySelectorAll('.pos-product-btn').forEach((btn) => {
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
                    cartItemsEl.appendChild(cartEmptyMsg);
                    cartTotalEl.textContent = money(0);
                    cartJsonInput.value = '';
                    checkoutBtn.disabled = true;
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

                cartTotalEl.textContent = money(total);
                cartJsonInput.value = JSON.stringify(Array.from(cart, ([id, item]) => ({
                    product_id: Number(id),
                    quantity: item.qty,
                })));
                checkoutBtn.disabled = false;
            }

            document.querySelectorAll('.pos-product-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const stock = Number(btn.dataset.stock);
                    const existing = cart.get(id);

                    if (existing) {
                        if (existing.qty < stock) {
                            existing.qty += 1;
                        }
                    } else {
                        cart.set(id, {
                            name: btn.dataset.name,
                            price: Number(btn.dataset.price),
                            qty: 1,
                            stock,
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
                    } else if (nextQty <= item.stock) {
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
<?php /**PATH C:\Users\Francisco Luis\Downloads\IT_12\resources\views/pos.blade.php ENDPATH**/ ?>