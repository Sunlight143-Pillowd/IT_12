<x-app-layout>
    <x-slot name="header">
<<<<<<< HEAD
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-600">Your selections</p>
            <h2 class="mt-1 text-2xl font-black text-gray-900">{{ __('Your Cart') }}</h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
=======
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Cart') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
<<<<<<< HEAD
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
=======
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($items->isEmpty())
<<<<<<< HEAD
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
                    <h3 class="text-xl font-bold text-gray-900">Your cart is empty</h3>
                    <p class="mt-2 text-sm text-gray-500">Browse the shop and add products to get started.</p>
                    <a href="{{ route('store.accessories') }}" class="mt-5 inline-flex rounded-lg bg-purple-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-purple-800">
=======
                <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                    <p class="text-gray-600">Your cart is empty.</p>
                    <a href="{{ route('store.accessories') }}" class="mt-4 inline-flex rounded-md bg-purple-600 px-5 py-3 text-sm font-semibold text-white hover:bg-purple-700">
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                        Browse products
                    </a>
                </div>
            @else
<<<<<<< HEAD
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
                        <h3 class="text-lg font-black text-gray-900">Checkout</h3>
                        <div class="mt-4 space-y-2 border-b border-gray-100 pb-4 text-sm">
                            <div class="flex justify-between gap-4 text-gray-600">
                                <span>Subtotal</span>
                                <span>₱{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between gap-4 text-base font-black text-gray-900">
                                <span>Total</span>
                                <span>₱{{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('cart.order') }}" class="mt-5 space-y-5" x-data="{ fulfillment: '{{ old('fulfillment_method', 'pickup') }}' }">
                            @csrf
                            <fieldset class="space-y-3">
                                <legend class="text-sm font-bold text-gray-900">Customer information</legend>
                                <label class="block text-sm font-medium text-gray-700">
                                    Full name
                                    <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" maxlength="255" required autocomplete="name"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>
                                <label class="block text-sm font-medium text-gray-700">
                                    Email
                                    <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" maxlength="255" required autocomplete="email"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>
                                <label class="block text-sm font-medium text-gray-700">
                                    Contact number <span class="font-normal text-gray-400">(optional)</span>
                                    <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" maxlength="40" autocomplete="tel"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>
                            </fieldset>

                            <fieldset class="space-y-3">
                                <legend class="text-sm font-bold text-gray-900">Delivery or pickup</legend>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach (['pickup' => 'Store pickup', 'delivery' => 'Delivery'] as $value => $label)
                                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-3 py-3 text-sm font-semibold text-gray-700 has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50 has-[:checked]:text-purple-800">
                                            <input type="radio" name="fulfillment_method" value="{{ $value }}" x-model="fulfillment" @checked(old('fulfillment_method', 'pickup') === $value) class="border-gray-300 text-purple-700 focus:ring-purple-500">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                                <label x-cloak x-show="fulfillment === 'delivery'" class="block text-sm font-medium text-gray-700">
                                    Delivery address
                                    <textarea name="delivery_address" rows="3" maxlength="2000" :required="fulfillment === 'delivery'" autocomplete="street-address"
                                              class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('delivery_address') }}</textarea>
                                </label>
                            </fieldset>

                            <fieldset class="space-y-3">
                                <legend class="text-sm font-bold text-gray-900">Mode of payment</legend>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach (['cash' => 'Cash', 'gcash' => 'GCash', 'bank_transfer' => 'Bank transfer', 'other' => 'Other'] as $value => $label)
                                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-3 py-3 text-sm font-semibold text-gray-700 has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50 has-[:checked]:text-purple-800">
                                            <input type="radio" name="payment_method" value="{{ $value }}" @checked(old('payment_method', 'cash') === $value) class="border-gray-300 text-purple-700 focus:ring-purple-500">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500">Payment is recorded as a preference only; online payment is not yet enabled.</p>
                            </fieldset>

                            <button type="submit" class="w-full rounded-xl bg-purple-700 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                Place order
                            </button>
                            <p class="text-center text-xs leading-relaxed text-gray-500">Your order will be saved as pending until staff confirms it. Stock is deducted after acceptance.</p>
                        </form>
                    </aside>
=======
                <div class="divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white px-4 sm:px-6">
                    @foreach ($items as $item)
                        <article class="flex flex-col gap-4 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $item['product']->name }}</h3>
                                <p class="mt-1 text-sm text-gray-600">₱{{ number_format($item['product']->price, 0) }} each</p>
                                @if (!$item['product']->is_active || $item['available_stock'] < 1)
                                    <p class="mt-1 text-sm text-red-600">Currently unavailable. Remove this item to continue.</p>
                                @elseif ($item['quantity'] > $item['available_stock'])
                                    <p class="mt-1 text-sm text-amber-700">Only {{ $item['available_stock'] }} available. Update the quantity to continue.</p>
                                @else
                                    <p class="mt-1 text-sm text-gray-500">{{ $item['available_stock'] }} available</p>
                                @endif
                            </div>

                            @if ($item['product']->is_active && $item['available_stock'] > 0)
                                <form method="POST" action="{{ route('cart.items.update', $item['product']) }}" class="flex items-end gap-2">
                                    @csrf
                                    @method('PUT')
                                    <label class="text-xs font-medium text-gray-700">
                                        Quantity
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['available_stock'] }}" required
                                               class="mt-1 block w-20 rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    </label>
                                    <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                        Update
                                    </button>
                                </form>
                            @endif

                            <p class="text-right font-bold text-gray-900">₱{{ number_format($item['line_total'], 0) }}</p>

                            <form method="POST" action="{{ route('cart.items.destroy', $item['product']) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700">
                                    Remove
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end">
                    <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white p-5">
                        <div class="flex justify-between gap-4 text-base font-bold text-gray-900">
                            <span>Subtotal</span>
                            <span>₱{{ number_format($subtotal, 0) }}</span>
                        </div>
                        <form method="POST" action="{{ route('cart.order') }}" class="mt-5 space-y-4">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700">
                                Name
                                <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" maxlength="255" required
                                       class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </label>
                            <label class="block text-sm font-medium text-gray-700">
                                Email
                                <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" maxlength="255" required
                                       class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </label>
                            <button type="submit" class="w-full rounded-md bg-purple-600 px-5 py-3 text-sm font-semibold text-white hover:bg-purple-700">
                                Place order
                            </button>
                            <p class="text-xs text-gray-500">Your order will be saved as pending until staff confirms it. Stock is not deducted yet.</p>
                        </form>
                    </div>
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
