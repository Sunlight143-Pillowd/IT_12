<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Categories') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($categories as $category)
                    <a href="{{ $category['route'] }}" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-purple-600 hover:shadow-md">
                        <div class="mb-4 flex h-20 items-center justify-center rounded-lg bg-gray-100 text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400">
                            [ Category ]
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $category['label'] }}</h3>
                        <p class="mt-2 text-sm text-gray-500">Explore available products in this category.</p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
