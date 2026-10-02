<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Quotation #{{ $quotation->id }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('quotation.index') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Quotations</a>
                <button type="button" onclick="window.print()" class="rounded bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700">Print</button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm print:border-0 print:shadow-none">
                <div class="flex flex-col gap-4 border-b border-gray-200 pb-5 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-500">Quotation #{{ $quotation->id }}</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $quotation->customer_name ?: 'Customer not recorded' }}</p>
                        <p class="mt-1 text-sm text-gray-600">{{ $quotation->customer_contact ?: 'No contact recorded' }}</p>
                    </div>
                    <div class="sm:text-right">
                        <p class="text-xs font-bold uppercase text-gray-500">Created</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $quotation->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                </div>

                <section class="py-5">
                    <h3 class="text-sm font-bold uppercase text-gray-500">Purpose / Notes</h3>
                    <p class="mt-2 whitespace-pre-line text-sm text-gray-800">{{ $quotation->notes ?: 'No purpose or notes were provided.' }}</p>
                </section>

                <div class="overflow-x-auto border-y border-gray-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-3 py-3">Item</th><th class="px-3 py-3">Unit Price</th><th class="px-3 py-3">Qty</th><th class="px-3 py-3 text-right">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($quotation->items as $item)
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-3 font-semibold text-gray-900">{{ $item->product_name }}</td>
                                    <td class="px-3 py-3">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-3 py-3">{{ $item->quantity }}</td>
                                    <td class="px-3 py-3 text-right font-semibold">₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-5">
                    <div class="flex w-full max-w-xs justify-between text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span>₱{{ number_format($quotation->total_amount, 2) }}</span>
                    </div>
                </div>
            </article>
        </div>
    </div>
</x-app-layout><div>
    <!-- People find pleasure in different ways. I find it in keeping my mind clear. - Marcus Aurelius -->
</div>
