@php
    $currentFilter = $filter ?? 'all';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-4">
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

            @if ($errors->has('image'))
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    {{ $errors->first('image') }}
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
<<<<<<< HEAD
                    <div class="flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
=======
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
>>>>>>> f3ac0bb2f8c156e46e87a9aef60a47e16a08f462
                        <div data-photo-card class="relative h-52 bg-gray-100">
                            @if ($product->image_path)
                                <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                            @else
                                <img data-product-photo-preview alt="{{ $product->name }}" class="hidden h-full w-full object-contain">
                                <div data-product-photo-placeholder class="flex h-full items-center justify-center text-sm text-gray-500" aria-label="No product photo available">
                                    Photo unavailable
                                </div>
                                @if (Auth::user()?->isAdmin())
                                    <form method="POST" action="{{ route('inventory.products.image', $product) }}" enctype="multipart/form-data" class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-3">
                                        @csrf
                                        <label class="inline-flex cursor-pointer items-center gap-2 rounded bg-white px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-gray-800 shadow transition hover:bg-purple-100 hover:text-purple-800">
                                            <span>Upload Photo</span>
                                            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/bmp,image/webp" required class="sr-only" onchange="previewProductPhoto(this)">
                                        </label>
                                        <button type="submit" data-product-photo-submit class="hidden rounded bg-purple-700 px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-white shadow transition hover:bg-purple-800">Save Photo</button>
                                    </form>
                                @endif
                            @endif
                        </div>
<<<<<<< HEAD
                        <div class="flex flex-1 flex-col p-4">
=======
                        <div class="p-4">
>>>>>>> f3ac0bb2f8c156e46e87a9aef60a47e16a08f462
                            <h3 class="text-xl font-bold text-gray-900">{{ $product->name }}</h3>
                            @if (! empty(trim((string) $product->description)))
                                <p class="mt-2 text-sm leading-6 text-gray-600">{{ $product->description }}</p>
                            @endif
<<<<<<< HEAD
                            <div class="mt-auto flex items-center justify-between pt-4">
                                <span class="text-lg font-black text-purple-600">₱{{ number_format($product->price, 0) }}</span>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $availableStock <= $product->low_stock_threshold ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ $availableStock }} in stock</span>
                            </div>
                            <x-store.cart-controls :product="$product" :available-stock="$availableStock" />
=======
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-lg font-black text-purple-600">₱{{ number_format($product->price, 0) }}</span>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $availableStock <= $product->low_stock_threshold ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ $availableStock }} in stock</span>
                            </div>
                            <x-store.cart-controls :product="$product" :available-stock="$availableStock" :show-quantity-controls="request()->routeIs('store.laptops')" />
>>>>>>> f3ac0bb2f8c156e46e87a9aef60a47e16a08f462
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

<script>
    function previewProductPhoto(input) {
        const file = input.files?.[0];
        const card = input.closest('[data-photo-card]');
        const image = card?.querySelector('[data-product-photo-preview]');
        const placeholder = card?.querySelector('[data-product-photo-placeholder]');
        const submitButton = card?.querySelector('[data-product-photo-submit]');

        if (!file || !image || !placeholder || !submitButton) {
            return;
        }

        image.src = URL.createObjectURL(file);
        image.classList.remove('hidden');
        placeholder.classList.add('hidden');
        submitButton.classList.remove('hidden');
    }
</script>
</x-app-layout>
