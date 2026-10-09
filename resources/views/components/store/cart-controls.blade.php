@props(['product', 'availableStock'])

@php
    $cartQuantity = (int) (session('cart', [])[$product->id] ?? 0);
@endphp

<div class="mt-4 flex flex-wrap items-center gap-2">
    @if ($availableStock > 0)
        <form method="POST" action="{{ route('cart.items.store', $product) }}">
            @csrf
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
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
            </button>
        </form>
    @else
        <button type="button" disabled class="cursor-not-allowed rounded-md bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-600">
            Out of stock
        </button>
    @endif

</div>
