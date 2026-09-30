<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Receipts & Computer Build Orders') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-2">
                <section aria-labelledby="receipts-heading" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 id="receipts-heading" class="text-xl font-bold text-gray-900">Receipts</h3>
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $recentSales->count() }} recent</span>
                    </div>

                    @forelse ($recentSales as $sale)
                        <button type="button"
                                class="receipt-trigger mb-3 w-full rounded border border-gray-200 bg-gray-50 p-4 text-left transition hover:border-purple-600 hover:bg-purple-50"
                                data-receipt-id="receipt-{{ $sale->id }}"
                                aria-haspopup="dialog">
                            <span class="flex items-center justify-between gap-3">
                                <span class="font-bold text-gray-900">Receipt #{{ $sale->id }}</span>
                                <span class="text-sm font-bold text-purple-600">₱{{ number_format($sale->total_amount, 2) }}</span>
                            </span>
                            <span class="mt-2 block text-sm text-gray-700">{{ $sale->customer_name ?: 'Walk-in customer' }}</span>
                            <span class="mt-1 block text-xs text-gray-500">{{ $sale->created_at->format('M j, Y g:i A') }}</span>
                            <span class="mt-3 block text-xs font-semibold text-purple-700">View receipt · {{ $sale->items->count() }} item(s)</span>
                        </button>

                        <dialog id="receipt-{{ $sale->id }}" class="m-auto max-h-[85vh] w-[min(32rem,calc(100%-2rem))] max-w-none overflow-y-auto rounded-lg border border-gray-200 bg-white p-0 shadow-xl backdrop:bg-black/50">
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4 border-b border-gray-200 pb-4">
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900">Receipt #{{ $sale->id }}</h4>
                                        <p class="mt-1 text-sm text-gray-600">{{ $sale->customer_name ?: 'Walk-in customer' }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $sale->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                    <button type="button" class="receipt-close rounded border border-gray-300 px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Close</button>
                                </div>

                                <div class="mt-4 space-y-3">
                                    @foreach ($sale->items as $item)
                                        <div class="flex items-start justify-between gap-4 text-sm">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                                                <p class="text-gray-500">₱{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                                            </div>
                                            <p class="font-semibold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-5 flex items-center justify-between border-t border-gray-200 pt-4 text-base font-bold text-gray-900">
                                    <span>Total</span>
                                    <span>₱{{ number_format($sale->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </dialog>
                    @empty
                        <p class="rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">No receipts yet.</p>
                    @endforelse
                </section>

                <section aria-labelledby="build-orders-heading" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 id="build-orders-heading" class="text-xl font-bold text-gray-900">Computer Build Orders</h3>
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $recentBuilds->count() }} recent</span>
                    </div>

                    @forelse ($recentBuilds as $build)
                        <button type="button"
                                class="build-trigger mb-3 w-full rounded border border-gray-200 bg-gray-50 p-4 text-left transition hover:border-purple-600 hover:bg-purple-50"
                                data-build-id="build-{{ $build->id }}"
                                aria-haspopup="dialog">
                            <span class="flex items-center justify-between gap-3">
                                <span class="font-bold text-gray-900">{{ $build->build_number }}</span>
                                <span class="text-sm font-bold text-purple-600">₱{{ number_format($build->total_cost, 2) }}</span>
                            </span>
                            <span class="mt-2 block text-sm text-gray-700">{{ $build->customer_name ?: 'Walk-in customer' }}</span>
                            <span class="mt-1 block text-xs uppercase text-gray-500">{{ $build->status }} · {{ $build->created_at->format('M j, Y g:i A') }}</span>
                            <span class="mt-3 block text-xs font-semibold text-purple-700">View parts · {{ $build->items->count() }} component(s)</span>
                        </button>

                        <dialog id="build-{{ $build->id }}" class="m-auto max-h-[85vh] w-[min(36rem,calc(100%-2rem))] max-w-none overflow-y-auto rounded-lg border border-gray-200 bg-white p-0 shadow-xl backdrop:bg-black/50">
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4 border-b border-gray-200 pb-4">
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900">{{ $build->build_number }}</h4>
                                        <p class="mt-1 text-sm text-gray-600">{{ $build->customer_name ?: 'Walk-in customer' }}</p>
                                        <p class="mt-1 text-xs uppercase text-gray-500">{{ $build->status }} · {{ $build->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                    <button type="button" class="build-close rounded border border-gray-300 px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Close</button>
                                </div>

                                <div class="mt-4 space-y-3">
                                    @foreach ($build->items as $item)
                                        <div class="flex items-start justify-between gap-4 text-sm">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                                                <p class="text-gray-500">₱{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                                            </div>
                                            <p class="font-semibold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($build->notes)
                                    <div class="mt-4 border-t border-gray-200 pt-3">
                                        <p class="text-xs font-semibold uppercase text-gray-500">Notes</p>
                                        <p class="mt-1 text-sm text-gray-700">{{ $build->notes }}</p>
                                    </div>
                                @endif

                                <div class="mt-5 flex items-center justify-between border-t border-gray-200 pt-4 text-base font-bold text-gray-900">
                                    <span>Total</span>
                                    <span>₱{{ number_format($build->total_cost, 2) }}</span>
                                </div>
                            </div>
                        </dialog>
                    @empty
                        <p class="rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">No computer build orders yet.</p>
                    @endforelse
                </section>
            </div>
        </div>
    </div>

    <script>
        (function () {
            document.querySelectorAll('.receipt-trigger, .build-trigger').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    const dialogId = trigger.dataset.receiptId || trigger.dataset.buildId;
                    document.getElementById(dialogId)?.showModal();
                });
            });

            document.querySelectorAll('.receipt-close, .build-close').forEach((button) => {
                button.addEventListener('click', () => button.closest('dialog')?.close());
            });
        })();
    </script>
</x-app-layout>
