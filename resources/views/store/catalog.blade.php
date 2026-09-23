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
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex h-52 items-center justify-center bg-gray-100 text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">
                            [ Image Placeholder ]
                        </div>
                        <div class="p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-500">{{ ucfirst($product->category) }}</p>
                            <h3 class="mt-2 text-xl font-bold text-gray-900">{{ $product->name }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ $product->description ?? 'High-performance product for your setup.' }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-lg font-black text-purple-600">₱{{ number_format($product->price, 0) }}</span>
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">{{ $product->stock_quantity }} in stock</span>
                            </div>
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
