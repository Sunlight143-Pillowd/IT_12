<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-700">Custom configuration</p>
                <h1 class="mt-1 text-2xl font-black text-gray-900">Edit your build</h1>
            </div>
            <a href="{{ route('buildpc.customer') }}" class="text-sm font-semibold text-purple-700 hover:underline">Back to build page</a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl space-y-7 px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('buildpc.customer.update', $pcBuild) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5">
                        <h2 class="text-lg font-black text-gray-900">Update your components</h2>
                        <p class="mt-1 text-sm text-gray-500">Choose a replacement product or leave a slot blank to remove it.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($componentGroups as $type => $label)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <label for="component-{{ $type }}" class="mb-2 block text-sm font-bold text-gray-800">{{ $label }}</label>
                                <select id="component-{{ $type }}" name="items[{{ $type }}][product_id]" data-key="{{ $type }}" class="component-select w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">Remove / keep empty</option>
                                    @foreach ($groupedProducts[$type] ?? [] as $product)
                                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-available="{{ $product->availableStock() }}" {{ ($selectedProducts[$type] ?? null) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} — ₱{{ number_format($product->price, 2) }} ({{ $product->availableStock() }} available)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-gray-900">Edited selection</h2>
                            <p class="mt-1 text-sm text-gray-500">Blank selections are removed from the build.</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-500">Build status: <span class="text-amber-700">Pending</span></p>
                    </div>

                    <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">Component</th>
                                    <th class="px-4 py-3">Availability</th>
                                    <th class="px-4 py-3 text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody id="build-summary-body">
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-500">Select components to update your build.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex items-center justify-between rounded-xl bg-purple-50 px-4 py-4">
                        <span class="text-sm font-bold uppercase tracking-wide text-purple-800">Estimated total</span>
                        <span id="build-total" class="text-2xl font-black text-purple-800">₱0.00</span>
                    </div>

                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button type="submit" style="background-color: #6b21a8; color: #fff;" class="inline-flex justify-center rounded-xl px-5 py-3 text-sm font-bold shadow-sm transition hover:brightness-90 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            Save changes
                        </button>
                        <a href="{{ route('buildpc.customer') }}" class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm transition hover:border-gray-400">Cancel</a>
                    </div>
                </section>
            </form>
        </div>
    </div>

    <script>
        const summaryBody = document.getElementById('build-summary-body');
        const totalEl = document.getElementById('build-total');
        const componentSelects = document.querySelectorAll('.component-select');
        const moneyFormatter = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });

        function updateBuildSummary() {
            const rows = [];
            let total = 0;

            componentSelects.forEach((select) => {
                const option = select.selectedOptions[0];
                if (!option || !option.value) {
                    return;
                }

                const price = Number(option.dataset.price || 0);
                const available = Number(option.dataset.available || 0);
                total += price;

                const row = document.createElement('tr');
                row.className = 'border-t border-gray-100';
                [option.dataset.name, available >= 1 ? `${available} available` : 'Out of stock', moneyFormatter.format(price)].forEach((value, index) => {
                    const cell = document.createElement('td');
                    cell.className = `px-4 py-3 ${index === 2 ? 'text-right font-bold text-purple-800' : 'text-gray-700'}`;
                    cell.textContent = value;
                    row.appendChild(cell);
                });
                rows.push(row);
            });

            summaryBody.replaceChildren();
            if (rows.length === 0) {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 3;
                cell.className = 'px-4 py-6 text-center text-gray-500';
                cell.textContent = 'Select components to update your build.';
                row.appendChild(cell);
                summaryBody.appendChild(row);
            } else {
                rows.forEach((row) => summaryBody.appendChild(row));
            }
            totalEl.textContent = moneyFormatter.format(total);
        }

        componentSelects.forEach((select) => select.addEventListener('change', updateBuildSummary));
        updateBuildSummary();
    </script>
</x-app-layout>
