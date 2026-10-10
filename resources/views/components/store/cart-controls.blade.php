<<<<<<< HEAD
@props(['product', 'availableStock'])
=======
@props(['product', 'availableStock', 'showQuantityControls' => false])
>>>>>>> f3ac0bb2f8c156e46e87a9aef60a47e16a08f462

@php
    $cartQuantity = (int) (session('cart', [])[$product->id] ?? 0);
@endphp

<div class="mt-4 flex flex-wrap items-center gap-2">
    @if ($availableStock > 0)
        <form method="POST" action="{{ route('cart.items.store', $product) }}">
            @csrf
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
<<<<<<< HEAD
                    aria-label="Add {{ $product->name }} to cart"
                    title="Add to cart"
                    @disabled($cartQuantity >= $availableStock)
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:bg-gray-300">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                    <path d="M3 4h2l2.1 9.2a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.76L18.9 7H6.1" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="10" cy="17.5" r="1.25"/>
                    <circle cx="17" cy="17.5" r="1.25"/>
                </svg>
                <span>Add to cart</span>
=======
                    @disabled($cartQuantity >= $availableStock)
                    class="rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:bg-gray-300">
                Add to cart
>>>>>>> f3ac0bb2f8c156e46e87a9aef60a47e16a08f462
            </button>
        </form>
    @else
        <button type="button" disabled class="cursor-not-allowed rounded-md bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-600">
            Out of stock
        </button>
    @endif

<<<<<<< HEAD
=======
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
>>>>>>> f3ac0bb2f8c156e46e87a9aef60a47e16a08f462
</div>
