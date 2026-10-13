<x-app-layout>
    @php
        $todaySalesVat = \App\Support\PhilippineVat::breakdown((float) $todaySales);
        $revenueVat = \App\Support\PhilippineVat::breakdown((float) $revenue);
    @endphp

    @if($isStaff)
        <x-slot name="header">
            <div class="flex items-center justify-between gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Admin Dashboard') }}
                </h2>

                <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                    Go back to Main Dashboard
                </a>
            </div>
        </x-slot>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                @if (session('status'))
                    <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
                @endif
                <section class="relative overflow-hidden rounded-2xl bg-black text-white shadow-xl">
                    <div class="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_top,rgba(168,85,247,0.55),transparent_55%)]"></div>
                    <div class="relative p-8 lg:p-10">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm sm:p-5">
                                <div class="mb-3 flex items-center justify-between lg:mb-4">
                                    <span class="text-sm font-bold uppercase tracking-wide text-purple-300">Today</span>
                                    <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-[10px] font-bold uppercase text-emerald-300">Live</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3">
                                    <div class="flex min-w-0 flex-col justify-center gap-2 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-3 transition-colors hover:border-purple-300/30 lg:gap-3 lg:p-4">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Sales Today</p>
                                        <p class="break-words text-xl font-black leading-tight tracking-tight tabular-nums text-white sm:text-2xl">₱{{ number_format($todaySales, 0) }}</p>
                                        <p class="text-[10px] text-gray-400">Includes ₱{{ number_format($todaySalesVat['vat'], 2) }} VAT (12%)</p>
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-center gap-2 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-3 transition-colors hover:border-purple-300/30 lg:gap-3 lg:p-4">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Pending Orders Today</p>
                                        <p class="break-words text-xl font-black leading-tight tracking-tight tabular-nums text-white sm:text-2xl">{{ $todayOrders }}</p>
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-center gap-2 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-3 transition-colors hover:border-red-300/30 lg:gap-3 lg:p-4">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Low Stock</p>
                                        <p class="break-words text-xl font-black leading-tight tracking-tight tabular-nums text-red-400 sm:text-2xl">{{ $lowStock }}</p>
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-center gap-2 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-3 transition-colors hover:border-purple-300/30 lg:gap-3 lg:p-4">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Total Revenue</p>
                                        <p class="break-words text-xl font-black leading-tight tracking-tight tabular-nums text-white sm:text-2xl">₱{{ number_format($revenue, 0) }}</p>
                                        <p class="text-[10px] text-gray-400">Includes ₱{{ number_format($revenueVat['vat'], 2) }} VAT (12%)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex min-w-0 flex-col rounded-xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                    <h1 class="text-xl font-black text-white">Customer Orders</h1>
                                    <span class="rounded-full bg-purple-500/20 px-3 py-1 text-xs font-bold text-purple-200">{{ $totalOrders }} order(s)</span>
                                </div>
                                <div class="max-h-[22rem] overflow-y-auto rounded-lg border border-white/10">
                                    <table class="w-full table-fixed text-left text-xs text-gray-200 sm:text-sm">
                                        <thead class="sticky top-0 z-10 bg-gray-900 text-[10px] uppercase text-gray-300 sm:text-xs">
                                            <tr><th class="w-[8%] px-2 py-3 sm:px-4">Order</th><th class="w-[16%] px-2 py-3 sm:px-4">Customer</th><th class="w-[16%] px-2 py-3 sm:px-4">Items</th><th class="w-[10%] px-2 py-3 sm:px-4">Date</th><th class="w-[10%] px-2 py-3 text-right sm:px-4">Total</th><th class="w-[17%] px-2 py-3 sm:px-4">Payment / Fulfillment</th><th class="w-[23%] px-2 py-3 sm:px-4">Status / Action</th></tr>
                                        </thead>
                                        <tbody class="break-words">
                                            @forelse($storeOrders as $order)
                                                @php
                                                    $orderVat = \App\Support\PhilippineVat::breakdown((float) $order->total_amount);
                                                @endphp
                                                <tr class="border-t border-white/10">
                                                    <td class="px-4 py-3 font-semibold text-white">#{{ $order->id }}</td>
                                                    <td class="px-4 py-3">{{ $order->customer_name }}<br><span class="text-xs text-gray-400">{{ $order->customer_email }}</span>@if ($order->customer_phone)<br><span class="text-xs text-gray-400">{{ $order->customer_phone }}</span>@endif</td>
                                                    <td class="px-4 py-3">
                                                        <ul class="space-y-1">
                                                            @foreach ($order->items as $item)
                                                                <li>{{ $item->product_name }} × {{ $item->quantity }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-300">{{ $order->created_at->format('M j, Y') }}</td>
                                                    <td class="px-4 py-3 text-right font-semibold text-white">
                                                        @if ($order->shipping_zone === 'outside_davao' && $order->shipping_fee === null)
                                                            Subtotal<br>₱{{ number_format($order->total_amount, 2) }}
                                                        @else
                                                            ₱{{ number_format($order->total_amount, 2) }}
                                                        @endif
                                                        <span class="block text-[10px] font-normal text-gray-400">Includes ₱{{ number_format($orderVat['vat'], 2) }} VAT (12%)</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <p class="font-semibold">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</p>
                                                        <p class="text-xs text-gray-400">{{ ucfirst($order->fulfillment_method) }}</p>
                                                        @if ($order->shipping_zone === 'outside_davao')
                                                            <p class="mt-1 text-xs text-gray-400">
                                                                Shipping: {{ $order->shipping_fee === null ? 'Fee needs confirmation' : '₱'.number_format($order->shipping_fee, 2) }}
                                                            </p>
                                                        @elseif ($order->shipping_fee !== null && $order->shipping_fee > 0)
                                                            <p class="mt-1 text-xs text-gray-400">Shipping: ₱{{ number_format($order->shipping_fee, 2) }}</p>
                                                        @endif
                                                        @if ($order->shipping_zone === 'davao_city')
                                                            <p class="mt-1 text-xs text-gray-400">Distance: {{ number_format($order->shipping_distance_km, 1) }} km</p>
                                                        @endif
                                                        @if ($order->delivery_address)<p class="mt-1 max-w-48 text-xs text-gray-400">{{ $order->fulfillment_method === 'pickup' ? 'Pickup at: ' : '' }}{{ $order->delivery_address }}</p>@endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex flex-col items-start gap-2">
                                                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold text-black {{ $order->status === 'accepted' ? 'bg-emerald-100' : 'bg-amber-100' }}">{{ ucfirst($order->status) }}</span>
                                                            @if ($order->status === 'pending' && $order->shipping_zone === 'outside_davao' && $order->shipping_fee === null && Auth::user()?->canManageOrders())
                                                                <form method="POST" action="{{ route('dashboard.orders.shipping-fee', $order) }}" class="flex flex-wrap items-end gap-2">
                                                                    @csrf
                                                                    <label class="text-xs text-gray-300">
                                                                        Shipping fee
                                                                        <input type="number" name="shipping_fee" min="0" max="100000" step="0.01" value="{{ $order->shipping_fee }}" required
                                                                               class="mt-1 block w-28 rounded-md border-gray-300 text-sm text-gray-900 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                                                    </label>
                                                                    <button type="submit" class="rounded-lg bg-purple-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-purple-800">{{ $order->status === 'accepted' ? 'Update fee' : 'Confirm fee' }}</button>
                                                                </form>
                                                            @elseif ($order->status === 'pending' && Auth::user()?->canManageOrders())
                                                                <form method="POST" action="{{ route('dashboard.orders.accept', $order) }}" class="inline-block" onsubmit="const button = this.querySelector('button[type=submit]'); button.disabled = true; button.textContent = 'Accepting...';">
                                                                    @csrf
                                                                    <button type="submit" class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-800">Accept</button>
                                                                </form>
                                                            @elseif ($order->status === 'accepted')
                                                                <div class="flex flex-wrap items-center gap-2">
                                                                    <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-black" aria-disabled="true">Order accepted</span>
                                                                    @if ($order->sale)
                                                                        <a href="{{ route('pos.receipt', $order->sale) }}" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:border-purple-600 hover:text-purple-700">View receipt</a>
                                                                    @endif
                                                                    @if ($order->shipping_zone === 'outside_davao' && Auth::user()?->canManageOrders())
                                                                        <form method="POST" action="{{ route('dashboard.orders.shipping-fee', $order) }}" class="flex flex-wrap items-end gap-2">
                                                                            @csrf
                                                                            <label class="text-xs text-gray-300">
                                                                                Shipping fee
                                                                                <input type="number" name="shipping_fee" min="0" max="100000" step="0.01" value="{{ $order->shipping_fee }}" required
                                                                                       class="mt-1 block w-28 rounded-md border-gray-300 text-sm text-gray-900 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                                                            </label>
                                                                            <button type="submit" class="rounded-lg bg-purple-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-purple-800">Update fee</button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                                <details class="mt-2 w-full max-w-56 rounded-lg border border-white/20 p-2">
                                                                    <summary class="cursor-pointer text-xs font-bold text-white">Edit accepted order</summary>
                                                                    <form method="POST" action="{{ route('dashboard.orders.update', $order) }}" class="mt-3 space-y-2">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <label class="block text-xs text-gray-300">Customer name
                                                                            <input type="text" name="customer_name" value="{{ $order->customer_name }}" maxlength="255" required class="mt-1 block w-full rounded-md border-gray-300 text-sm text-gray-900">
                                                                        </label>
                                                                        <label class="block text-xs text-gray-300">Email (optional)
                                                                            <input type="email" name="customer_email" value="{{ $order->customer_email }}" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 text-sm text-gray-900">
                                                                        </label>
                                                                        <label class="block text-xs text-gray-300">Phone
                                                                            <input type="tel" name="customer_phone" value="{{ $order->customer_phone }}" maxlength="40" class="mt-1 block w-full rounded-md border-gray-300 text-sm text-gray-900">
                                                                        </label>
                                                                        <label class="block text-xs text-gray-300">Delivery / pickup address
                                                                            <textarea name="delivery_address" rows="2" maxlength="2000" class="mt-1 block w-full rounded-md border-gray-300 text-sm text-gray-900">{{ $order->delivery_address }}</textarea>
                                                                        </label>
                                                                        <button type="submit" class="rounded-lg bg-purple-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-purple-800">Save details</button>
                                                                    </form>
                                                                </details>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No customer orders yet.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('inventory.index') }}" class="inline-block rounded-lg bg-purple-600 px-5 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-purple-700">VIEW INVENTORY</a>
                            <a href="{{ route('pos.index') }}" class="inline-block rounded-lg border border-white/30 bg-white/5 px-5 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-white/10">OPEN POS</a>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h2 class="text-xl font-black text-gray-900">Quick Actions</h2>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <a href="{{ route('inventory.index') }}" class="rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Inventory</p>
                                <p class="text-sm text-gray-500">Check stock levels</p>
                            </a>
                            <a href="{{ route('pos.index') }}" class="rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">POS</p>
                                <p class="text-sm text-gray-500">Open cashier</p>
                            </a>
                            <a href="{{ route('quotation.index') }}" class="rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Quotations</p>
                                <p class="text-sm text-gray-500">Create customer quotes</p>
                            </a>
                            <a href="{{ route('buildpc.index') }}" class="rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Build PC</p>
                                <p class="text-sm text-gray-500">Create custom desktop builds</p>
                            </a>
                        </div>
                </section>

                <section aria-label="Store summary" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    @foreach ([
                        ['Total Products', $products, 'text-purple-700'],
                        ['Total Orders', $totalOrders, 'text-gray-900'],
                        ['Pending Orders', $pendingOrders, 'text-amber-700'],
                        ['Accepted Orders', $acceptedOrders, 'text-emerald-700'],
                        ['Low-stock Products', $lowStock, 'text-red-700'],
                    ] as [$label, $count, $color])
                        <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $label }}</p>
                            <p class="mt-2 text-3xl font-black tabular-nums {{ $color }}">{{ $count }}</p>
                        </article>
                    @endforeach
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-black text-gray-900">Recent customer build requests</h2>
                            <p class="text-sm text-gray-500">{{ $pendingBuildRequests }} pending request(s)</p>
                        </div>
                        <a href="{{ route('buildpc.index') }}" class="text-sm font-semibold text-purple-700 hover:underline">Manage requests</a>
                    </div>
                    @forelse ($recentCustomerBuilds as $build)
                        <article class="flex flex-col gap-2 border-t border-gray-100 py-3 first:border-0 first:pt-0 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900">{{ $build->build_number }} · {{ $build->customer_name }}</p>
                                <p class="text-xs text-gray-500">{{ $build->user?->email }} · {{ $build->created_at->format('M j, Y') }}</p>
                                @if ($build->notes)
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $build->notes }}</p>
                                @endif
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-bold uppercase {{ $build->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">{{ ucfirst($build->status) }}</span>
                        </article>
                    @empty
                        <p class="border-t border-gray-100 pt-3 text-sm text-gray-500">No customer build requests yet.</p>
                    @endforelse
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 p-5">
                        <h2 class="text-xl font-black text-gray-900">Recent Sales</h2>
                        <a href="{{ route('pos.index') }}" class="text-sm font-semibold text-purple-700 hover:underline">All receipts</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Receipt</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Date</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3">Document</th></tr></thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    @php
                                        $saleVat = \App\Support\PhilippineVat::breakdown((float) $sale->total_amount);
                                    @endphp
                                    <tr class="border-t border-gray-200"><td class="px-4 py-3 font-semibold text-gray-900">#{{ $sale->id }}</td><td class="px-4 py-3">{{ $sale->customer_name ?: 'Customer not recorded' }}</td><td class="px-4 py-3">{{ $sale->items_count }}@foreach ($sale->items as $item)<span class="block text-xs text-gray-500">{{ $item->product_name }}</span>@endforeach</td><td class="px-4 py-3">{{ $sale->created_at->format('M j, Y') }}</td><td class="px-4 py-3 text-right font-semibold">₱{{ number_format($saleVat['total'], 2) }}<span class="block text-[10px] font-normal text-gray-500">VAT included · ₱{{ number_format($saleVat['vat'], 2) }}</span></td><td class="px-4 py-3"><a href="{{ route('pos.receipt', $sale) }}" class="rounded border border-gray-300 px-3 py-1.5 text-xs font-semibold hover:border-purple-600">View receipt</a></td></tr>
                                @empty
                                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No sales recorded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 p-5">
                        <h2 class="text-xl font-black text-gray-900">Stock-In Transactions</h2>
                        <a href="{{ route('stock-in.index') }}" class="text-sm font-semibold text-purple-700 hover:underline">All deliveries</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Supplier</th><th class="px-4 py-3">Invoice Number</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Document</th></tr></thead>
                            <tbody>
                                @forelse($recentStockIns as $stockIn)
                                    <tr class="border-t border-gray-200"><td class="px-4 py-3">{{ $stockIn->received_at->format('M j, Y') }}</td><td class="px-4 py-3 font-semibold">{{ $stockIn->supplier_name }}</td><td class="px-4 py-3">{{ $stockIn->invoice_number }}</td><td class="px-4 py-3">{{ $stockIn->items_count }}</td><td class="px-4 py-3">@if ($stockIn->delivery_document_path)<a href="{{ asset('storage/'.$stockIn->delivery_document_path) }}" target="_blank" rel="noopener" class="font-semibold text-purple-700 hover:underline">View document</a>@else<span class="text-gray-400">None</span>@endif</td></tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No delivery transactions recorded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    @else
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                @if (session('status'))
                    <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
                @endif
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-gray-500">Welcome back</p>
                        <h1 class="mt-2 text-3xl font-black text-gray-900">{{ Auth::user()->name }}</h1>
                    </div>
                    <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                        Back to Main Dashboard
                    </a>
                </div>

@php
    $allOrders = $customerStoreOrders->concat($purchaseHistory)->sortByDesc('created_at');
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-gray-900">My Store Orders</h2>
                <p class="mt-1 text-sm font-medium text-gray-500">Purchase History</p>
            </div>
            <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-purple-700">
                {{ $allOrders->count() }} order(s)
            </span>
        </div>

        @if ($allOrders->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center">
                <p class="text-lg font-semibold text-gray-700">No purchases yet.</p>
                <p class="mt-2 text-sm text-gray-500">Your recent orders and product purchases will appear here.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($allOrders as $order)
                    @php
                        $orderVat = \App\Support\PhilippineVat::breakdown((float) $order->total_amount);
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                        <div class="flex flex-col gap-3 border-b border-gray-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">
                                    {{ isset($order->status) ? 'Order' : 'Receipt' }} #{{ $order->id }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600">{{ $order->created_at->format('F d, Y h:i A') }}</p>
                            </div>
                            <div class="text-left sm:text-right">
                                @if (isset($order->status))
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase text-black {{ $order->status === 'accepted' ? 'bg-emerald-100' : 'bg-amber-100' }}">{{ ucfirst($order->status) }}</span>
                                    <p class="mt-2 text-xs text-gray-500">Payment: {{ ucwords(str_replace('_', ' ', $order->payment_method)) }} · {{ ucfirst($order->fulfillment_method) }}</p>
                                    @if ($order->delivery_address)<p class="mt-1 text-xs text-gray-500">{{ $order->fulfillment_method === 'pickup' ? 'Pickup at: ' : 'Delivery to: ' }}{{ $order->delivery_address }}</p>@endif
                                    @if ($order->shipping_zone === 'outside_davao' && $order->shipping_fee === null)
                                        <p class="mt-1 text-xs text-amber-700">Shipping fee will be confirmed by staff before acceptance.</p>
                                    @elseif ($order->shipping_fee !== null && $order->shipping_fee > 0)
                                        <p class="mt-1 text-xs text-gray-500">Shipping: ₱{{ number_format($order->shipping_fee, 2) }}</p>
                                    @endif
                                    @if ($order->sale)
                                        <a href="{{ route('pos.receipt', $order->sale) }}" class="mt-2 inline-flex text-xs font-semibold text-purple-700 hover:underline">View receipt #{{ $order->sale->id }}</a>
                                    @endif
                                @endif
                                <p class="mt-1 text-xl font-black text-gray-900">
                                    @if (isset($order->status) && $order->shipping_zone === 'outside_davao' && $order->shipping_fee === null)
                                        Subtotal ₱{{ number_format($order->total_amount, 2) }}
                                    @else
                                        ₱{{ number_format($order->total_amount, 2) }}
                                    @endif
                                </p>
                                <p class="mt-1 text-xs text-gray-500">Includes ₱{{ number_format($orderVat['vat'], 2) }} Philippine VAT (12%).</p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between gap-4 border-b border-gray-200 pb-2 last:border-0 last:pb-0">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $item->product_name }} × {{ $item->quantity }}</p>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700">₱{{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900">My PC Builds</h2>
            <p class="mt-1 text-sm text-gray-500">Follow each custom build from review through completion.</p>
        </div>
        <a href="{{ route('buildpc.customer') }}" class="rounded-lg bg-purple-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-purple-800">Build a PC</a>
    </div>
    <div class="space-y-4 p-6">
        @forelse ($customerBuilds as $build)
            <article class="rounded-xl border border-gray-200 p-4 sm:p-5">
                <div class="flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Build ID · {{ $build->build_number }}</p>
                        <h3 class="mt-1 text-lg font-bold text-gray-900">{{ $build->customer_name }}</h3>
                        <p class="text-sm text-gray-500">Build date: {{ $build->created_at->format('M j, Y') }}</p>
                    </div>
                    <span class="w-fit rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ in_array($build->status, ['ready', 'completed'], true) ? 'bg-emerald-100 text-emerald-800' : ($build->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ ucfirst($build->status) }}</span>
                </div>
                <div class="mt-4 grid gap-5 lg:grid-cols-[minmax(0,1fr)_18rem]">
                    <div class="grid gap-x-6 gap-y-2 sm:grid-cols-2">
                        @foreach ($build->items as $item)
                            <div class="text-sm">
                                <span class="font-semibold text-gray-500">{{ $item->product->category }}:</span>
                                <span class="text-gray-800">{{ $item->product->name }} × {{ $item->quantity }}</span>
                            </div>
                        @endforeach
                        @if ($build->notes)
                            <p class="col-span-full whitespace-pre-line text-sm text-gray-700">{{ $build->notes }}</p>
                        @endif
                        <div class="text-sm font-black text-gray-900">
                            @if ($build->estimated_cost !== null)
                                @php
                                    $buildVat = \App\Support\PhilippineVat::breakdown((float) $build->estimated_cost);
                                @endphp
                                Estimated price: ₱{{ number_format($buildVat['total'], 2) }}
                                <span class="block text-xs font-normal text-gray-500">Includes ₱{{ number_format($buildVat['vat'], 2) }} Philippine VAT (12%).</span>
                            @else
                                Quote pending
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach (['Product photo' => $build->product_photo_path, 'Before photo' => $build->before_photo_path, 'After photo' => $build->after_photo_path] as $label => $path)
                            <div>
                                <p class="mb-1 text-[10px] font-bold uppercase tracking-wide text-gray-500">{{ $label }}</p>
                                @if ($path)
                                    <a href="{{ asset('storage/'.$path) }}" target="_blank" rel="noopener"><img src="{{ asset('storage/'.$path) }}" alt="{{ $label }} for {{ $build->build_number }}" class="aspect-square w-full rounded-lg object-cover"></a>
                                @else
                                    <div class="flex aspect-square items-center justify-center rounded-lg bg-gray-100 text-center text-[10px] text-gray-400">Photo pending</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-7 text-center">
                <p class="font-semibold text-gray-800">No PC builds yet.</p>
                <p class="mt-1 text-sm text-gray-500">Create a custom build and its progress will appear here.</p>
            </div>
        @endforelse
    </div>
</section>
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
