<x-app-layout>
    @php
        $saleVat = \App\Support\PhilippineVat::breakdown((float) $sale->total_amount);
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Receipt #{{ $sale->id }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('pos.index') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to POS</a>
                <button type="button" onclick="window.print()" class="rounded bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700">Print receipt</button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <p class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</p>
            @endif
            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm print:border-0 print:shadow-none">
                <div class="border-b border-gray-200 pb-4">
                    <p class="text-xs font-bold uppercase text-gray-500">Sales receipt #{{ $sale->id }}</p>
                    @if ($sale->store_order_id)
                        <p class="mt-1 text-xs font-semibold text-purple-700">Customer order #{{ $sale->store_order_id }}</p>
                    @endif
                    <p class="mt-2 text-xl font-bold text-gray-900">{{ $sale->customer_name ?: 'Customer not recorded' }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ $sale->created_at->format('M j, Y g:i A') }}</p>
                </div>

                <div class="space-y-4 py-5">
                    @foreach ($sale->items as $item)
                        <div class="flex items-start justify-between gap-4 text-sm">
                            <div class="min-w-0">
                                <p class="wrap-break-word font-semibold text-gray-900">{{ $item->product_name }}</p>
                                <p class="mt-1 text-gray-500">₱{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                                @if ($item->product?->description)
                                    <p class="mt-1 wrap-break-word text-xs text-gray-600">{{ $item->product->description }}</p>
                                @endif
                                @if ($item->product?->sku)
                                    <p class="mt-1 text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                                @endif
                                @foreach ($item->units as $unit)
                                    <p class="mt-1 text-xs text-gray-500">Serial: {{ $unit->serial_number ?? 'Not recorded' }} · Lot: {{ $unit->lot_code ?: 'Not recorded' }} · Warranty through: {{ $unit->warranty_ends_at?->format('M j, Y') ?: 'Not recorded' }}</p>
                                @endforeach
                            </div>
                            <p class="shrink-0 font-semibold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @endforeach
                </div>

                @if ($sale->shipping_fee > 0)
                    <div class="flex items-center justify-between border-t border-gray-200 py-4 text-sm">
                        <span class="font-semibold text-gray-700">Shipping</span>
                        <span class="font-semibold text-gray-900">₱{{ number_format($sale->shipping_fee, 2) }}</span>
                    </div>
                @endif
                @foreach ($sale->payments as $payment)
                    <div class="space-y-1 border-t border-gray-200 py-4 text-sm">
                        <div class="flex justify-between gap-3"><span>Payment method</span><strong class="capitalize">{{ str_replace('_', ' ', $payment->method) }}</strong></div>
                        <div class="flex justify-between gap-3"><span>Paid</span><strong>₱{{ number_format((float) $payment->amount, 2) }}</strong></div>
                        @if ((float) $payment->amount_tendered > (float) $payment->amount)
                            <div class="flex justify-between gap-3"><span>Cash received</span><span>₱{{ number_format((float) $payment->amount_tendered, 2) }}</span></div>
                            <div class="flex justify-between gap-3"><span>Change</span><span>₱{{ number_format((float) $payment->amount_tendered - (float) $payment->amount, 2) }}</span></div>
                        @endif
                        @if ($payment->reference)
                            <div class="flex justify-between gap-3"><span>Reference</span><span>{{ $payment->reference }}</span></div>
                        @endif
                    </div>
                @endforeach
                <div class="space-y-2 border-t border-gray-200 py-4 text-sm">
                    <p class="text-xs text-gray-500">Prices include 12% Philippine VAT.</p>
                    <div class="flex items-center justify-between text-gray-700">
                        <span>Amount before VAT</span>
                        <span>₱{{ number_format($saleVat['net'], 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-700">
                        <span>VAT (12%, included)</span>
                        <span>₱{{ number_format($saleVat['vat'], 2) }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between border-t border-gray-200 pt-4 text-lg font-bold text-gray-900">
                    <span>Total (VAT included)</span>
                    <span>₱{{ number_format($saleVat['total'], 2) }}</span>
                </div>
            </article>
        </div>
    </div>
</x-app-layout><div>
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->
</div>
