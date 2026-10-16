<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Warranty & Service Transactions</h2>
            <a href="{{ route('dashboard') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <p class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                <ul class="list-disc rounded border border-red-200 bg-red-50 px-4 py-3 pl-8 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-lg font-bold text-gray-900">Verify Coverage</h3>
                <p class="mb-4 text-sm text-gray-600">Search by serial number, lot code, SKU, or product name to view the receipt and warranty dates.</p>
                <form method="GET" action="{{ route('warranty.index') }}" class="flex flex-col gap-2 sm:flex-row">
                    <input name="q" value="{{ $search }}" autofocus placeholder="Serial, lot code, SKU, or product" class="min-w-0 flex-1 rounded border-gray-300 text-sm">
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Look up product</button>
                    <a href="{{ route('warranty.index') }}" class="rounded border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Clear</a>
                </form>
            </section>

            @if ($search !== '')
                <section class="space-y-3">
                    <h3 class="text-lg font-bold text-gray-900">Matching Product Units</h3>
                    @forelse ($units as $unit)
                        @php
                            $sale = $unit->saleItem?->sale;
                            $covered = $unit->warranty_ends_at && $unit->warranty_ends_at->gte(today());
                        @endphp
                        <article class="grid gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:grid-cols-[1fr_auto]">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-bold text-gray-900">{{ $unit->product->name }}</h4>
                                    <span class="rounded-full px-2 py-1 text-xs font-bold {{ $covered ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $covered ? 'Coverage active' : 'Coverage expired / unknown' }}</span>
                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">{{ ucfirst(str_replace('_', ' ', $unit->status)) }}</span>
                                </div>
                                <dl class="mt-3 grid gap-2 text-sm sm:grid-cols-2 xl:grid-cols-3">
                                    <div><dt class="text-xs text-gray-500">SKU</dt><dd>{{ $unit->product->sku }}</dd></div>
                                    <div><dt class="text-xs text-gray-500">Serial number</dt><dd>{{ $unit->serial_number ?: 'Not recorded' }}</dd></div>
                                    <div><dt class="text-xs text-gray-500">Lot code</dt><dd>{{ $unit->lot_code ?: 'Not recorded' }}</dd></div>
                                    <div><dt class="text-xs text-gray-500">Manufactured</dt><dd>{{ $unit->manufactured_at?->format('M j, Y') ?: 'Not recorded' }}</dd></div>
                                    <div><dt class="text-xs text-gray-500">Warranty through</dt><dd>{{ $unit->warranty_ends_at?->format('M j, Y') ?: 'Not recorded' }}</dd></div>
                                    <div><dt class="text-xs text-gray-500">Supplier / invoice</dt><dd>{{ $unit->stockInItem?->stockIn?->supplier?->name ?: $unit->stockInItem?->stockIn?->supplier_name ?: 'Not recorded' }} · {{ $unit->stockInItem?->stockIn?->invoice_number ?: '—' }}</dd></div>
                                    <div><dt class="text-xs text-gray-500">Sale / customer</dt><dd>{{ $sale?->id ? 'Receipt #'.$sale->id : 'Not sold in POS' }} · {{ $sale?->customer_name ?: '—' }}</dd></div>
                                </dl>
                            </div>
                            @if ($unit->status === 'sold')
                                <form method="POST" action="{{ route('warranty.cases.store') }}" class="grid min-w-64 gap-2 rounded-lg bg-gray-50 p-3">
                                    @csrf
                                    <input type="hidden" name="product_unit_id" value="{{ $unit->id }}">
                                    <select name="customer_id" class="rounded border-gray-300 text-sm">
                                        <option value="">Customer (optional)</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                    <select name="case_type" class="rounded border-gray-300 text-sm">
                                        <option value="warranty">Warranty case</option>
                                        <option value="replacement">Replacement</option>
                                    </select>
                                    <textarea name="description" required maxlength="3000" rows="2" placeholder="Issue or request" class="rounded border-gray-300 text-sm"></textarea>
                                    <button class="rounded bg-purple-700 px-3 py-2 text-xs font-bold text-white hover:bg-purple-800">Open service transaction</button>
                                </form>
                            @endif
                        </article>
                    @empty
                        <p class="rounded-lg bg-white p-5 text-sm text-gray-500">No matching stock or purchase records found.</p>
                    @endforelse
                </section>
            @endif

            <section class="space-y-3">
                <h3 class="text-lg font-bold text-gray-900">Recent Service Transactions</h3>
                @forelse ($serviceCases as $case)
                    <article class="grid gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:grid-cols-[1fr_auto]">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="font-bold text-gray-900">{{ $case->case_number }}</h4>
                                <span class="rounded-full bg-purple-50 px-2 py-1 text-xs font-semibold text-purple-800">{{ ucfirst(str_replace('_', ' ', $case->status)) }}</span>
                                <span class="text-xs text-gray-500">{{ ucfirst($case->case_type) }}</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-700">{{ $case->description }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $case->customer?->name ?: 'Customer not linked' }} ·
                                {{ $case->productUnit?->product?->name ?: 'Product unavailable' }} ·
                                {{ $case->created_at->format('M j, Y') }}
                            </p>
                            @if ($case->resolution)
                                <p class="mt-2 text-sm text-gray-600">Resolution: {{ $case->resolution }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('warranty.cases.update', $case) }}" class="grid min-w-56 gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="rounded border-gray-300 text-sm">
                                @foreach (['open', 'under_review', 'replacement_in_progress', 'completed'] as $status)
                                    <option value="{{ $status }}" @selected($case->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                            <textarea name="resolution" rows="2" maxlength="3000" placeholder="Resolution / update" class="rounded border-gray-300 text-sm">{{ $case->resolution }}</textarea>
                            <button class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-xs font-bold text-purple-800 hover:bg-purple-100">Update status</button>
                        </form>
                    </article>
                @empty
                    <p class="rounded-lg bg-white p-5 text-sm text-gray-500">No service transactions have been opened.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
