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
                        <form method="POST" action="{{ route('cart.order') }}" class="mt-5 space-y-5"
                              x-data="{
                                  shippingChoice: @js(old('shipping_zone', old('fulfillment_method', 'pickup') === 'delivery' ? 'davao_city' : 'pickup')),
                                  distanceKm: @js(old('shipping_distance_km', '')),
                                  subtotal: @js((float) $subtotal),
                                  calculated: false,
                                  shippingFee() {
                                      if (this.shippingChoice === 'pickup') return 0;
                                      if (this.shippingChoice === 'outside_davao') return null;
                                      const distance = Number(this.distanceKm);
                                      if (!Number.isFinite(distance) || distance <= 0) return null;
                                      return 79 + (Math.ceil(Math.max(0, distance - 4)) * 15);
                                  },
                                  calculateShipping() {
                                      this.calculated = true;
                                  }
                              }">
                            @csrf
                            <input type="hidden" name="fulfillment_method" :value="shippingChoice === 'pickup' ? 'pickup' : 'delivery'">
                            <input type="hidden" name="shipping_zone" :value="shippingChoice === 'pickup' ? '' : shippingChoice">

                            <div class="space-y-2 border-b border-gray-100 pb-4 text-sm">
                                <div class="flex justify-between gap-4 text-gray-600">
                                    <span>Subtotal</span>
                                    <span>₱{{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between gap-4 text-gray-600">
                                    <span>Shipping</span>
                                    <span>
                                        <span x-show="shippingChoice === 'pickup'">₱0.00</span>
                                        <span x-cloak x-show="shippingChoice === 'davao_city' && !calculated">Calculate below</span>
                                        <span x-cloak x-show="shippingChoice === 'davao_city' && calculated && shippingFee()" x-text="'₱' + Number(shippingFee()).toFixed(2)"></span>
                                        <span x-cloak x-show="shippingChoice === 'outside_davao'">To be confirmed</span>
                                    </span>
                                </div>
                                <div class="flex justify-between gap-4 border-t border-gray-100 pt-3 text-base font-black text-gray-900">
                                    <span x-text="shippingChoice === 'outside_davao' ? 'Subtotal (shipping pending)' : 'Total'">Total</span>
                                    <span x-text="'₱' + (subtotal + (shippingChoice === 'outside_davao' || !calculated && shippingChoice === 'davao_city' ? 0 : (shippingFee() || 0))).toFixed(2)">₱{{ number_format($subtotal, 2) }}</span>
                                </div>
                            </div>

                            <fieldset class="space-y-3">
                                <legend class="text-sm font-bold text-gray-900">Shipping options</legend>
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 text-sm text-gray-700 has-checked:border-purple-600 has-checked:bg-purple-50">
                                    <input type="radio" value="davao_city" x-model="shippingChoice" @change="calculated = false" class="mt-0.5 border-gray-300 text-purple-700 focus:ring-purple-500">
                                    <span><span class="block font-semibold">Davao City rider</span><span class="text-xs text-gray-500">₱79 for the first 4 km, then ₱15 per succeeding km.</span></span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 text-sm text-gray-700 has-checked:border-purple-600 has-checked:bg-purple-50">
                                    <input type="radio" value="outside_davao" x-model="shippingChoice" @change="calculated = false" class="mt-0.5 border-gray-300 text-purple-700 focus:ring-purple-500">
                                    <span><span class="block font-semibold">Outside Davao City</span><span class="text-xs text-gray-500">Fee depends on location, item size, and weight. Staff will confirm it before accepting your order.</span></span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 text-sm text-gray-700 has-checked:border-purple-600 has-checked:bg-purple-50">
                                    <input type="radio" value="pickup" x-model="shippingChoice" @change="calculated = false" class="mt-0.5 border-gray-300 text-purple-700 focus:ring-purple-500">
                                    <span><span class="block font-semibold">Store pickup</span><span class="text-xs text-gray-500">No shipping fee.</span></span>
                                </label>
                            </fieldset>

                            <label x-cloak x-show="shippingChoice === 'davao_city'" class="block text-sm font-medium text-gray-700">
                                Distance from store (km)
                                <input type="number" name="shipping_distance_km" x-model="distanceKm" @input="calculated = false" min="0.1" max="1000" step="0.1" :required="shippingChoice === 'davao_city'"
                                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </label>
                            <button type="button" @click="calculateShipping()" class="w-full rounded-lg border border-purple-700 px-4 py-2.5 text-sm font-semibold text-purple-700 transition hover:bg-purple-50">
                                Calculate shipping
                            </button>
                            <p x-cloak x-show="shippingChoice === 'davao_city' && calculated && !shippingFee()" class="text-xs text-red-600" role="alert">Enter a valid distance to calculate shipping.</p>

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

                            <label x-cloak x-show="shippingChoice !== 'pickup'" class="block text-sm font-medium text-gray-700">
                                Delivery address
                                <textarea name="delivery_address" rows="3" maxlength="2000" :required="shippingChoice !== 'pickup'" autocomplete="street-address"
                                          class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('delivery_address') }}</textarea>
                            </label>

                            <fieldset class="space-y-3">
                                <label class="block text-sm font-bold text-gray-900">
                                    Pay As
                                    <select name="payment_method" class="mt-1 block w-full rounded-lg border-gray-300 text-sm font-normal shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        @foreach (['cash' => 'Cash', 'gcash' => 'GCash', 'bank_transfer' => 'Bank transfer', 'other' => 'Other'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('payment_method', 'cash') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <p class="text-xs text-gray-500">Payment is recorded as a preference only; online payment is not yet enabled.</p>
                            </fieldset>

                            <button type="submit" class="w-full rounded-xl bg-purple-700 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                Place order
                            </button>
                            <p class="text-center text-xs leading-relaxed text-gray-500">Your order will be saved as pending until staff confirms it. Stock is deducted after acceptance.</p>
                        </form>
                    </aside>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
