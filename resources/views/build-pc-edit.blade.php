<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Edit PC Build</h2>
            <a href="{{ $isCustomer ? route('buildpc.customer') : route('buildpc.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                Back to builds
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Build</p>
                        <h3 class="mt-2 text-2xl font-black text-gray-900">{{ $pcBuild->build_number }}</h3>
                    </div>
                    <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-purple-700">{{ $pcBuild->status }}</span>
                </div>

                <form method="POST" action="{{ $isCustomer ? route('buildpc.customer.update', $pcBuild) : route('buildpc.update', $pcBuild) }}">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($componentGroups as $type => $label)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <label class="mb-2 block text-sm font-semibold text-gray-700">{{ $label }}</label>
                                <select name="items[{{ $type }}][product_id]" data-key="{{ $type }}" class="component-select w-full rounded border border-gray-300 px-3 py-2 text-sm">
                                    <option value="">Remove / no selection</option>
                                    @foreach ($groupedProducts[$type] ?? [] as $product)
                                        <option value="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-price="{{ $product->price }}"
                                            data-available="{{ $product->available_for_build }}"
                                            {{ ($selectedProducts[$type] ?? null) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} (Avail: {{ $product->available_for_build }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Selected components</h3>
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Summary</div>
                        </div>

                        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="px-3 py-3 font-semibold">Product</th>
                                        <th class="px-3 py-3 font-semibold">Availability</th>
                                        <th class="px-3 py-3 font-semibold text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody id="build-summary-body">
                                    <tr>
                                        <td colspan="3" class="px-3 py-6 text-center text-gray-500">No components selected yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center justify-between rounded-lg border border-purple-200 bg-purple-50 px-4 py-3">
                            <span class="text-sm font-bold uppercase tracking-[0.2em] text-purple-700">Total</span>
                            <span id="build-total" class="text-2xl font-black text-purple-700">₱0</span>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center rounded bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-700">Update Build</button>
                        <a href="{{ $isCustomer ? route('buildpc.customer') : route('buildpc.index') }}" class="inline-flex items-center rounded border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-gray-400">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const summaryBody = document.getElementById('build-summary-body');
        const totalEl = document.getElementById('build-total');
        const selects = document.querySelectorAll('.component-select');

        function formatMoney(value) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP',
                maximumFractionDigits: 2,
            }).format(value);
        }

        function renderBuildSummary() {
            const rows = [];
            let total = 0;

            selects.forEach((select) => {
                const option = select.selectedOptions[0];
                if (!option || !option.value) {
                    return;
                }

                const price = Number(option.dataset.price || 0);
                const available = Number(option.dataset.available || 0);
                total += price;

                rows.push(`
                    <tr>
                        <td class="px-3 py-3 font-semibold text-gray-900">${option.dataset.name}</td>
                        <td class="px-3 py-3 text-gray-700">${available >= 1 ? `${available} available` : 'Out of stock'}</td>
                        <td class="px-3 py-3 text-right font-bold text-purple-700">${formatMoney(price)}</td>
                    </tr>
                `);
            });

            if (rows.length === 0) {
                summaryBody.innerHTML = '<tr><td colspan="3" class="px-3 py-6 text-center text-gray-500">No components selected yet.</td></tr>';
            } else {
                summaryBody.innerHTML = rows.join('');
            }

            totalEl.textContent = formatMoney(total);
        }

        selects.forEach((select) => select.addEventListener('change', renderBuildSummary));
        renderBuildSummary();
    </script>
</x-app-layout>
