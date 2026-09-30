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

            <div class="grid gap-6 lg:grid-cols-3">
                <section aria-labelledby="order-list-heading" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 id="order-list-heading" class="text-xl font-bold text-gray-900">Receipts & Computer Build Orders</h3>
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $recentSales->count() + $recentBuilds->count() }} records</span>
                    </div>

                    <div class="mb-4 flex gap-2" role="group" aria-label="Order type">
                        <button type="button" class="history-tab rounded border border-purple-600 bg-purple-600 px-3 py-2 text-sm font-semibold text-white" data-history-tab="receipts" aria-pressed="true">
                            Receipts ({{ $recentSales->count() }})
                        </button>
                        <button type="button" class="history-tab rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700" data-history-tab="builds" aria-pressed="false">
                            Computer Builds ({{ $recentBuilds->count() }})
                        </button>
                    </div>

                    <div id="receipt-list" class="history-panel grid gap-3 sm:grid-cols-2 xl:grid-cols-3" data-history-panel="receipts">
                        @forelse ($recentSales as $sale)
                            <button type="button"
                                    class="order-card rounded border border-gray-200 bg-gray-50 p-3 text-left transition hover:border-purple-600 hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    data-detail-template="receipt-detail-{{ $sale->id }}"
                                    data-record-type="Receipt"
                                    aria-pressed="false">
                                <span class="block truncate text-sm font-bold text-gray-900">Receipt #{{ $sale->id }}</span>
                                <span class="mt-1 block truncate text-xs text-gray-600">{{ $sale->customer_name ?: 'Walk-in customer' }}</span>
                                <span class="mt-2 block text-sm font-bold text-purple-600">₱{{ number_format($sale->total_amount, 2) }}</span>
                                <span class="mt-1 block text-[11px] text-gray-500">{{ $sale->items->count() }} item(s) · {{ $sale->created_at->format('M j, Y') }}</span>
                            </button>
                        @empty
                            <p class="col-span-full rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">No receipts yet.</p>
                        @endforelse
                    </div>

                    <div id="build-list" class="history-panel grid gap-3 sm:grid-cols-2 xl:grid-cols-3" data-history-panel="builds" hidden>
                        @forelse ($recentBuilds as $build)
                            <button type="button"
                                    class="order-card rounded border border-gray-200 bg-gray-50 p-3 text-left transition hover:border-purple-600 hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    data-detail-template="build-detail-{{ $build->id }}"
                                    data-record-type="Computer Build"
                                    aria-pressed="false">
                                <span class="block truncate text-sm font-bold text-gray-900">{{ $build->build_number }}</span>
                                <span class="mt-1 block truncate text-xs text-gray-600">{{ $build->customer_name ?: 'Walk-in customer' }}</span>
                                <span class="mt-2 block text-sm font-bold text-purple-600">₱{{ number_format($build->total_cost, 2) }}</span>
                                <span class="mt-1 block text-[11px] uppercase text-gray-500">{{ $build->status }} · {{ $build->items->count() }} part(s)</span>
                            </button>
                        @empty
                            <p class="col-span-full rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">No computer build orders yet.</p>
                        @endforelse
                    </div>
                </section>

                <section aria-labelledby="selected-order-title" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 id="selected-order-title" class="mb-4 text-xl font-bold text-gray-900">Order Details</h3>
                    <div id="selected-order-details" aria-live="polite" class="min-h-30">
                        <p class="text-sm text-gray-400">Select a receipt or computer build to view its items.</p>
                    </div>
                </section>
            </div>

            <div class="hidden" aria-hidden="true">
                @foreach ($recentSales as $sale)
                    <template id="receipt-detail-{{ $sale->id }}">
                        <div>
                            <div class="border-b border-gray-200 pb-3">
                                <p class="font-bold text-gray-900">Receipt #{{ $sale->id }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $sale->customer_name ?: 'Walk-in customer' }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $sale->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            <div class="space-y-3 py-4">
                                @foreach ($sale->items as $item)
                                    <div class="flex items-start justify-between gap-2 text-sm">
                                        <div class="min-w-0">
                                            <p class="wrap-break-word font-semibold text-gray-900">{{ $item->product_name }}</p>
                                            <p class="text-xs text-gray-500">₱{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                                        </div>
                                        <p class="shrink-0 font-semibold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                                <span>Total</span>
                                <span>₱{{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </template>
                @endforeach

                @foreach ($recentBuilds as $build)
                    <template id="build-detail-{{ $build->id }}">
                        <div>
                            <div class="border-b border-gray-200 pb-3">
                                <p class="font-bold text-gray-900">{{ $build->build_number }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $build->customer_name ?: 'Walk-in customer' }}</p>
                                <p class="mt-1 text-xs uppercase text-gray-500">{{ $build->status }} · {{ $build->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            <div class="space-y-3 py-4">
                                @foreach ($build->items as $item)
                                    <div class="flex items-start justify-between gap-2 text-sm">
                                        <div class="min-w-0">
                                            <p class="wrap-break-word font-semibold text-gray-900">{{ $item->product->name }}</p>
                                            <p class="text-xs text-gray-500">₱{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                                        </div>
                                        <p class="shrink-0 font-semibold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                            @if ($build->notes)
                                <div class="mb-3 border-t border-gray-200 pt-3">
                                    <p class="text-xs font-semibold uppercase text-gray-500">Notes</p>
                                        <p class="mt-1 wrap-break-word text-sm text-gray-700">{{ $build->notes }}</p>
                                </div>
                            @endif
                            <div class="flex items-center justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                                <span>Total</span>
                                <span>₱{{ number_format($build->total_cost, 2) }}</span>
                            </div>
                        </div>
                    </template>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        (function () {
            const orderCards = document.querySelectorAll('.order-card');
            const orderDetails = document.getElementById('selected-order-details');
            const orderDetailsTitle = document.getElementById('selected-order-title');
            const historyTabs = document.querySelectorAll('.history-tab');

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

                    const selectedCard = document.querySelector(`[data-history-panel="${selectedPanel}"] .order-card`);
                    if (selectedCard) {
                        selectOrder(selectedCard);
                        return;
                    }

                    orderCards.forEach((orderCard) => orderCard.setAttribute('aria-pressed', 'false'));
                    orderDetailsTitle.textContent = selectedPanel === 'receipts' ? 'Receipt Details' : 'Computer Build Details';
                    orderDetails.replaceChildren();

                    const emptyMessage = document.createElement('p');
                    emptyMessage.className = 'text-sm text-gray-400';
                    emptyMessage.textContent = selectedPanel === 'receipts' ? 'No receipts yet.' : 'No computer build orders yet.';
                    orderDetails.appendChild(emptyMessage);
                });
            });

            const initialTab = document.querySelector('[data-history-panel="receipts"] .order-card')
                ? 'receipts'
                : 'builds';
            document.querySelector(`[data-history-tab="${initialTab}"]`)?.click();
        })();
    </script>
</x-app-layout>
