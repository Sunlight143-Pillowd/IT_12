<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Categories') }}
        </h2>
    </x-slot>

<<<<<<< HEAD
    <div class="py-4">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('image'))
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    {{ $errors->first('image') }}
                </div>
            @endif

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($categories as $category)
                    <article class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-purple-600 hover:shadow-md">
                        <a href="{{ $category['route'] }}" class="block">
                            <div class="relative mb-4 flex h-20 items-center justify-center overflow-hidden rounded-lg bg-gray-100 text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400">
                                @if ($category['image_path'])
                                    <img src="{{ asset('storage/'.$category['image_path']) }}" alt="{{ $category['label'] }}" class="h-full w-full object-contain">
                                @else
                                    [ Category ]
                                @endif
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $category['label'] }}</h3>
                            <p class="mt-2 text-sm text-gray-500">Explore available products in this category.</p>
                        </a>
                    </article>
=======
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
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                @endforeach
            </div>
        </div>
    </div>
<<<<<<< HEAD

=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
</x-app-layout>
