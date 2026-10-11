<x-app-layout>
    @php
        /** @var \Illuminate\Support\ViewErrorBag $errors */
    @endphp

    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-600">Your selections</p>
            <h2 class="mt-1 text-2xl font-black text-gray-900">{{ __('Your Cart') }}</h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (count($items) === 0)
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
                    <h3 class="text-xl font-bold text-gray-900">Your cart is empty</h3>
                    <p class="mt-2 text-sm text-gray-500">Browse the shop and add products to get started.</p>
                    <a href="{{ route('store.accessories') }}" class="mt-5 inline-flex rounded-lg bg-purple-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-purple-800">
                        Browse products
                    </a>
                </div>
            @else
                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
                    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4">
                            <h3 class="font-bold text-gray-900">Order items</h3>
                        </div>
                        <div class="divide-y divide-gray-100 px-5">
                            @foreach ($items as $item)
                                <article class="flex flex-col gap-4 py-5 sm:flex-row sm:items-center">
                                    @if ($item['product']->image_path)
                                        <img src="{{ asset('storage/'.$item['product']->image_path) }}" alt="{{ $item['product']->name }}" class="h-24 w-24 shrink-0 rounded-xl bg-gray-100 object-cover">
                                    @else
                                        <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-3xl font-light text-purple-500" aria-label="No product photo available">＋</div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-gray-900">{{ $item['product']->name }}</h4>
                                        <p class="mt-1 text-sm text-gray-500">₱{{ number_format($item['product']->price, 2) }} each</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $item['available_stock'] }} available</p>
                                        @if (! $item['product']->is_active || $item['available_stock'] < 1)
                                            <p class="mt-1 text-sm text-red-600">Currently unavailable. Remove this item to continue.</p>
                                        @elseif ($item['quantity'] > $item['available_stock'])
                                            <p class="mt-1 text-sm text-amber-700">Only {{ $item['available_stock'] }} available; this cart item cannot be ordered.</p>
                                        @endif
                                    </div>
                                    <form x-data="{ quantity: {{ $item['quantity'] }}, available: {{ $item['available_stock'] }} }" x-ref="quantityForm" method="POST" action="{{ route('cart.items.update', $item['product']) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="quantity" x-model="quantity">
                                        <button type="button"
                                                aria-label="Decrease quantity of {{ $item['product']->name }}"
                                                @click="quantity = Math.max(1, Math.min(available, quantity - 1)); $nextTick(() => $refs.quantityForm.requestSubmit())"
                                                :disabled="quantity <= 1 || available < 1"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">−</button>
                                        <span x-text="quantity" aria-live="polite" class="min-w-6 text-center text-sm font-semibold text-gray-900">{{ $item['quantity'] }}</span>
                                        <button type="button"
                                                aria-label="Increase quantity of {{ $item['product']->name }}"
                                                @click="quantity = Math.min(available, quantity + 1); $nextTick(() => $refs.quantityForm.requestSubmit())"
                                                :disabled="quantity >= available"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">+</button>
                                    </form>
                                    <div class="sm:text-right">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Subtotal</p>
                                        <p class="mt-1 font-black text-gray-900">₱{{ number_format($item['line_total'], 2) }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('cart.items.destroy', $item['product']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Remove {{ $item['product']->name }} from cart" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                                            Remove
                                        </button>
                                    </form>
                                </article>
                            @endforeach
                        </div>
                    </section>

                    <aside class="h-fit rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h3 class="text-lg font-black uppercase tracking-wide text-gray-900">Cart Totals</h3>
                        <div class="mt-5 flex justify-between border-b border-gray-100 pb-4 text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-900">₱{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="mt-5 inline-flex w-full justify-center rounded-xl bg-purple-700 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            Proceed to checkout
                        </a>
                        <p class="mt-3 text-center text-xs leading-relaxed text-gray-500">Review your shipping and billing details before placing the order.</p>
                    </aside>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
