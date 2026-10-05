<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Cart') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($items->isEmpty())
                <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                    <p class="text-gray-600">Your cart is empty.</p>
                    <a href="{{ route('store.accessories') }}" class="mt-4 inline-flex rounded-md bg-purple-600 px-5 py-3 text-sm font-semibold text-white hover:bg-purple-700">
                        Browse products
                    </a>
                </div>
            @else
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
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
