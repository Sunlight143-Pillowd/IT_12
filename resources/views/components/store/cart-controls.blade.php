@props(['product', 'availableStock', 'showQuantityControls' => false])

@php
    $cartQuantity = (int) (session('cart', [])[$product->id] ?? 0);
@endphp

<div class="mt-4 flex flex-wrap items-center gap-2">
    @if ($availableStock > 0)
        <form method="POST" action="{{ route('cart.items.store', $product) }}">
            @csrf
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
                    @disabled($cartQuantity >= $availableStock)
                    class="rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:bg-gray-300">
                Add to cart
            </button>
        </form>
    @else
        <button type="button" disabled class="cursor-not-allowed rounded-md bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-600">
            Out of stock
        </button>
    @endif

    @if ($cartQuantity > 0 || $showQuantityControls)
        @if ($cartQuantity > 0)
            <form method="POST" action="{{ route('cart.items.update', $product) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="quantity" value="{{ max(1, min($availableStock, $cartQuantity - 1)) }}">
                <button type="submit"
                        aria-label="Decrease quantity of {{ $product->name }}"
                        @disabled($cartQuantity <= 1 || $availableStock < 1)
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">−</button>
            </form>
        @else
            <button type="button" aria-label="Decrease quantity of {{ $product->name }}" disabled class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 opacity-40">−</button>
        @endif
        <span class="min-w-6 text-center text-sm font-semibold text-gray-900" aria-label="Quantity in cart">{{ $cartQuantity }}</span>
        @if ($cartQuantity > 0)
            <form method="POST" action="{{ route('cart.items.update', $product) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="quantity" value="{{ min($availableStock, $cartQuantity + 1) }}">
                <button type="submit"
                        aria-label="Increase quantity of {{ $product->name }}"
                        @disabled($cartQuantity >= $availableStock)
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">+</button>
            </form>
            <form method="POST" action="{{ route('cart.items.destroy', $product) }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                        aria-label="Remove {{ $product->name }} from cart"
                        class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                    Remove
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('cart.items.store', $product) }}">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                        aria-label="Increase quantity of {{ $product->name }}"
                        @disabled($availableStock < 1)
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">+</button>
            </form>
        @endif
    @endif
</div>
