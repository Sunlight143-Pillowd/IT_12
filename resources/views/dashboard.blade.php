<x-app-layout>
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
                        <div class="grid items-stretch gap-8 lg:grid-cols-[minmax(0,1.7fr)_minmax(18rem,1fr)]">
                            <div class="flex min-w-0 flex-col overflow-hidden rounded-xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                    <h1 class="text-xl font-black text-white">Customer Orders</h1>
                                    <span class="rounded-full bg-purple-500/20 px-3 py-1 text-xs font-bold text-purple-200">{{ $storeOrders->count() }} order(s)</span>
                                </div>
                                <div class="h-[22rem] overflow-auto rounded-lg border border-white/10">
                                    <table class="min-w-[760px] text-left text-sm text-gray-200">
                                        <thead class="sticky top-0 z-10 bg-gray-900 text-xs uppercase text-gray-300">
                                            <tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Date</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3">Payment / Fulfillment</th><th class="px-4 py-3">Status / Action</th></tr>
                                        </thead>
                                        <tbody>
                                            @forelse($storeOrders as $order)
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
                                                        @if ($order->delivery_address)<p class="mt-1 max-w-48 text-xs text-gray-400">{{ $order->delivery_address }}</p>@endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex flex-col items-start gap-2">
                                                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold text-black {{ $order->status === 'accepted' ? 'bg-emerald-100' : 'bg-amber-100' }}">{{ ucfirst($order->status) }}</span>
                                                            @if ($order->status === 'pending' && $order->shipping_zone === 'outside_davao' && $order->shipping_fee === null && Auth::user()?->canManageOrders())
                                                                <form method="POST" action="{{ route('dashboard.orders.shipping-fee', $order) }}" class="flex items-end gap-2">
                                                                    @csrf
                                                                    <label class="text-xs text-gray-300">
                                                                        Shipping fee
                                                                        <input type="number" name="shipping_fee" min="0" max="100000" step="0.01" required
                                                                               class="mt-1 block w-28 rounded-md border-gray-300 text-sm text-gray-900 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                                                    </label>
                                                                    <button type="submit" class="rounded-lg bg-purple-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-purple-800">Save fee</button>
                                                                </form>
                                                            @elseif ($order->status === 'pending' && Auth::user()?->canManageOrders())
                                                                <form method="POST" action="{{ route('dashboard.orders.accept', $order) }}" class="inline-block">
                                                                    @csrf
                                                                    <button type="submit" class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-800">Accept order</button>
                                                                </form>
                                                            @elseif ($order->status === 'accepted')
                                                                <div class="flex flex-wrap items-center gap-2">
                                                                    <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-black" aria-disabled="true">Order accepted</span>
                                                                    @if ($order->sale)
                                                                        <a href="{{ route('pos.receipt', $order->sale) }}" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:border-purple-600 hover:text-purple-700">View receipt</a>
                                                                    @endif
                                                                </div>
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

                            <div class="flex h-full flex-col rounded-xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                                <div class="mb-4 flex items-center justify-between">
                                    <span class="text-sm font-bold uppercase tracking-wide text-purple-300">Today</span>
                                    <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-[10px] font-bold uppercase text-emerald-300">Live</span>
                                </div>
                                <div class="grid flex-1 auto-rows-fr grid-cols-2 gap-3">
                                    <div class="flex min-w-0 flex-col justify-center gap-3 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-4 transition-colors hover:border-purple-300/30">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Revenue</p>
                                        <p class="break-words text-2xl font-black leading-none tracking-tight tabular-nums text-white">₱{{ number_format($revenue, 0) }}</p>
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-center gap-3 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-4 transition-colors hover:border-purple-300/30">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Orders</p>
                                        <p class="break-words text-2xl font-black leading-none tracking-tight tabular-nums text-white">{{ $todayOrders }}</p>
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-center gap-3 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-4 transition-colors hover:border-red-300/30">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Low Stock</p>
                                        <p class="break-words text-2xl font-black leading-none tracking-tight tabular-nums text-red-400">{{ $lowStock }}</p>
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-center gap-3 rounded-xl border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.03] p-4 transition-colors hover:border-purple-300/30">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-300">Sales Today</p>
                                        <p class="break-words text-2xl font-black leading-none tracking-tight tabular-nums text-white">₱{{ number_format($todaySales, 0) }}</p>
                                    </div>
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
                                    <tr class="border-t border-gray-200"><td class="px-4 py-3 font-semibold text-gray-900">#{{ $sale->id }}</td><td class="px-4 py-3">{{ $sale->customer_name ?: 'Customer not recorded' }}</td><td class="px-4 py-3">{{ $sale->items_count }}@foreach ($sale->items as $item)<span class="block text-xs text-gray-500">{{ $item->product_name }}</span>@endforeach</td><td class="px-4 py-3">{{ $sale->created_at->format('M j, Y') }}</td><td class="px-4 py-3 text-right font-semibold">₱{{ number_format($sale->total_amount, 2) }}</td><td class="px-4 py-3"><a href="{{ route('pos.receipt', $sale) }}" class="rounded border border-gray-300 px-3 py-1.5 text-xs font-semibold hover:border-purple-600">View receipt</a></td></tr>
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
                                    @if ($order->delivery_address)<p class="mt-1 text-xs text-gray-500">Delivery to: {{ $order->delivery_address }}</p>@endif
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
                        <div class="text-sm font-black text-gray-900">Total Price: ₱{{ number_format($build->total_cost, 2) }}</div>
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

    <script>
        document.querySelectorAll('.build-photo-input').forEach((input) => {
            input.addEventListener('change', () => {
                const file = input.files?.[0];
                const preview = document.getElementById(input.dataset.preview);
                if (!file || !preview) {
                    return;
                }

                const imageUrl = URL.createObjectURL(file);
                if (preview.tagName === 'IMG') {
                    preview.src = imageUrl;
                    return;
                }

                const image = document.createElement('img');
                image.id = preview.id;
                image.src = imageUrl;
                image.alt = 'Selected photo preview';
                image.className = 'mb-2 aspect-square w-full rounded-lg bg-white object-cover';
                preview.replaceWith(image);
            });
        });
    </script>
</x-app-layout>
