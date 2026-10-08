@php
    $currentFilter = $filter ?? 'all';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="mb-6 text-sm text-gray-500">{{ $description }}</p>

            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('quantity'))
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    {{ $errors->first('quantity') }}
                </div>
            @endif

            <div class="mb-8 flex flex-wrap gap-2">
                @foreach ($filters as $value => $label)
                    @php
                        $routeName = match ($type) {
                            'desktop' => 'store.desktops',
                            'laptop' => 'store.laptops',
                            'accessory' => 'store.accessories',
                            default => 'store.special-offers',
                        };
                    @endphp

                    <a href="{{ route($routeName, $value === 'all' ? [] : ['filter' => $value]) }}"
                       class="rounded-full border px-3 py-1.5 text-xs font-semibold {{ $currentFilter === $value ? 'border-purple-600 bg-purple-600 text-white' : 'border-gray-300 text-gray-700 hover:border-purple-400' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($products as $product)
                    @php($availableStock = $product->availableStock())
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        @if ($product->image_path)
                            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-52 w-full bg-gray-100 object-cover">
                        @else
                            <div class="flex h-52 flex-col items-center justify-center gap-2 bg-gray-100 text-gray-400" aria-label="No product photo available">
                                <span class="text-4xl font-light leading-none text-purple-500">＋</span>
                                <span class="text-[10px] font-semibold uppercase tracking-[0.2em]">Image</span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-xl font-bold text-gray-900">{{ $product->name }}</h3>
                            @if (! empty(trim((string) $product->description)))
                                <p class="mt-2 text-sm leading-6 text-gray-600">{{ $product->description }}</p>
                            @endif
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-lg font-black text-purple-600">₱{{ number_format($product->price, 0) }}</span>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $availableStock <= $product->low_stock_threshold ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ $availableStock }} in stock</span>
                            </div>
                            <x-store.cart-controls :product="$product" :available-stock="$availableStock" :show-quantity-controls="request()->routeIs('store.laptops', 'store.top-selling')" />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center text-gray-500">
                        No stock available.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
