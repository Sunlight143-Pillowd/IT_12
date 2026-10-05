<x-app-layout>
    @if($isAdmin)
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
                <section class="relative overflow-hidden rounded-2xl bg-black text-white shadow-xl">
                    <div class="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_top,rgba(168,85,247,0.55),transparent_55%)]"></div>
                    <div class="relative grid gap-8 p-8 lg:grid-cols-2 lg:p-10">
                        <div>
                            <p class="mb-3 text-sm font-bold uppercase tracking-[0.2em] text-purple-300">Admin access</p>
                            <h1 class="text-3xl font-black leading-tight md:text-5xl">
                                Control your store from one dashboard.
                            </h1>
                            <p class="mt-4 max-w-lg text-sm text-gray-300 md:text-base">
                                Keep an eye on inventory, daily sales, and high-priority product movement from a single control center.
                            </p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('inventory.index') }}" class="inline-block bg-purple-600 px-5 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-purple-700">VIEW INVENTORY</a>
                                <a href="{{ route('pos.index') }}" class="inline-block border border-white/30 bg-white/5 px-5 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-white/10">OPEN POS</a>
                            </div>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                            <div class="mb-4 flex items-center justify-between">
                                <span class="text-sm font-bold uppercase tracking-wide text-purple-300">Today</span>
                                <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-[10px] font-bold uppercase text-emerald-300">Live</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-300">Revenue</p>
                                    <p class="mt-2 text-2xl font-black text-white">₱{{ number_format($revenue, 0) }}</p>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-300">Orders</p>
                                    <p class="mt-2 text-2xl font-black text-white">{{ $todayOrders }}</p>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-300">Low Stock</p>
                                    <p class="mt-2 text-2xl font-black text-red-400">{{ $lowStock }}</p>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-300">Sales Today</p>
                                    <p class="mt-2 text-2xl font-black text-white">₱{{ number_format($todaySales, 0) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="grid gap-4 md:grid-cols-4">
                    @php
                        $stats = [
                            ['label' => 'Products', 'value' => number_format((int) $products), 'tone' => 'text-gray-900'],
                            ['label' => 'Low Stock', 'value' => number_format((int) $lowStock), 'tone' => 'text-red-600'],
                            ['label' => 'Sales Today', 'value' => number_format($todayOrders), 'tone' => 'text-emerald-600'],
                            ['label' => 'Revenue', 'value' => '₱' . number_format($revenue, 0), 'tone' => 'text-purple-600'],
                        ];
                    @endphp

                    @foreach($stats as $stat)
                        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">{{ $stat['label'] }}</p>
                            <p class="mt-3 text-3xl font-black {{ $stat['tone'] }}">{{ $stat['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-xl font-black text-gray-900">Top Selling Products</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">Units Sold</th><th class="px-4 py-3 text-right">Sales</th></tr></thead>
                                <tbody>
                                    @forelse($topProducts as $product)
                                        <tr class="border-t border-gray-200"><td class="px-4 py-3 font-semibold text-gray-900">{{ $product->product_name }}</td><td class="px-4 py-3">{{ $product->units_sold }}</td><td class="px-4 py-3 text-right font-semibold">₱{{ number_format($product->sales_total, 2) }}</td></tr>
                                    @empty
                                        <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">No product sales recorded.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-gray-900">Quick Actions</h2>
                        <div class="mt-5 space-y-3">
                            <a href="{{ route('inventory.index') }}" class="block rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Inventory</p>
                                <p class="text-sm text-gray-500">Check stock levels</p>
                            </a>
                            <a href="{{ route('pos.index') }}" class="block rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">POS</p>
                                <p class="text-sm text-gray-500">Open cashier</p>
                            </a>
                            <a href="{{ route('quotation.index') }}" class="block rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Quotations</p>
                                <p class="text-sm text-gray-500">Create customer quotes</p>
                            </a>
                            <a href="{{ route('buildpc.index') }}" class="block rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-purple-600 hover:bg-purple-50">
                                <p class="font-bold text-gray-900">Build PC</p>
                                <p class="text-sm text-gray-500">Create custom desktop builds</p>
                            </a>
                        </div>
                    </div>
                </div>

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
                                    <tr class="border-t border-gray-200"><td class="px-4 py-3 font-semibold text-gray-900">#{{ $sale->id }}</td><td class="px-4 py-3">{{ $sale->customer_name ?: 'Customer not recorded' }}</td><td class="px-4 py-3">{{ $sale->items_count }}</td><td class="px-4 py-3">{{ $sale->created_at->format('M j, Y') }}</td><td class="px-4 py-3 text-right font-semibold">₱{{ number_format($sale->total_amount, 2) }}</td><td class="px-4 py-3"><a href="{{ route('pos.receipt', $sale) }}" class="rounded border border-gray-300 px-3 py-1.5 text-xs font-semibold hover:border-purple-600">View receipt</a></td></tr>
                                @empty
                                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No sales recorded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 p-5">
                        <h2 class="text-xl font-black text-gray-900">Pending Customer Orders</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Date</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3">Status</th></tr></thead>
                            <tbody>
                                @forelse($pendingStoreOrders as $order)
                                    <tr class="border-t border-gray-200">
                                        <td class="px-4 py-3 font-semibold text-gray-900">#{{ $order->id }}</td>
                                        <td class="px-4 py-3">{{ $order->customer_name }}<br><span class="text-xs text-gray-500">{{ $order->customer_email }}</span></td>
                                        <td class="px-4 py-3">
                                            <ul class="space-y-1">
                                                @foreach ($order->items as $item)
                                                    <li>{{ $item->product_name }} × {{ $item->quantity }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-4 py-3">{{ $order->created_at->format('M j, Y') }}</td>
                                        <td class="px-4 py-3 text-right font-semibold">₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">{{ ucfirst($order->status) }}</span>
                                                @if ($order->status === 'pending' && Auth::user()?->canManageOrders())
                                                    <form method="POST" action="{{ route('dashboard.orders.accept', $order) }}" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="rounded bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white transition hover:bg-emerald-700">Accept order</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No pending customer orders.</td></tr>
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
            <h2 class="text-2xl font-black text-gray-900">Purchase History</h2>
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
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                        <div class="flex flex-col gap-3 border-b border-gray-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">
                                    {{ isset($order->status) ? 'Order' : 'Receipt' }} #{{ $order->id }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600">{{ $order->created_at->format('F d, Y h:i A') }}</p>
                            </div>
                            <div class="text-left sm:text-right">
                                @isset($order->status)
                                    <p class="text-sm text-gray-500">Status: {{ ucfirst($order->status) }}</p>
                                @endisset
                                <p class="mt-1 text-xl font-black text-gray-900">₱{{ number_format($order->total_amount, 2) }}</p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between gap-4 border-b border-gray-200 pb-2 last:border-0 last:pb-0">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                                        <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
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
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
