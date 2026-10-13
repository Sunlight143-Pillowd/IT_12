<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-700">Custom configuration</p>
                <h1 class="mt-1 text-2xl font-black text-gray-900">Build your PC</h1>
            </div>
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-purple-700 hover:underline">View my orders and builds</a>
            @endauth
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl space-y-7 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <form id="customer-build-form" method="POST" action="{{ route('buildpc.customer.store') }}" class="space-y-5">
                    @csrf

                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="mb-5 grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="customer_name" class="mb-2 block text-sm font-bold text-gray-800">Your name</label>
                                <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name', $pendingBuild['customer_name'] ?? auth()->user()?->name ?? '') }}" required maxlength="150" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="John Doe">
                            </div>
                            <div>
                                <label for="customer_email" class="mb-2 block text-sm font-bold text-gray-800">Email address <span class="font-normal text-gray-400">(optional)</span></label>
                                <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email', $pendingBuild['customer_email'] ?? auth()->user()?->email ?? '') }}" maxlength="150" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="you@example.com">
                            </div>
                        </div>
                        <div class="mb-5">
                            <h2 class="text-lg font-black text-gray-900">Choose your components</h2>
                            <p class="mt-1 text-sm text-gray-500">Choose from currently available products. Select at least one component; our team will review your request and provide a quote.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($componentGroups as $type => $label)
                                <div>
                                    <label for="component-{{ $type }}" class="mb-2 block text-sm font-bold text-gray-800">{{ $label }}</label>
                                    <select id="component-{{ $type }}" name="components[{{ $type }}]" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm transition focus:border-purple-500 focus:ring-purple-500">
                                        <option value="">Select {{ $label }} (optional)</option>
                                        @foreach ($groupedProducts[$type] ?? [] as $product)
                                            <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" @selected((string) old('components.'.$type, $pendingBuild['components'][$type] ?? '') === (string) $product->id)>
                                                {{ $product->name }} — ₱{{ number_format($product->price, 2) }} · {{ $product->availableStock() }} available
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                        <section id="customer-build-receipt" aria-live="polite" class="mt-5 rounded-xl border border-purple-200 bg-purple-50 p-4">
                            <div class="mb-3">
                                <h3 class="text-base font-black text-purple-900">Estimated receipt</h3>
                                <p class="mt-1 text-xs text-purple-800">Catalog prices include 12% Philippine VAT. Final quote will be confirmed by our team.</p>
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-purple-100 bg-white">
                                <table class="min-w-full text-left text-sm">
                                    <thead class="bg-purple-50 text-xs uppercase tracking-wide text-purple-800">
                                        <tr><th class="px-3 py-2">Component</th><th class="px-3 py-2 text-right">Amount</th></tr>
                                    </thead>
                                    <tbody id="customer-build-receipt-items">
                                        <tr><td colspan="2" class="px-3 py-4 text-center text-gray-500">Select components to see your estimate.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <dl class="mt-3 space-y-2 text-sm">
                                <div class="flex justify-between gap-4 text-gray-700">
                                    <dt>Amount before VAT</dt>
                                    <dd id="customer-build-net" class="font-semibold">₱0.00</dd>
                                </div>
                                <div class="flex justify-between gap-4 text-gray-700">
                                    <dt>VAT (12%, included)</dt>
                                    <dd id="customer-build-vat" class="font-semibold">₱0.00</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-t border-purple-200 pt-2 font-black text-purple-900">
                                    <dt>Estimated total (VAT included)</dt>
                                    <dd id="customer-build-total">₱0.00</dd>
                                </div>
                            </dl>
                        </section>
                        <label for="build-notes" class="mt-5 block text-sm font-bold text-gray-800">
                            Additional notes <span class="font-normal text-gray-400">(optional)</span>
                            <textarea id="build-notes" name="notes" rows="3" maxlength="2000" placeholder="Budget, intended use, or other preferences" class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('notes', $pendingBuild['notes'] ?? '') }}</textarea>
                        </label>
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <button type="submit" class="inline-flex justify-center rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">Submit build request</button>
                            <p class="text-xs text-gray-500">
                                Estimated price uses the selected catalog parts. Additional notes are not included; our team will confirm the final quote.
                                @guest
                                    <span class="font-semibold text-purple-700">You’ll be asked to sign in before submitting.</span>
                                @endguest
                            </p>
                        </div>
                    </section>
                </form>

            </div>

            @auth
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-xl font-black text-gray-900">PC builds</h2>
                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold text-purple-800">{{ $builds->count() }} build(s)</span>
                    </div>
                    @forelse ($builds as $build)
                        <article class="mb-4 rounded-xl border border-gray-200 p-4 last:mb-0">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Build ID · {{ $build->build_number }}</p>
                                    <h3 class="mt-1 text-lg font-bold text-gray-900">{{ $build->customer_name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $build->created_at->format('M j, Y') }}</p>
                                    @if ($build->notes)
                                        <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ $build->notes }}</p>
                                    @endif
                                    <p class="mt-2 text-sm font-semibold text-gray-700">
                                        @if ($build->estimated_cost !== null)
                                            @php
                                                $buildVat = \App\Support\PhilippineVat::breakdown((float) $build->estimated_cost);
                                            @endphp
                                            Estimated price (VAT included): ₱{{ number_format($buildVat['total'], 2) }}
                                            <span class="block text-xs font-normal text-gray-500">Includes ₱{{ number_format($buildVat['vat'], 2) }} Philippine VAT (12%).</span>
                                        @else
                                            Quote pending
                                        @endif
                                    </p>
                                </div>
                                <span class="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800">{{ ucfirst($build->status) }}</span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('buildpc.show', $build) }}" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">View</a>
                                <a href="{{ route('buildpc.print', $build) }}" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Print</a>
                                <a href="{{ route('buildpc.customer.edit', $build) }}" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Edit</a>
                                <form action="{{ route('buildpc.customer.destroy', $build) }}" method="POST" onsubmit="return confirm('Delete this build?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center rounded border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-red-700 transition hover:border-red-300 hover:bg-red-100">Delete</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">No builds yet. Start by building your first custom PC.</div>
                    @endforelse
                </section>
            @endauth
        </div>
    </div>

    <script>
        (() => {
            const componentSelects = document.querySelectorAll('#customer-build-form select[name^="components["]');
            const receiptItems = document.getElementById('customer-build-receipt-items');
            const netElement = document.getElementById('customer-build-net');
            const vatElement = document.getElementById('customer-build-vat');
            const totalElement = document.getElementById('customer-build-total');
            const moneyFormatter = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
            const vatRate = {{ \App\Support\PhilippineVat::RATE_PERCENT }};

            function updateEstimatedReceipt() {
                const rows = [];
                let total = 0;

                componentSelects.forEach((select) => {
                    const option = select.selectedOptions[0];
                    if (!option || !option.value) {
                        return;
                    }

                    const price = Number(option.dataset.price || 0);
                    total += price;

                    const row = document.createElement('tr');
                    row.className = 'border-t border-gray-100';
                    const componentCell = document.createElement('td');
                    componentCell.className = 'px-3 py-2 font-medium text-gray-800';
                    componentCell.textContent = option.dataset.name;
                    const amountCell = document.createElement('td');
                    amountCell.className = 'px-3 py-2 text-right font-semibold text-gray-900';
                    amountCell.textContent = moneyFormatter.format(price);
                    row.append(componentCell, amountCell);
                    rows.push(row);
                });

                receiptItems.replaceChildren();
                if (rows.length === 0) {
                    const row = document.createElement('tr');
                    const cell = document.createElement('td');
                    cell.colSpan = 2;
                    cell.className = 'px-3 py-4 text-center text-gray-500';
                    cell.textContent = 'Select components to see your estimate.';
                    row.appendChild(cell);
                    receiptItems.appendChild(row);
                } else {
                    rows.forEach((row) => receiptItems.appendChild(row));
                }

                const vat = Math.round(total * vatRate / (100 + vatRate) * 100) / 100;
                netElement.textContent = moneyFormatter.format(total - vat);
                vatElement.textContent = moneyFormatter.format(vat);
                totalElement.textContent = moneyFormatter.format(total);
            }

            componentSelects.forEach((select) => select.addEventListener('change', updateEstimatedReceipt));
            updateEstimatedReceipt();
        })();
    </script>
</x-app-layout>
