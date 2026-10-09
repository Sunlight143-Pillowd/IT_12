<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-700">Custom configuration</p>
                <h1 class="mt-1 text-2xl font-black text-gray-900">Build your PC</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-purple-700 hover:underline">Back to dashboard</a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl space-y-7 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
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

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.6fr)_minmax(20rem,1fr)] lg:items-start">
                <form id="build-pc-form" method="POST" action="{{ route('buildpc.store') }}" class="min-w-0 space-y-5">
                @csrf

                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="customer_name" class="mb-2 block text-sm font-bold text-gray-800">Customer name</label>
                            <input id="customer_name" name="customer_name" type="text" required value="{{ old('customer_name', auth()->user()?->name ?? '') }}" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="John Doe">
                        </div>
                        <div>
                            <label for="customer_email" class="mb-2 block text-sm font-bold text-gray-800">Customer email</label>
                            <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email', auth()->user()?->email ?? '') }}" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="customer@example.com">
                        </div>
                    </div>
                    <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-gray-900">Choose components</h2>
                            <p class="mt-1 text-sm text-gray-500">Select available parts and review the estimated total before saving.</p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($componentGroups as $type => $label)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <label for="component-{{ $type }}" class="mb-2 block text-sm font-bold text-gray-800">{{ $label }}</label>
                                <select id="component-{{ $type }}" name="items[{{ $type }}][product_id]" data-key="{{ $type }}" class="component-select w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">Select {{ $label }}</option>
                                    @foreach ($groupedProducts[$type] ?? [] as $product)
                                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-available="{{ $product->availableStock() }}">
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
                            <h2 class="text-lg font-black text-gray-900">Selected components</h2>
                            <p class="mt-1 text-sm text-gray-500">Review the selected parts and their current availability.</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-500">Build #: {{ 'PC-' . str_pad((string) (count($builds) + 1), 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                <tr><th class="px-4 py-3">Component</th><th class="px-4 py-3">Availability</th><th class="px-4 py-3 text-right">Price</th></tr>
                            </thead>
                            <tbody id="build-summary-body">
                                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">Select components to start your build.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex items-center justify-between rounded-xl bg-purple-50 px-4 py-4">
                        <span class="text-sm font-bold uppercase tracking-wide text-purple-800">Estimated total</span>
                        <span id="build-total" class="text-2xl font-black text-purple-800">₱0.00</span>
                    </div>
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button type="submit" class="inline-flex justify-center rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800">Save Build</button>
                        <button type="reset" class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm transition hover:border-gray-400">Reset</button>
                    </div>
                </section>
                </form>

                <section class="flex min-h-0 flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:sticky lg:top-6 lg:max-h-[calc(100vh-3rem)]">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-5">
                        <div>
                            <h2 class="text-xl font-black text-gray-900">Customer Build / PC Build Management</h2>
                            <p class="mt-1 text-sm text-gray-500">Review components, update the build workflow, and attach progress photos.</p>
                        </div>
                        <a href="{{ route('buildpc.customer') }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">Customer build page</a>
                    </div>
                    <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
                        @forelse ($customerBuildManagement as $build)
                            <article class="rounded-xl border border-gray-200 p-4">
                                <div class="flex flex-col gap-4">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-lg font-black text-gray-900">{{ $build->build_number }}</h3>
                                            <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold uppercase text-purple-800">{{ ucfirst($build->status) }}</span>
                                        </div>
                                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $build->customer_name }}</p>
                                        <p class="text-xs text-gray-500">Build date: {{ $build->created_at->format('M j, Y') }}</p>
                                        <ul class="mt-3 grid gap-x-6 gap-y-1 text-sm text-gray-700 sm:grid-cols-2">
                                            @foreach ($build->items as $item)
                                                <li>{{ $item->product->category }}: {{ $item->product->name }} × {{ $item->quantity }}</li>
                                            @endforeach
                                        </ul>
                                        <p class="mt-3 font-black text-gray-900">Total price: ₱{{ number_format($build->total_cost, 2) }}</p>
                                    </div>

                                    <form method="POST" action="{{ route('dashboard.customer-builds.update', $build) }}" enctype="multipart/form-data" class="w-full space-y-4 rounded-xl bg-gray-50 p-4">
                                        @csrf
                                        @method('PATCH')
                                        <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">
                                            <label class="block text-sm font-semibold text-gray-700">
                                                Build status
                                                <select name="status" class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-purple-500 focus:ring-purple-500">
                                                    <option value="{{ $build->status }}">{{ ucfirst($build->status) }} (current)</option>
                                                    @foreach ($buildStatusOptions[$build->status] ?? [] as $nextStatus)
                                                        <option value="{{ $nextStatus }}">{{ ucfirst($nextStatus) }}</option>
                                                    @endforeach
                                                </select>
                                            </label>
                                            <button type="submit" class="rounded-lg bg-purple-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-purple-800">Save updates</button>
                                        </div>
                                        <div class="grid grid-cols-3 gap-3">
                                            @foreach ([
                                                'product_photo' => ['Product photo', $build->product_photo_path],
                                                'before_photo' => ['Before photo', $build->before_photo_path],
                                                'after_photo' => ['After photo', $build->after_photo_path],
                                            ] as $field => [$label, $path])
                                                @php
                                                    $previewId = 'build-'.$build->id.'-'.$field;
                                                @endphp
                                                <div class="min-w-0">
                                                    <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-gray-500">{{ $label }}</p>
                                                    @if ($path)
                                                        <img id="{{ $previewId }}" src="{{ asset('storage/'.$path) }}" alt="{{ $label }} for {{ $build->build_number }}" class="mb-2 aspect-square w-full rounded-lg bg-white object-cover">
                                                    @else
                                                        <div id="{{ $previewId }}" class="mb-2 flex aspect-square items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white text-xs text-gray-400">No photo</div>
                                                    @endif
                                                    <label class="flex cursor-pointer items-center justify-center gap-1 rounded-lg border border-gray-300 bg-white px-2 py-2 text-center text-xs font-bold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">
                                                        <span aria-hidden="true" class="text-base leading-none">+</span> Add Photo
                                                        <input type="file" name="{{ $field }}" accept="image/jpeg,image/png,image/webp" class="build-photo-input sr-only" data-preview="{{ $previewId }}">
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">No customer PC builds have been submitted.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-black text-gray-900">Saved Builds</h3>
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">{{ $builds->count() }} build(s)</span>
                </div>

                @forelse ($builds as $build)
                    <article class="mb-4 rounded-xl border border-gray-200 p-4 last:mb-0">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Build ID · {{ $build->build_number }}</p>
                                <h4 class="mt-1 text-lg font-bold text-gray-900">{{ $build->customer_name ?: 'Customer not recorded' }}</h4>
                                <p class="text-sm text-gray-500">{{ $build->created_at->format('M j, Y') }}</p>
                            </div>
                            <span class="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800">{{ ucfirst($build->status) }}</span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('buildpc.show', $build) }}" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">View</a>
                            <a href="{{ route('buildpc.print', $build) }}" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Print</a>
                            <a href="{{ route('buildpc.edit', $build) }}" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Edit</a>
                            <form action="{{ route('buildpc.destroy', $build) }}" method="POST" onsubmit="return confirm('Delete this build?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center rounded border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-red-700 transition hover:border-red-300 hover:bg-red-100">Delete</button>
                            </form>
                            <form action="{{ route('buildpc.cancel', $build) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-amber-700 transition hover:border-amber-300 hover:bg-amber-100">Cancel order</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">No builds yet.</div>
                @endforelse
            </section>

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
                cell.textContent = 'Select components to start your build.';
                row.appendChild(cell);
                summaryBody.appendChild(row);
            } else {
                rows.forEach((row) => summaryBody.appendChild(row));
            }
            totalEl.textContent = moneyFormatter.format(total);
        }

        componentSelects.forEach((select) => select.addEventListener('change', updateBuildSummary));
        document.getElementById('build-pc-form').addEventListener('reset', () => {
            window.setTimeout(updateBuildSummary, 0);
        });
        updateBuildSummary();
    </script>
</x-app-layout>
