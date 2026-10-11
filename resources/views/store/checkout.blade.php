<x-app-layout>
    @php
        $customerNameParts = explode(' ', trim((string) auth()->user()?->name), 2);
    @endphp

    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-600">Order details</p>
            <h2 class="mt-1 text-2xl font-black text-gray-900">Checkout</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('checkout.place-order') }}"
                  x-data="{
                      shippingChoice: @js(old('shipping_zone', old('fulfillment_method', 'pickup') === 'delivery' ? 'davao_city' : 'pickup')),
                      paymentMethod: @js(old('payment_method', 'cash')),
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
                  }"
                  x-effect="if (shippingChoice === 'outside_davao' && paymentMethod === 'cash') paymentMethod = 'gcash'">
                @csrf
                <input type="hidden" name="fulfillment_method" :value="shippingChoice === 'pickup' ? 'pickup' : 'delivery'">
                <input type="hidden" name="shipping_zone" :value="shippingChoice === 'pickup' ? '' : shippingChoice">

                <div class="grid gap-8 lg:grid-cols-2">
                    <section class="space-y-6">
                        <div>
                            <h3 class="mb-3 text-2xl font-black text-gray-900">Your order</h3>
                            <div class="border border-gray-200 bg-white p-5 sm:p-7">
                                <div class="flex justify-between border-b border-gray-200 pb-4 text-xs font-bold uppercase tracking-wide text-gray-600">
                                    <span>Product</span>
                                    <span>Subtotal</span>
                                </div>
                                <div class="divide-y divide-gray-100">
                                    @foreach ($items as $item)
                                        <div class="flex items-center justify-between gap-4 py-4 text-sm">
                                            <div class="flex min-w-0 items-center gap-3">
                                                @if ($item['product']->image_path)
                                                    <img src="{{ asset('storage/'.$item['product']->image_path) }}" alt="" class="h-14 w-14 shrink-0 rounded-lg bg-gray-100 object-contain">
                                                @endif
                                                <span class="min-w-0 font-medium text-gray-800">
                                                    {{ $item['product']->name }} <span class="text-gray-500">× {{ $item['quantity'] }}</span>
                                                </span>
                                            </div>
                                            <span class="shrink-0 font-semibold text-gray-900">₱{{ number_format($item['line_total'], 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="space-y-3 border-t border-gray-200 pt-4 text-sm">
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
                                    <div class="flex justify-between gap-4 border-t border-gray-200 pt-3 text-base font-black text-gray-900">
                                        <span x-text="shippingChoice === 'outside_davao' ? 'Total before shipping' : 'Total'">Total</span>
                                        <span x-text="'₱' + (subtotal + (shippingChoice === 'outside_davao' || !calculated && shippingChoice === 'davao_city' ? 0 : (shippingFee() || 0))).toFixed(2)">₱{{ number_format($subtotal, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <fieldset class="space-y-3">
                            <legend class="text-xl font-bold text-gray-900">Shipping</legend>
                            <label class="flex cursor-pointer items-start gap-3 border border-gray-200 bg-white p-4 text-sm text-gray-700 has-checked:border-purple-600 has-checked:bg-purple-50">
                                <input type="radio" value="davao_city" x-model="shippingChoice" @change="calculated = false" class="mt-0.5 border-gray-300 text-purple-700 focus:ring-purple-500">
                                <span><span class="block font-semibold">Davao City rider</span><span class="text-xs text-gray-500">₱79 for the first 4 km, then ₱15 per succeeding km.</span></span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 border border-gray-200 bg-white p-4 text-sm text-gray-700 has-checked:border-purple-600 has-checked:bg-purple-50">
                                <input type="radio" value="outside_davao" x-model="shippingChoice" @change="calculated = false" class="mt-0.5 border-gray-300 text-purple-700 focus:ring-purple-500">
                                <span><span class="block font-semibold">Outside Davao City</span><span class="text-xs text-gray-500">Staff will confirm shipping based on your location and items.</span></span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 border border-gray-200 bg-white p-4 text-sm text-gray-700 has-checked:border-purple-600 has-checked:bg-purple-50">
                                <input type="radio" value="pickup" x-model="shippingChoice" @change="calculated = false" class="mt-0.5 border-gray-300 text-purple-700 focus:ring-purple-500">
                                <span><span class="block font-semibold">Store pickup</span><span class="text-xs text-gray-500">No shipping fee.</span></span>
                            </label>
                        </fieldset>

                        <div x-cloak x-show="shippingChoice === 'davao_city'" class="space-y-3 rounded-xl border border-gray-200 bg-white p-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Distance from store (km)
                                <input type="number" name="shipping_distance_km" x-model="distanceKm" @input="calculated = false" min="0.1" max="1000" step="0.1" :required="shippingChoice === 'davao_city'"
                                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </label>
                            <button type="button" @click="calculateShipping()" class="w-full rounded-lg border border-purple-700 px-4 py-2.5 text-sm font-semibold text-purple-700 transition hover:bg-purple-50">
                                Calculate shipping
                            </button>
                            <p x-cloak x-show="calculated && !shippingFee()" class="text-xs text-red-600" role="alert">Enter a valid distance to calculate shipping.</p>
                        </div>
                    </section>

                    <section>
                        <h3 class="mb-3 text-2xl font-black text-gray-900">Customer details</h3>
                        <div class="space-y-5 border border-gray-200 bg-white p-5 sm:p-7">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    First name <span class="text-red-600">*</span>
                                    <input type="text" name="first_name" value="{{ old('first_name', $customerNameParts[0] ?? '') }}" maxlength="128" required autocomplete="given-name"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>
                                <label class="block text-sm font-medium text-gray-700">
                                    Last name <span class="text-red-600">*</span>
                                    <input type="text" name="last_name" value="{{ old('last_name', $customerNameParts[1] ?? '') }}" maxlength="128" required autocomplete="family-name"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>
                            </div>

                            <label class="block text-sm font-medium text-gray-700">
                                Company name <span class="font-normal text-gray-400">(optional)</span>
                                <input type="text" name="customer_company" value="{{ old('customer_company') }}" maxlength="255" autocomplete="organization"
                                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </label>

                            <div x-cloak x-show="shippingChoice === 'pickup'" x-transition class="space-y-2 border-t border-gray-100 pt-5">
                                <h4 class="text-lg font-bold text-gray-900">Pickup location</h4>
                                <p class="text-sm text-gray-700">{{ config('store.name') }}</p>
                                <p class="text-sm text-gray-600">{{ config('store.address') }}</p>
                            </div>

                            <div x-cloak x-show="shippingChoice !== 'pickup'" x-transition class="space-y-5 border-t border-gray-100 pt-5">
                                <h4 class="text-lg font-bold text-gray-900">Shipping address</h4>
                                <p class="text-sm font-medium text-gray-700">Country / Region <span class="text-red-600">*</span><span class="mt-1 block font-normal">Philippines</span></p>

                                <label class="block text-sm font-medium text-gray-700">
                                    Street address <span class="text-red-600">*</span>
                                    <input type="text" name="address_line_1" value="{{ old('address_line_1') }}" maxlength="255" :required="shippingChoice !== 'pickup'" autocomplete="address-line1" placeholder="House number and street name"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>
                                <label class="block text-sm font-medium text-gray-700">
                                    Apartment, suite, unit, etc. <span class="font-normal text-gray-400">(optional)</span>
                                    <input type="text" name="address_line_2" value="{{ old('address_line_2') }}" maxlength="255" autocomplete="address-line2"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Town / City <span class="text-red-600">*</span>
                                        <input type="text" name="address_city" value="{{ old('address_city') }}" maxlength="255" :required="shippingChoice !== 'pickup'" autocomplete="address-level2"
                                               class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    </label>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Province <span class="font-normal text-gray-400">(optional)</span>
                                        <input type="text" name="address_province" value="{{ old('address_province') }}" maxlength="255" autocomplete="address-level1"
                                               class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    </label>
                                </div>

                                <label class="block text-sm font-medium text-gray-700">
                                    Postal code <span class="font-normal text-gray-400">(optional)</span>
                                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" maxlength="20" autocomplete="postal-code"
                                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </label>
                            </div>

                            <label class="block text-sm font-medium text-gray-700">
                                Phone <span class="text-red-600">*</span>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" maxlength="40" required autocomplete="tel"
                                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </label>

                            <label class="block text-sm font-medium text-gray-700">
                                Email address <span class="text-red-600">*</span>
                                <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" maxlength="255" required autocomplete="email"
                                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </label>

                            <label class="block text-sm font-bold text-gray-900">
                                Payment method
                                <select name="payment_method" x-model="paymentMethod" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm font-normal shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="cash" x-bind:disabled="shippingChoice === 'outside_davao'" @selected(old('payment_method', 'cash') === 'cash') @disabled(old('shipping_zone') === 'outside_davao')>Cash</option>
                                    @foreach (['gcash' => 'GCash', 'bank_transfer' => 'Bank transfer', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('payment_method', 'cash') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <p x-cloak x-show="shippingChoice !== 'outside_davao'" class="text-xs text-gray-500">Payment is recorded as a preference only; online payment is not yet enabled.</p>
                            <p x-cloak x-show="shippingChoice === 'outside_davao'" class="text-xs text-amber-700">Cash is unavailable for outside-Davao delivery. Choose an electronic payment method.</p>

                            <button type="submit" class="w-full rounded-xl bg-purple-700 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                Place order
                            </button>
                            <p class="text-center text-xs leading-relaxed text-gray-500">Your order will be saved as pending until staff confirms it. Stock is deducted after acceptance.</p>
                        </div>
                    </section>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
