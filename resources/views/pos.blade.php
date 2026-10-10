<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('POS Transactions') }}
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
                        <h3 id="order-list-heading" class="text-xl font-bold text-gray-900">POS Transactions</h3>
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $recentSales->count() + $recentBuilds->count() + $recentStockIns->count() }} records</span>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-2" role="group" aria-label="POS record type">
                        <button type="button" class="history-tab rounded border border-purple-600 bg-purple-600 px-3 py-2 text-sm font-semibold text-white" data-history-tab="receipts" aria-pressed="true">
                            Receipts ({{ $recentSales->count() }})
                        </button>
                        <button type="button" class="history-tab rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700" data-history-tab="builds" aria-pressed="false">
                            Computer Builds ({{ $recentBuilds->count() }})
                        </button>
                        <button type="button" class="history-tab rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700" data-history-tab="invoices" aria-pressed="false">
                            Supplier Invoices ({{ $recentStockIns->count() }})
                        </button>
                    </div>

                    <label for="history-search" class="sr-only">Search receipts, builds, and supplier invoices</label>
                    <input id="history-search" type="search" placeholder="Search receipts, builds, invoices, suppliers, products..." autocomplete="off" class="mb-4 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-purple-600 focus:ring-purple-600">

                    <div id="receipt-list" class="history-panel grid gap-3 sm:grid-cols-2 xl:grid-cols-3" data-history-panel="receipts">
                        @forelse ($recentSales as $sale)
                            <button type="button"
                                    class="order-card rounded border border-gray-200 bg-gray-50 p-3 text-left transition hover:border-purple-600 hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    data-detail-template="receipt-detail-{{ $sale->id }}"
                                    data-record-type="Receipt"
                                    aria-pressed="false">
                                <span class="block truncate text-sm font-bold text-gray-900">Receipt #{{ $sale->id }}</span>
                                <span class="mt-1 block truncate text-xs text-gray-600">{{ $sale->customer_name ?: 'Customer not recorded' }}</span>
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
                                    data-record-type="{{ $build->user_id !== null && $build->stock_deducted_at !== null ? 'Customer PC Build Receipt' : 'Computer Build' }}"
                                    aria-pressed="false">
                                <span class="block truncate text-sm font-bold text-gray-900">{{ $build->build_number }}</span>
                                <span class="mt-1 block truncate text-xs text-gray-600">{{ $build->customer_name ?: 'Customer not recorded' }}</span>
                                <span class="mt-2 block text-sm font-bold text-purple-600">₱{{ number_format($build->total_cost, 2) }}</span>
                                <span class="mt-1 block text-[11px] uppercase text-gray-500">
                                    @if ($build->user_id !== null && $build->stock_deducted_at !== null)
                                        Customer PC Build Receipt
                                    @else
                                        {{ $build->status }}
                                    @endif
                                    · {{ $build->items->count() }} part(s)
                                </span>
                            </button>
                        @empty
                            <p class="col-span-full rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">No computer build orders yet.</p>
                        @endforelse
                    </div>

                    <div id="invoice-list" class="history-panel grid gap-3 sm:grid-cols-2 xl:grid-cols-3" data-history-panel="invoices" hidden>
                        @forelse ($recentStockIns as $stockIn)
                            <button type="button"
                                    class="order-card rounded border border-gray-200 bg-gray-50 p-3 text-left transition hover:border-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    data-detail-template="invoice-detail-{{ $stockIn->id }}"
                                    data-record-type="Supplier Invoice"
                                    aria-pressed="false">
                                <span class="block truncate text-sm font-bold text-gray-900">Invoice {{ $stockIn->invoice_number }}</span>
                                <span class="mt-1 block truncate text-xs text-gray-600">{{ $stockIn->supplier_name }}</span>
                                <span class="mt-2 block text-sm font-bold text-red-600">₱{{ number_format($stockIn->invoice_total, 2) }}</span>
                                <span class="mt-1 block text-[11px] text-gray-500">{{ $stockIn->items->count() }} item(s) · {{ $stockIn->received_at->format('M j, Y') }}</span>
                            </button>
                        @empty
                            <p class="col-span-full rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">No supplier invoices yet.</p>
                        @endforelse
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
                @foreach ($recentSales as $sale)
                    <template id="receipt-detail-{{ $sale->id }}">
                        <div>
                            <div class="border-b border-gray-200 pb-3">
                                <p class="font-bold text-gray-900">Receipt #{{ $sale->id }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $sale->customer_name ?: 'Customer not recorded' }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $sale->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            <div class="space-y-3 py-4">
                                @foreach ($sale->items as $item)
                                    <div class="flex items-start justify-between gap-2 text-sm">
                                        <div class="min-w-0">
                                            <p class="wrap-break-word font-semibold text-gray-900">{{ $item->product_name }}</p>
                                            <p class="text-xs text-gray-500">₱{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                                            @if ($item->product?->description)
                                                <p class="mt-1 wrap-break-word text-xs text-gray-600">{{ $item->product->description }}</p>
                                            @endif
                                            @foreach ($item->units as $unit)
                                                <p class="mt-1 text-xs text-gray-500">Serial: {{ $unit->serial_number ?? 'Not recorded' }} · Warranty: {{ $unit->warranty_months !== null ? $unit->warranty_months.' months' : 'Not recorded' }}</p>
                                            @endforeach
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
                                <p class="mt-1 text-sm text-gray-600">{{ $build->customer_name ?: 'Customer not recorded' }}</p>
                                <p class="mt-1 text-xs uppercase text-gray-500">
                                    @if ($build->user_id !== null && $build->stock_deducted_at !== null)
                                        Customer PC Build Receipt
                                    @else
                                        {{ $build->status }}
                                    @endif
                                    · {{ ($build->stock_deducted_at ?? $build->created_at)->format('M j, Y g:i A') }}
                                </p>
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

                @foreach ($recentStockIns as $stockIn)
                    <template id="invoice-detail-{{ $stockIn->id }}">
                        <div>
                            <div class="border-b border-gray-200 pb-3">
                                <p class="font-bold text-gray-900">Supplier Invoice {{ $stockIn->invoice_number }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $stockIn->supplier_name }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $stockIn->received_at->format('M j, Y g:i A') }}</p>
                                @if ($stockIn->supplier_contact)
                                    <p class="mt-1 text-xs text-gray-500">{{ $stockIn->supplier_contact }}</p>
                                @endif
                            </div>
                            <div class="space-y-3 py-4">
                                @foreach ($stockIn->items as $item)
                                    <div class="flex items-start justify-between gap-2 text-sm">
                                        <div class="min-w-0">
                                            <p class="wrap-break-word font-semibold text-gray-900">{{ $item->product->name }}</p>
                                            <p class="text-xs text-gray-500">₱{{ number_format($item->unit_cost, 2) }} × {{ $item->quantity }}</p>
                                        </div>
                                        <p class="shrink-0 font-semibold text-gray-900">₱{{ number_format((float) $item->unit_cost * $item->quantity, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                            @if ($stockIn->delivery_document_path)
                                <a class="mb-3 inline-block text-sm font-semibold text-purple-700 hover:underline" href="{{ asset('storage/'.$stockIn->delivery_document_path) }}" target="_blank" rel="noopener">Open supplier invoice document</a>
                            @endif
                            <div class="flex items-center justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                                <span>Invoice total</span>
                                <span>₱{{ number_format($stockIn->invoice_total, 2) }}</span>
                            </div>
                        </div>
                    </template>
                @endforeach
            </div>
        </div>

        <section id="pos-analytics" aria-labelledby="pos-analytics-heading" class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h3 id="pos-analytics-heading" class="text-xl font-bold text-gray-900">Receipts & Supplier Invoice Analytics</h3>
                    <p class="mt-1 text-sm text-gray-500">Daily totals for the last 30 days, including today.</p>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Green = receipts · Red = stock invoices</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">
                <figure class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <figcaption class="mb-4 text-sm font-bold text-gray-800">Receipts vs. stock invoices</figcaption>
                    <div class="relative mx-auto aspect-square w-48 rounded-full"
                        @if ($chartTotal > 0)
                            style="background: conic-gradient(#16a34a 0% {{ number_format($receiptSharePercent, 2, '.', '') }}%, #dc2626 {{ number_format($receiptSharePercent, 2, '.', '') }}% 100%)"
                        @else
                            style="background: #e5e7eb"
                        @endif
                        role="img"
                        aria-label="Receipts total ₱{{ number_format($receiptTotal, 2) }} and supplier invoices total ₱{{ number_format($invoiceTotal, 2) }} for the last 30 days">
                        <div class="absolute inset-6 flex flex-col items-center justify-center rounded-full bg-white text-center">
                            <span class="text-xs font-semibold uppercase text-gray-500">POS activity</span>
                            <span class="mt-1 text-lg font-black text-gray-900">₱{{ number_format($chartTotal, 2) }}</span>
                        </div>
                    </div>
                    <div class="mt-5 space-y-2 text-sm">
                        <p class="flex items-center justify-between gap-3">
                            <span class="flex items-center gap-2 text-gray-700"><span class="h-3 w-3 rounded-full bg-green-600"></span>Receipts + accepted builds</span>
                            <strong class="text-gray-900">₱{{ number_format($receiptTotal, 2) }}</strong>
                        </p>
                        <p class="flex items-center justify-between gap-3">
                            <span class="flex items-center gap-2 text-gray-700"><span class="h-3 w-3 rounded-full bg-red-600"></span>Supplier invoices</span>
                            <strong class="text-gray-900">₱{{ number_format($invoiceTotal, 2) }}</strong>
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
            const chartData = {{ \Illuminate\Support\Js::from($posChartData) }};
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
</x-app-layout>
