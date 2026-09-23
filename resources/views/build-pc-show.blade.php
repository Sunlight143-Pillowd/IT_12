<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">PC Build Details</h2>
            <a href="{{ route('buildpc.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                Back to Build PC
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">{{ $pcBuild->build_number }}</p>
                        <h3 class="mt-2 text-2xl font-black text-gray-900">{{ $pcBuild->customer_name ?: 'Walk-in Customer' }}</h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-purple-700">{{ $pcBuild->status }}</span>
                        <span class="rounded-full bg-gray-200 px-3 py-1 text-xs font-bold uppercase tracking-wide text-gray-700">Total: ₱{{ number_format($pcBuild->total_cost, 2) }}</span>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-xl border border-gray-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-3 py-3 font-semibold">Product</th>
                                <th class="px-3 py-3 font-semibold">Qty</th>
                                <th class="px-3 py-3 font-semibold">Unit Price</th>
                                <th class="px-3 py-3 font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pcBuild->items as $item)
                                <tr class="border-t border-gray-200">
                                    <td class="px-3 py-3 font-semibold text-gray-900">{{ $item->product->name }}</td>
                                    <td class="px-3 py-3 text-gray-700">{{ $item->quantity }}</td>
                                    <td class="px-3 py-3 text-gray-700">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-3 py-3 font-bold text-purple-700">₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Notes</p>
                    <p class="mt-2 text-sm text-gray-700">{{ $pcBuild->notes ?: 'No notes provided.' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
