<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-700">Custom configuration</p>
                <h1 class="mt-1 text-2xl font-black text-gray-900">Build your PC</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600">
                Back to dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl space-y-0 px-4 sm:px-6 lg:px-8">
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

            <div class="grid gap-0 lg:grid-cols-[minmax(0,1.6fr)_minmax(20rem,1fr)] lg:items-start">
                <form id="build-pc-form" method="POST" action="{{ route('buildpc.store') }}" class="min-w-0 space-y-0">
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
                        <div>
                            <span class="block text-sm font-bold uppercase tracking-wide text-purple-800">Estimated receipt</span>
                            <span class="mt-1 block text-xs text-purple-700">Catalog prices include 12% Philippine VAT.</span>
                        </div>
                        <span id="build-total" class="text-2xl font-black text-purple-800">₱0.00</span>
                    </div>
                    <dl class="mt-3 space-y-2 px-1 text-sm">
                        <div class="flex justify-between gap-4 text-gray-600"><dt>Amount before VAT</dt><dd id="build-net">₱0.00</dd></div>
                        <div class="flex justify-between gap-4 text-gray-600"><dt>VAT (12%, included)</dt><dd id="build-vat">₱0.00</dd></div>
                    </dl>
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button type="submit" class="inline-flex justify-center rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800">Save Build</button>
                        <button type="reset" class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm transition hover:border-gray-400">Reset</button>
                    </div>
                </section>
                </form>

                <section class="self-start overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 bg-gray-50/70 p-4">
                        <div class="min-w-0">
                            <h2 class="text-base font-bold leading-snug text-gray-900">Customer Build / PC Build Management</h2>
                            <p class="mt-1 text-xs text-gray-500">Review builds, update status, and add progress photos.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-bold text-purple-800">{{ count($customerBuildManagement) }}</span>
                            <a href="{{ route('buildpc.customer') }}" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">Customer page</a>
                        </div>
                    </div>
                    <div class="space-y-3 p-4">
                        @forelse ($customerBuildManagement as $build)
                            <article class="rounded-2xl border border-gray-200 bg-gray-50/60 p-3">
                                <div class="flex flex-col gap-3">
                                    <div class="grid min-w-0 gap-2 sm:grid-cols-2">
                                        <div class="rounded-xl border border-gray-200 bg-white p-3 sm:col-span-2">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <h3 class="text-sm font-extrabold text-gray-900">{{ $build->build_number }}</h3>
                                                <span class="rounded-full bg-purple-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-purple-800">{{ ucfirst($build->status) }}</span>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">Build request</p>
                                        </div>

                                        <div class="rounded-xl border border-gray-200 bg-white p-3">
                                            <p class="text-[10px] font-bold uppercase tracking-wide text-gray-400">Customer</p>
                                            <p class="mt-1 break-words text-sm font-semibold text-gray-800">{{ $build->customer_name }}</p>
                                            @if ($build->user?->email)
                                                <p class="mt-1 break-all text-xs text-gray-500">{{ $build->user->email }}</p>
                                            @endif
                                        </div>

                                        <div class="rounded-xl border border-gray-200 bg-white p-3">
                                            <p class="text-[10px] font-bold uppercase tracking-wide text-gray-400">Build date</p>
                                            <p class="mt-1 text-sm font-semibold text-gray-800">{{ $build->created_at->format('M j, Y') }}</p>
                                            <p class="mt-2 text-xs font-bold text-purple-800">
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

                                        @if ($build->notes)
                                            <p class="whitespace-pre-line rounded-xl border border-purple-100 bg-purple-50 p-3 text-xs text-gray-700 sm:col-span-2">{{ $build->notes }}</p>
                                        @endif

                                        <ul class="grid gap-1 rounded-xl border border-gray-200 bg-white p-3 text-xs text-gray-700 sm:col-span-2 sm:grid-cols-2">
                                            @foreach ($build->items as $item)
                                                <li class="rounded-lg bg-gray-50 px-2.5 py-2">{{ $item->product->category }}: {{ $item->product->name }} × {{ $item->quantity }}</li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    @if ($build->status === 'pending')
                                        <form method="POST" action="{{ route('dashboard.customer-builds.update', $build) }}" class="w-full" onsubmit="const button = this.querySelector('button[type=submit]'); button.disabled = true; button.textContent = 'Accepting...';">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">Accept</button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('dashboard.customer-builds.update', $build) }}" enctype="multipart/form-data" class="w-full space-y-3 rounded-xl border border-gray-200 bg-white p-3">
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
                                            <button type="submit" class="rounded-lg bg-purple-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-purple-800">Save details</button>
                                        </div>
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Component specifications and notes
                                            <textarea name="notes" rows="3" maxlength="2000" class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-sm font-normal shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ $build->notes }}</textarea>
                                        </label>
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
                                                    <div id="{{ $previewId }}" class="photo-preview mb-2 flex aspect-square items-center justify-center overflow-hidden rounded-lg border border-dashed border-gray-300 bg-white text-xs text-gray-400">
                                                    @if ($path)
                                                        <img src="{{ asset('storage/'.$path) }}" alt="{{ $label }} for {{ $build->build_number }}" class="h-full w-full object-cover">
                                                    @else
                                                        <span>No photo</span>
                                                    @endif
                                                    </div>
                                                    <label class="flex cursor-pointer items-center justify-center gap-1 rounded-lg border border-gray-300 bg-white px-2 py-2 text-center text-xs font-bold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">
                                                        <span aria-hidden="true" class="text-base leading-none">+</span> Add Photo
                                                        <input type="file" name="{{ $field }}" accept=".jpg,.jpeg,.png" class="build-photo-input sr-only" data-preview="{{ $previewId }}">
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
        document.querySelectorAll('.build-photo-input').forEach((input) => {
            input.addEventListener('change', () => {
                const file = input.files?.[0];
                const preview = document.getElementById(input.dataset.preview);
                if (!file || !preview) {
                    return;
                }

                if (input.dataset.previewUrl) {
                    URL.revokeObjectURL(input.dataset.previewUrl);
                }

                const imageUrl = URL.createObjectURL(file);
                input.dataset.previewUrl = imageUrl;

                const image = document.createElement('img');
                image.src = imageUrl;
                image.alt = 'Selected photo preview';
                image.className = 'h-full w-full object-cover';
                preview.replaceChildren(image);
            });
        });

        const summaryBody = document.getElementById('build-summary-body');
        const totalEl = document.getElementById('build-total');
        const netEl = document.getElementById('build-net');
        const vatEl = document.getElementById('build-vat');
        const componentSelects = document.querySelectorAll('.component-select');
        const moneyFormatter = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
        const vatRate = {{ \App\Support\PhilippineVat::RATE_PERCENT }};

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
            const vat = Math.round(total * vatRate / (100 + vatRate) * 100) / 100;
            netEl.textContent = moneyFormatter.format(total - vat);
            vatEl.textContent = moneyFormatter.format(vat);
        }

        componentSelects.forEach((select) => select.addEventListener('change', updateBuildSummary));
        document.getElementById('build-pc-form').addEventListener('reset', () => {
            window.setTimeout(updateBuildSummary, 0);
        });
        updateBuildSummary();
    </script>
</x-app-layout>
