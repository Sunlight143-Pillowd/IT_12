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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('POS Transactions')); ?>

            </h2>

            <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                <?php echo e(__('Back to Dashboard')); ?>

            </a>
        </div>
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
            <?php if($errors->any()): ?>
                <ul class="mb-6 list-disc rounded border border-red-200 bg-red-50 px-4 py-3 pl-8 text-sm text-red-700">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>

            <section aria-labelledby="pos-sale-heading" class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-4">
                    <h3 id="pos-sale-heading" class="text-xl font-bold text-gray-900">Process Sale</h3>
                    <p class="mt-1 text-sm text-gray-500">Search or scan a SKU, add products and quantities, then record payment.</p>
                </div>
                <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(20rem,1fr)]">
                    <div class="space-y-3">
                        <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_14rem]">
                            <label class="sr-only" for="pos-product-search">Search products or scan SKU</label>
                            <input id="pos-product-search" type="search" placeholder="Search products or scan SKU…" autocomplete="off" class="rounded border-gray-300 text-sm">
                            <label class="sr-only" for="pos-category-filter">Filter by category</label>
                            <select id="pos-category-filter" class="rounded border-gray-300 text-sm">
                                <option value="all">All categories</option>
                                <?php $__currentLoopData = $products->pluck('category')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(strtolower($category)); ?>"><?php echo e($category); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div id="pos-product-grid" class="grid max-h-[34rem] gap-3 overflow-y-auto sm:grid-cols-2">
                            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <button type="button"
                                    class="pos-product rounded-lg border border-gray-200 bg-gray-50 p-3 text-left transition hover:border-purple-600 hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    data-product-id="<?php echo e($product->id); ?>"
                                    data-name="<?php echo e($product->name); ?>"
                                    data-sku="<?php echo e($product->sku); ?>"
                                    data-price="<?php echo e($product->price); ?>"
                                    data-stock="<?php echo e($product->availableStock()); ?>"
                                    data-category="<?php echo e(strtolower($product->category)); ?>"
                                    data-search="<?php echo e(strtolower($product->name.' '.$product->sku.' '.$product->category)); ?>"
                                    data-requires-serial="<?php echo e($product->requires_serial ? '1' : '0'); ?>">
                                    <span class="block font-bold text-gray-900"><?php echo e($product->name); ?></span>
                                    <span class="mt-1 block font-mono text-xs text-gray-500">SKU <?php echo e($product->sku); ?></span>
                                    <span class="mt-2 flex justify-between gap-2 text-sm">
                                        <strong class="text-purple-700">₱<?php echo e(number_format($product->price, 2)); ?></strong>
                                        <span class="<?php echo e($product->availableStock() > 0 ? 'text-gray-600' : 'font-semibold text-red-700'); ?>">Stock <?php echo e($product->availableStock()); ?></span>
                                    </span>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="col-span-full rounded border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500">No active products are available.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <form id="pos-checkout-form" method="POST" action="<?php echo e(route('pos.checkout')); ?>" class="space-y-4 rounded-lg bg-gray-50 p-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="cart_json" id="pos-cart-json">
                        <div class="flex items-center justify-between gap-3">
                            <h4 class="font-bold text-gray-900">Sale Items</h4>
                            <button type="button" id="pos-clear-cart" class="text-xs font-semibold text-red-700 hover:underline">Clear cart</button>
                        </div>
                        <div id="pos-cart-items" class="max-h-72 min-h-20 space-y-3 overflow-y-auto text-sm">
                            <p id="pos-cart-empty" class="text-gray-400">No products selected.</p>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                            <span>Total</span><span id="pos-cart-total">₱0.00</span>
                        </div>
                        <div class="space-y-3 border-t border-gray-200 pt-3">
                            <label class="block text-xs font-bold uppercase text-gray-500">Customer (optional)
                                <select name="customer_id" id="pos-customer-select" class="mt-1 w-full rounded border-gray-300 text-sm font-normal normal-case">
                                    <option value="">Walk-in / not linked</option>
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($customer->id); ?>" data-name="<?php echo e($customer->name); ?>"><?php echo e($customer->name); ?><?php echo e($customer->phone ? ' · '.$customer->phone : ''); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </label>
                            <input name="customer_name" id="pos-customer-name" maxlength="150" placeholder="Customer name (optional)" class="w-full rounded border-gray-300 text-sm">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="block text-xs font-bold uppercase text-gray-500">Payment method
                                    <select name="payment_method" id="pos-payment-method" required class="mt-1 w-full rounded border-gray-300 text-sm font-normal normal-case">
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="bank_transfer">Bank transfer</option>
                                        <option value="other">Other</option>
                                    </select>
                                </label>
                                <label class="block text-xs font-bold uppercase text-gray-500">Amount received
                                    <input name="payment_amount" id="pos-payment-amount" type="number" min="0.01" step="0.01" required class="mt-1 w-full rounded border-gray-300 text-sm font-normal normal-case">
                                </label>
                            </div>
                            <input name="payment_reference" maxlength="150" placeholder="Payment reference (optional)" class="w-full rounded border-gray-300 text-sm">
                        </div>
                        <button type="submit" class="w-full rounded bg-purple-700 px-4 py-3 text-sm font-bold text-white hover:bg-purple-800">Complete sale & open receipt</button>
                    </form>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-3">
                <section aria-labelledby="order-list-heading" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 id="order-list-heading" class="text-xl font-bold text-gray-900">POS Transactions</h3>
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500"><?php echo e($recentSales->count() + $recentBuilds->count() + $recentStockIns->count()); ?> records</span>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-2" role="group" aria-label="POS record type">
                        <button type="button" class="history-tab rounded border border-purple-600 bg-purple-600 px-3 py-2 text-sm font-semibold text-white" data-history-tab="receipts" aria-pressed="true">
                            Receipts (<?php echo e($recentSales->count()); ?>)
                        </button>
                        <button type="button" class="history-tab rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700" data-history-tab="builds" aria-pressed="false">
                            Computer Build Orders (<?php echo e($recentBuilds->count()); ?>)
                        </button>
                        <button type="button" class="history-tab rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700" data-history-tab="invoices" aria-pressed="false">
                            Supplier Invoices (<?php echo e($recentStockIns->count()); ?>)
                        </button>
                    </div>

                    <label for="history-search" class="sr-only">Search receipts, builds, and supplier invoices</label>
                    <input id="history-search" type="search" placeholder="Search receipts, builds, invoices, suppliers, products..." autocomplete="off" class="mb-4 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-purple-600 focus:ring-purple-600">

                    <div id="receipt-list" class="history-panel grid gap-3 sm:grid-cols-2 xl:grid-cols-3" data-history-panel="receipts">
                        <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <button type="button"
                                    class="order-card rounded border border-gray-200 bg-gray-50 p-3 text-left transition hover:border-purple-600 hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    data-detail-template="receipt-detail-<?php echo e($sale->id); ?>"
                                    data-record-type="Receipt"
                                    aria-pressed="false">
                                <span class="block truncate text-sm font-bold text-gray-900">Receipt #<?php echo e($sale->id); ?></span>
                                <span class="mt-1 block truncate text-xs text-gray-600"><?php echo e($sale->customer_name ?: 'Customer not recorded'); ?></span>
                                <?php if($sale->store_order_id): ?>
                                    <span class="mt-1 block text-[11px] font-semibold text-purple-700">Customer order #<?php echo e($sale->store_order_id); ?></span>
                                <?php endif; ?>
                                <span class="mt-2 block text-sm font-bold text-purple-600">₱<?php echo e(number_format($sale->total_amount, 2)); ?></span>
                                <span class="mt-1 block text-[11px] text-gray-500">Includes 12% Philippine VAT</span>
                                <span class="mt-1 block text-[11px] text-gray-500"><?php echo e($sale->items->count()); ?> item(s) · <?php echo e($sale->created_at->format('M j, Y')); ?></span>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="col-span-full rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">No receipts yet.</p>
                        <?php endif; ?>
                    </div>

                    <div id="build-list" class="history-panel grid gap-3 sm:grid-cols-2 xl:grid-cols-3" data-history-panel="builds" hidden>
                        <?php $__empty_1 = true; $__currentLoopData = $recentBuilds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <button type="button"
                                    class="order-card rounded border border-gray-200 bg-gray-50 p-3 text-left transition hover:border-purple-600 hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    data-detail-template="build-detail-<?php echo e($build->id); ?>"
                                    data-record-type="<?php echo e($build->user_id !== null && $build->stock_deducted_at !== null ? 'Customer PC Build Receipt' : 'Computer Build'); ?>"
                                    aria-pressed="false">
                                <span class="block truncate text-sm font-bold text-gray-900"><?php echo e($build->build_number); ?></span>
                                <span class="mt-1 block truncate text-xs text-gray-600"><?php echo e($build->customer_name ?: 'Customer not recorded'); ?></span>
                                <span class="mt-2 block text-sm font-bold text-purple-600">₱<?php echo e(number_format($build->total_cost, 2)); ?></span>
                                <?php if($build->estimated_cost !== null): ?>
                                    <span class="mt-1 block text-[11px] text-gray-500">Estimate includes 12% Philippine VAT</span>
                                <?php else: ?>
                                    <span class="mt-1 block text-[11px] text-gray-500">Includes 12% Philippine VAT</span>
                                <?php endif; ?>
                                <span class="mt-1 block text-[11px] uppercase text-gray-500">
                                    <?php if($build->user_id !== null && $build->stock_deducted_at !== null): ?>
                                        Customer PC Build Receipt
                                    <?php else: ?>
                                        <?php echo e($build->status); ?>

                                    <?php endif; ?>
                                    · <?php echo e($build->items->count()); ?> part(s)
                                </span>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="col-span-full rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">No computer build orders yet.</p>
                        <?php endif; ?>
                    </div>

                    <div id="invoice-list" class="history-panel grid gap-3 sm:grid-cols-2 xl:grid-cols-3" data-history-panel="invoices" hidden>
                        <?php $__empty_1 = true; $__currentLoopData = $recentStockIns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stockIn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <button type="button"
                                    class="order-card rounded border border-gray-200 bg-gray-50 p-3 text-left transition hover:border-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    data-detail-template="invoice-detail-<?php echo e($stockIn->id); ?>"
                                    data-record-type="Supplier Invoice"
                                    aria-pressed="false">
                                <span class="block truncate text-sm font-bold text-gray-900">Invoice <?php echo e($stockIn->invoice_number); ?></span>
                                <span class="mt-1 block truncate text-xs text-gray-600"><?php echo e($stockIn->supplier_name); ?></span>
                                <span class="mt-2 block text-sm font-bold text-red-600">₱<?php echo e(number_format($stockIn->invoice_total, 2)); ?></span>
                                <span class="mt-1 block text-[11px] text-gray-500">Includes 12% Philippine VAT</span>
                                <span class="mt-1 block text-[11px] text-gray-500"><?php echo e($stockIn->items->count()); ?> item(s) · <?php echo e($stockIn->received_at->format('M j, Y')); ?></span>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="col-span-full rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">No supplier invoices yet.</p>
                        <?php endif; ?>
                    </div>
                    <p id="history-no-results" class="hidden rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">No matching records.</p>
                </section>

                <section aria-labelledby="selected-order-title" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 id="selected-order-title" class="mb-4 text-xl font-bold text-gray-900">Order Details</h3>
                    <div id="selected-order-details" aria-live="polite" class="min-h-30">
                        <p class="text-sm text-gray-400">Select a receipt or computer build to view its items.</p>
                    </div>
                </section>
            </div>

            <div class="hidden" aria-hidden="true">
                <?php $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <template id="receipt-detail-<?php echo e($sale->id); ?>">
                        <?php
                            $saleVat = \App\Support\PhilippineVat::breakdown((float) $sale->total_amount);
                        ?>
                        <div>
                            <div class="border-b border-gray-200 pb-3">
                                <p class="font-bold text-gray-900">Receipt #<?php echo e($sale->id); ?></p>
                                <?php if($sale->store_order_id): ?>
                                    <p class="mt-1 text-xs font-semibold text-purple-700">Customer order #<?php echo e($sale->store_order_id); ?></p>
                                <?php endif; ?>
                                <p class="mt-1 text-sm text-gray-600"><?php echo e($sale->customer_name ?: 'Customer not recorded'); ?></p>
                                <p class="mt-1 text-xs text-gray-500"><?php echo e($sale->created_at->format('M j, Y g:i A')); ?></p>
                            </div>
                            <div class="space-y-3 py-4">
                                <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-start justify-between gap-2 text-sm">
                                        <div class="min-w-0">
                                            <p class="wrap-break-word font-semibold text-gray-900"><?php echo e($item->product_name); ?></p>
                                            <p class="text-xs text-gray-500">₱<?php echo e(number_format($item->unit_price, 2)); ?> × <?php echo e($item->quantity); ?></p>
                                            <?php if($item->product?->description): ?>
                                                <p class="mt-1 wrap-break-word text-xs text-gray-600"><?php echo e($item->product->description); ?></p>
                                            <?php endif; ?>
                                            <?php $__currentLoopData = $item->units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <p class="mt-1 text-xs text-gray-500">Serial: <?php echo e($unit->serial_number ?? 'Not recorded'); ?> · Warranty: <?php echo e($unit->warranty_months !== null ? $unit->warranty_months.' months' : 'Not recorded'); ?></p>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <p class="shrink-0 font-semibold text-gray-900">₱<?php echo e(number_format($item->subtotal, 2)); ?></p>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php if($sale->shipping_fee > 0): ?>
                                <div class="flex items-center justify-between border-t border-gray-200 py-3 text-sm">
                                    <span class="font-semibold text-gray-700">Shipping</span>
                                    <span class="font-semibold text-gray-900">₱<?php echo e(number_format($sale->shipping_fee, 2)); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="space-y-2 border-t border-gray-200 py-3 text-sm">
                                <p class="text-xs text-gray-500">Prices include 12% Philippine VAT.</p>
                                <div class="flex items-center justify-between text-gray-700"><span>Amount before VAT</span><span>₱<?php echo e(number_format($saleVat['net'], 2)); ?></span></div>
                                <div class="flex items-center justify-between text-gray-700"><span>VAT (12%, included)</span><span>₱<?php echo e(number_format($saleVat['vat'], 2)); ?></span></div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                                <span>Total (VAT included)</span>
                                <span>₱<?php echo e(number_format($saleVat['total'], 2)); ?></span>
                            </div>
                            <a href="<?php echo e(route('pos.receipt', $sale)); ?>" class="mt-4 inline-flex rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600 hover:text-purple-700">Open printable receipt</a>
                        </div>
                    </template>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = $recentBuilds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <template id="build-detail-<?php echo e($build->id); ?>">
                        <?php
                            $buildAmount = $build->user_id !== null && $build->estimated_cost !== null
                                ? (float) $build->estimated_cost
                                : (float) $build->total_cost;
                            $buildVat = \App\Support\PhilippineVat::breakdown($buildAmount);
                        ?>
                        <div>
                            <div class="border-b border-gray-200 pb-3">
                                <p class="font-bold text-gray-900"><?php echo e($build->build_number); ?></p>
                                <p class="mt-1 text-sm text-gray-600"><?php echo e($build->customer_name ?: 'Customer not recorded'); ?></p>
                                <p class="mt-1 text-xs uppercase text-gray-500">
                                    <?php if($build->user_id !== null && $build->stock_deducted_at !== null): ?>
                                        Customer PC Build Receipt
                                    <?php else: ?>
                                        <?php echo e($build->status); ?>

                                    <?php endif; ?>
                                    · <?php echo e(($build->stock_deducted_at ?? $build->created_at)->format('M j, Y g:i A')); ?>

                                </p>
                            </div>
                            <div class="space-y-3 py-4">
                                <?php $__currentLoopData = $build->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-start justify-between gap-2 text-sm">
                                        <div class="min-w-0">
                                            <p class="wrap-break-word font-semibold text-gray-900"><?php echo e($item->product->name); ?></p>
                                            <p class="text-xs text-gray-500">₱<?php echo e(number_format($item->unit_price, 2)); ?> × <?php echo e($item->quantity); ?></p>
                                        </div>
                                        <p class="shrink-0 font-semibold text-gray-900">₱<?php echo e(number_format($item->subtotal, 2)); ?></p>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php if($build->notes): ?>
                                <div class="mb-3 border-t border-gray-200 pt-3">
                                    <p class="text-xs font-semibold uppercase text-gray-500">Notes</p>
                                        <p class="mt-1 wrap-break-word text-sm text-gray-700"><?php echo e($build->notes); ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="space-y-2 border-t border-gray-200 py-3 text-sm">
                                <p class="text-xs text-gray-500">Prices include 12% Philippine VAT.</p>
                                <div class="flex items-center justify-between text-gray-700"><span>Amount before VAT</span><span>₱<?php echo e(number_format($buildVat['net'], 2)); ?></span></div>
                                <div class="flex items-center justify-between text-gray-700"><span>VAT (12%, included)</span><span>₱<?php echo e(number_format($buildVat['vat'], 2)); ?></span></div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                                <span><?php echo e($build->user_id !== null ? 'Estimated total' : 'Total'); ?> (VAT included)</span>
                                <span>₱<?php echo e(number_format($buildVat['total'], 2)); ?></span>
                            </div>
                        </div>
                    </template>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = $recentStockIns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stockIn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <template id="invoice-detail-<?php echo e($stockIn->id); ?>">
                        <?php
                            $invoiceVat = \App\Support\PhilippineVat::breakdown((float) $stockIn->invoice_total);
                        ?>
                        <div>
                            <div class="border-b border-gray-200 pb-3">
                                <p class="font-bold text-gray-900">Supplier Invoice <?php echo e($stockIn->invoice_number); ?></p>
                                <p class="mt-1 text-sm text-gray-600"><?php echo e($stockIn->supplier_name); ?></p>
                                <p class="mt-1 text-xs text-gray-500"><?php echo e($stockIn->received_at->format('M j, Y g:i A')); ?></p>
                                <?php if($stockIn->supplier_contact): ?>
                                    <p class="mt-1 text-xs text-gray-500"><?php echo e($stockIn->supplier_contact); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="space-y-3 py-4">
                                <?php $__currentLoopData = $stockIn->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-start justify-between gap-2 text-sm">
                                        <div class="min-w-0">
                                            <p class="wrap-break-word font-semibold text-gray-900"><?php echo e($item->product->name); ?></p>
                                            <p class="text-xs text-gray-500">₱<?php echo e(number_format($item->unit_cost, 2)); ?> × <?php echo e($item->quantity); ?></p>
                                        </div>
                                        <p class="shrink-0 font-semibold text-gray-900">₱<?php echo e(number_format((float) $item->unit_cost * $item->quantity, 2)); ?></p>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php if($stockIn->delivery_document_path): ?>
                                <a class="mb-3 inline-block text-sm font-semibold text-purple-700 hover:underline" href="<?php echo e(asset('storage/'.$stockIn->delivery_document_path)); ?>" target="_blank" rel="noopener">Open supplier invoice document</a>
                            <?php endif; ?>
                            <div class="space-y-2 border-t border-gray-200 py-3 text-sm">
                                <p class="text-xs text-gray-500">Invoice prices include 12% Philippine VAT.</p>
                                <div class="flex items-center justify-between text-gray-700"><span>Amount before VAT</span><span>₱<?php echo e(number_format($invoiceVat['net'], 2)); ?></span></div>
                                <div class="flex items-center justify-between text-gray-700"><span>VAT (12%, included)</span><span>₱<?php echo e(number_format($invoiceVat['vat'], 2)); ?></span></div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                                <span>Invoice total (VAT included)</span>
                                <span>₱<?php echo e(number_format($invoiceVat['total'], 2)); ?></span>
                            </div>
                        </div>
                    </template>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <?php
            $receiptVat = \App\Support\PhilippineVat::breakdown((float) $receiptTotal);
            $invoiceVatSummary = \App\Support\PhilippineVat::breakdown((float) $invoiceTotal);
        ?>
        <section id="pos-analytics" aria-labelledby="pos-analytics-heading" class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h3 id="pos-analytics-heading" class="text-xl font-bold text-gray-900">Receipts & Supplier Invoice Analytics</h3>
                    <p class="mt-1 text-sm text-gray-500">Daily totals for the last 30 days, including today. Amounts include 12% Philippine VAT.</p>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Green = receipts · Red = stock invoices</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">
                <figure class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <figcaption class="mb-4 text-sm font-bold text-gray-800">Receipts vs. stock invoices</figcaption>
                    <div class="relative mx-auto aspect-square w-48 rounded-full"
                        <?php if($chartTotal > 0): ?>
                            style="background: conic-gradient(#16a34a 0% <?php echo e(number_format($receiptSharePercent, 2, '.', '')); ?>%, #dc2626 <?php echo e(number_format($receiptSharePercent, 2, '.', '')); ?>% 100%)"
                        <?php else: ?>
                            style="background: #e5e7eb"
                        <?php endif; ?>
                        role="img"
                        aria-label="Receipts total ₱<?php echo e(number_format($receiptTotal, 2)); ?> and supplier invoices total ₱<?php echo e(number_format($invoiceTotal, 2)); ?> for the last 30 days">
                        <div class="absolute inset-6 flex flex-col items-center justify-center rounded-full bg-white text-center">
                            <span class="text-xs font-semibold uppercase text-gray-500">Net receipts</span>
                            <span class="mt-1 text-lg font-black <?php echo e($netTotal < 0 ? 'text-red-700' : 'text-gray-900'); ?>">
                                <?php echo e($netTotal < 0 ? '-' : ''); ?>₱<?php echo e(number_format(abs($netTotal), 2)); ?>

                            </span>
                        </div>
                    </div>
                    <div class="mt-5 space-y-2 text-sm">
                        <p class="flex items-center justify-between gap-3">
                            <span class="flex items-center gap-2 text-gray-700"><span class="h-3 w-3 rounded-full bg-green-600"></span>Receipts + accepted builds</span>
                            <strong class="text-gray-900">₱<?php echo e(number_format($receiptTotal, 2)); ?></strong>
                        </p>
                        <p class="flex items-center justify-between gap-3 text-xs text-gray-500">
                            <span>Included VAT (12%)</span>
                            <strong>₱<?php echo e(number_format($receiptVat['vat'], 2)); ?></strong>
                        </p>
                        <p class="flex items-center justify-between gap-3">
                            <span class="flex items-center gap-2 text-gray-700"><span class="h-3 w-3 rounded-full bg-red-600"></span>Supplier invoices</span>
                            <strong class="text-gray-900">₱<?php echo e(number_format($invoiceTotal, 2)); ?></strong>
                        </p>
                        <p class="flex items-center justify-between gap-3 text-xs text-gray-500">
                            <span>Included VAT (12%)</span>
                            <strong>₱<?php echo e(number_format($invoiceVatSummary['vat'], 2)); ?></strong>
                        </p>
                        <p class="flex items-center justify-between gap-3 border-t border-gray-200 pt-2 font-semibold">
                            <span class="text-gray-700">Receipts less supplier invoices</span>
                            <strong class="<?php echo e($netTotal < 0 ? 'text-red-700' : 'text-green-700'); ?>">
                                <?php echo e($netTotal < 0 ? '-' : ''); ?>₱<?php echo e(number_format(abs($netTotal), 2)); ?>

                            </strong>
                        </p>
                    </div>
                </figure>

                <figure class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <figcaption class="mb-4 text-sm font-bold text-gray-800">Daily receipts and stock invoices</figcaption>
                    <svg id="pos-line-chart" class="h-auto w-full" viewBox="0 0 900 300" role="img" aria-label="Line graph of receipts in green and supplier invoice costs in red over the last 30 days">
                        <title>Daily receipts and supplier invoice costs</title>
                        <desc>Receipts include point-of-sale sales and accepted customer PC builds. Invoices use supplier delivery costs.</desc>
                    </svg>
                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-xs font-semibold text-gray-600">
                        <span class="flex items-center gap-2"><span class="h-0.5 w-5 bg-green-600"></span>Receipts + accepted customer PC builds</span>
                        <span class="flex items-center gap-2"><span class="h-0.5 w-5 bg-red-600"></span>Supplier stock invoices</span>
                    </div>
                </figure>
            </div>
        </section>
    </div>

    <script>
        (function () {
            const productGrid = document.getElementById('pos-product-grid');
            const productSearch = document.getElementById('pos-product-search');
            const categoryFilter = document.getElementById('pos-category-filter');
            const cartItems = document.getElementById('pos-cart-items');
            const cartTotal = document.getElementById('pos-cart-total');
            const cartJson = document.getElementById('pos-cart-json');
            const paymentMethod = document.getElementById('pos-payment-method');
            const paymentAmount = document.getElementById('pos-payment-amount');
            const customerSelect = document.getElementById('pos-customer-select');
            const customerName = document.getElementById('pos-customer-name');
            const cart = new Map();
            let total = 0;
            let paymentWasEdited = false;

            function renderCart() {
                cartItems.replaceChildren();
                total = [...cart.values()].reduce((sum, item) => sum + item.price * item.quantity, 0);
                cartTotal.textContent = `₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                const entries = [...cart.values()];

                if (entries.length === 0) {
                    const empty = document.createElement('p');
                    empty.className = 'text-gray-400';
                    empty.textContent = 'No products selected.';
                    cartItems.append(empty);
                }

                entries.forEach((item) => {
                    const row = document.createElement('div');
                    row.className = 'space-y-2 rounded border border-gray-200 bg-white p-3';
                    const heading = document.createElement('div');
                    heading.className = 'flex items-start justify-between gap-2';
                    const name = document.createElement('p');
                    name.className = 'font-semibold text-gray-900';
                    name.textContent = `${item.name} · ₱${item.price.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'text-xs font-semibold text-red-700';
                    remove.dataset.removeProduct = item.id;
                    remove.textContent = 'Remove';
                    heading.append(name, remove);
                    row.append(heading);

                    const quantity = document.createElement('label');
                    quantity.className = 'flex items-center gap-2 text-xs text-gray-600';
                    quantity.textContent = 'Quantity';
                    const quantityInput = document.createElement('input');
                    quantityInput.type = 'number';
                    quantityInput.min = '1';
                    quantityInput.max = String(item.stock);
                    quantityInput.value = String(item.quantity);
                    quantityInput.className = 'w-20 rounded border-gray-300 text-sm';
                    quantityInput.dataset.quantityProduct = item.id;
                    quantity.append(quantityInput);
                    row.append(quantity);

                    if (item.requiresSerial) {
                        const serials = document.createElement('textarea');
                        serials.rows = 2;
                        serials.required = true;
                        serials.maxLength = 100000;
                        serials.placeholder = 'Enter one serial number per unit';
                        serials.className = 'w-full rounded border-gray-300 text-xs';
                        serials.value = item.serialNumbers.join('\n');
                        serials.dataset.serialProduct = item.id;
                        row.append(serials);
                    }
                    cartItems.append(row);
                });

                if (paymentMethod.value === 'cash') {
                    if (!paymentWasEdited || paymentAmount.value === '') {
                        paymentAmount.value = total > 0 ? total.toFixed(2) : '';
                    }
                    paymentAmount.readOnly = false;
                } else {
                    paymentAmount.value = total > 0 ? total.toFixed(2) : '';
                    paymentAmount.readOnly = true;
                }
            }

            function filterProducts() {
                const term = productSearch.value.trim().toLowerCase();
                const category = categoryFilter.value;
                productGrid.querySelectorAll('.pos-product').forEach((button) => {
                    button.hidden = !(button.dataset.search.includes(term)
                        && (category === 'all' || button.dataset.category === category));
                });
            }

            productGrid.addEventListener('click', (event) => {
                const button = event.target.closest('.pos-product');
                if (!button) {
                    return;
                }
                const id = button.dataset.productId;
                const existing = cart.get(id);
                const stock = Number(button.dataset.stock);
                if (stock < 1 || (existing && existing.quantity >= stock)) {
                    return;
                }
                cart.set(id, existing ? { ...existing, quantity: existing.quantity + 1 } : {
                    id,
                    name: button.dataset.name,
                    price: Number(button.dataset.price),
                    stock,
                    quantity: 1,
                    requiresSerial: button.dataset.requiresSerial === '1',
                    serialNumbers: [],
                });
                renderCart();
            });
            productSearch.addEventListener('input', filterProducts);
            categoryFilter.addEventListener('change', filterProducts);
            cartItems.addEventListener('input', (event) => {
                const id = event.target.dataset.quantityProduct;
                if (id) {
                    const item = cart.get(id);
                    const quantity = Number(event.target.value);
                    if (item && quantity >= 1 && quantity <= item.stock) {
                        cart.set(id, { ...item, quantity });
                        renderCart();
                    }
                    return;
                }
                const serialId = event.target.dataset.serialProduct;
                if (serialId) {
                    const item = cart.get(serialId);
                    if (item) {
                        cart.set(serialId, { ...item, serialNumbers: event.target.value.split(/\r?\n/).map((value) => value.trim()).filter(Boolean) });
                    }
                }
            });
            cartItems.addEventListener('click', (event) => {
                const id = event.target.dataset.removeProduct;
                if (id) {
                    cart.delete(id);
                    renderCart();
                }
            });
            paymentAmount.addEventListener('input', () => {
                paymentWasEdited = true;
            });
            paymentMethod.addEventListener('change', renderCart);
            customerSelect.addEventListener('change', () => {
                const selectedName = customerSelect.selectedOptions[0]?.dataset.name ?? '';
                if (selectedName) {
                    customerName.value = selectedName;
                }
            });
            document.getElementById('pos-clear-cart').addEventListener('click', () => {
                cart.clear();
                paymentWasEdited = false;
                renderCart();
            });
            document.getElementById('pos-checkout-form').addEventListener('submit', (event) => {
                if (cart.size === 0) {
                    event.preventDefault();
                    return;
                }
                cartJson.value = JSON.stringify([...cart.values()].map((item) => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    serial_numbers: item.serialNumbers,
                })));
            });
            renderCart();

            const orderCards = document.querySelectorAll('.order-card');
            const orderDetails = document.getElementById('selected-order-details');
            const orderDetailsTitle = document.getElementById('selected-order-title');
            const historyTabs = document.querySelectorAll('.history-tab');
            const historySearch = document.getElementById('history-search');
            const noResultsMessage = document.getElementById('history-no-results');

            function filterHistory() {
                const activePanel = document.querySelector('.history-panel:not([hidden])');
                if (!activePanel) {
                    return;
                }

                const searchTerm = historySearch.value.trim().toLowerCase();
                const cards = Array.from(activePanel.querySelectorAll('.order-card'));
                let visibleCount = 0;

                cards.forEach((card) => {
                    const detailTemplate = document.getElementById(card.dataset.detailTemplate);
                    const searchableText = `${card.textContent} ${detailTemplate?.content.textContent || ''}`.toLowerCase();
                    const matches = searchTerm === '' || searchableText.includes(searchTerm);
                    card.hidden = !matches;
                    if (matches) {
                        visibleCount += 1;
                    }
                });

                noResultsMessage.classList.toggle('hidden', cards.length === 0 || visibleCount > 0);

                const selectedCard = cards.find((card) => card.getAttribute('aria-pressed') === 'true');
                if (!selectedCard || selectedCard.hidden) {
                    const nextCard = cards.find((card) => !card.hidden);
                    if (nextCard) {
                        selectOrder(nextCard);
                    } else if (cards.length > 0) {
                        cards.forEach((card) => card.setAttribute('aria-pressed', 'false'));
                        orderDetailsTitle.textContent = 'No matching order';
                        orderDetails.replaceChildren();
                        const emptyMessage = document.createElement('p');
                        emptyMessage.className = 'text-sm text-gray-400';
                        emptyMessage.textContent = 'Try a different search.';
                        orderDetails.appendChild(emptyMessage);
                    }
                }
            }

            function selectOrder(card) {
                orderCards.forEach((orderCard) => {
                    const selected = orderCard === card;
                    orderCard.setAttribute('aria-pressed', String(selected));
                    orderCard.classList.toggle('border-purple-600', selected);
                    orderCard.classList.toggle('bg-purple-50', selected);
                });

                const detailTemplate = document.getElementById(card.dataset.detailTemplate);
                if (detailTemplate) {
                    orderDetails.replaceChildren(detailTemplate.content.cloneNode(true));
                    orderDetailsTitle.textContent = `${card.dataset.recordType} Details`;
                }
            }

            orderCards.forEach((card) => {
                card.addEventListener('click', () => selectOrder(card));
            });

            historyTabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const selectedPanel = tab.dataset.historyTab;

                    historyTabs.forEach((historyTab) => {
                        const selected = historyTab === tab;
                        historyTab.setAttribute('aria-pressed', String(selected));
                        historyTab.classList.toggle('border-purple-600', selected);
                        historyTab.classList.toggle('bg-purple-600', selected);
                        historyTab.classList.toggle('text-white', selected);
                        historyTab.classList.toggle('border-gray-300', !selected);
                        historyTab.classList.toggle('bg-white', !selected);
                        historyTab.classList.toggle('text-gray-700', !selected);
                    });

                    document.querySelectorAll('.history-panel').forEach((panel) => {
                        const hidden = panel.dataset.historyPanel !== selectedPanel;
                        panel.hidden = hidden;
                    });

                    filterHistory();
                    const selectedCard = document.querySelector(`[data-history-panel="${selectedPanel}"] .order-card:not([hidden])`);
                    if (selectedCard) {
                        selectOrder(selectedCard);
                        return;
                    }

                    const panelHasCards = document.querySelector(`[data-history-panel="${selectedPanel}"] .order-card`);
                    if (panelHasCards) {
                        return;
                    }

                    orderCards.forEach((orderCard) => orderCard.setAttribute('aria-pressed', 'false'));
                    const panelTitles = {
                        receipts: 'Receipt Details',
                        builds: 'Computer Build Details',
                        invoices: 'Supplier Invoice Details',
                    };
                    orderDetailsTitle.textContent = panelTitles[selectedPanel] || 'POS Details';
                    orderDetails.replaceChildren();

                    const emptyMessage = document.createElement('p');
                    emptyMessage.className = 'text-sm text-gray-400';
                    const emptyMessages = {
                        receipts: 'No receipts yet.',
                        builds: 'No computer build orders yet.',
                        invoices: 'No supplier invoices yet.',
                    };
                    emptyMessage.textContent = emptyMessages[selectedPanel] || 'No records yet.';
                    orderDetails.appendChild(emptyMessage);
                });
            });

            historySearch.addEventListener('input', filterHistory);

            const initialTab = ['receipts', 'builds', 'invoices'].find((tab) =>
                document.querySelector(`[data-history-panel="${tab}"] .order-card`)
            );
            document.querySelector(`[data-history-tab="${initialTab || 'receipts'}"]`)?.click();
        })();
    </script>

    <script>
        (() => {
            const chartData = <?php echo e(\Illuminate\Support\Js::from($posChartData)); ?>;
            const svg = document.getElementById('pos-line-chart');
            const svgNamespace = 'http://www.w3.org/2000/svg';
            const chart = { left: 64, right: 880, top: 16, bottom: 248 };
            const maximum = Math.max(1, ...chartData.flatMap((point) => [point.receipts, point.invoices]));
            const xPosition = (index) => chart.left + (chart.right - chart.left) * index / Math.max(chartData.length - 1, 1);
            const yPosition = (value) => chart.bottom - (chart.bottom - chart.top) * value / maximum;

            function addElement(name, attributes, text = null) {
                const element = document.createElementNS(svgNamespace, name);
                Object.entries(attributes).forEach(([attribute, value]) => element.setAttribute(attribute, value));
                if (text !== null) {
                    element.textContent = text;
                }
                svg.appendChild(element);
                return element;
            }

            for (let tick = 0; tick <= 4; tick += 1) {
                const value = maximum * tick / 4;
                const y = yPosition(value);
                addElement('line', { x1: chart.left, y1: y, x2: chart.right, y2: y, stroke: '#e5e7eb', 'stroke-width': 1 });
                addElement('text', { x: chart.left - 8, y: y + 4, 'text-anchor': 'end', fill: '#6b7280', 'font-size': 11 }, `₱${Math.round(value).toLocaleString()}`);
            }

            [
                { key: 'receipts', color: '#16a34a' },
                { key: 'invoices', color: '#dc2626' },
            ].forEach((series) => {
                const points = chartData.map((point, index) => `${xPosition(index)},${yPosition(point[series.key])}`).join(' ');
                addElement('polyline', {
                    points,
                    fill: 'none',
                    stroke: series.color,
                    'stroke-width': 3,
                    'stroke-linecap': 'round',
                    'stroke-linejoin': 'round',
                });
            });

            chartData.forEach((point, index) => {
                if (index % 5 === 0 || index === chartData.length - 1) {
                    addElement('text', {
                        x: xPosition(index),
                        y: 274,
                        'text-anchor': index === chartData.length - 1 ? 'end' : 'middle',
                        fill: '#6b7280',
                        'font-size': 11,
                    }, point.label);
                }
            });
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
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views\pos.blade.php ENDPATH**/ ?>