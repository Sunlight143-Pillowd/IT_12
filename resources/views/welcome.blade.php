<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Davao Boss Computer') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] {
                display: none !important;
            }

            @media (min-width: 1280px) {
                .storefront-desktop-navigation {
                    display: flex !important;
                }

                .storefront-menu-toggle,
                .storefront-responsive-navigation {
                    display: none !important;
                }
            }

            @media (max-width: 1279px) {
                .storefront-desktop-navigation {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body class="bg-white text-gray-900 antialiased">
        <div class="bg-[#1c1c1c] text-gray-300 text-xs">
            <div class="mx-auto flex h-8 max-w-7xl items-center justify-between px-4">
                <div class="hidden items-center gap-4 md:flex">
                    <span class="hover:text-white">CORSAIR</span>
                    <span class="hover:text-white">elgato <span class="text-[10px]">(R)</span></span>
                    <span class="hover:text-white">SCUF GAMING</span>
                    <span class="hover:text-white">GAMER SENSE</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <span class="hidden sm:inline">24/7 Lifetime Support</span>
                    <a href="tel:{{ config('store.phone') }}" class="hover:text-white">{{ config('store.phone') }} (PH)</a>
                    <span class="hidden sm:inline">Contact</span>
                </div>
            </div>
        </div>

        <header x-data="{ mobileMenuOpen: false }" class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4">
                <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-2" aria-label="Davao Boss Computer home">
                    <x-application-logo />
                </a>

                <nav class="storefront-desktop-navigation hidden items-center gap-5 text-xs font-bold tracking-wide text-gray-800 xl:flex">
                    <a href="{{ route('store.desktops') }}" class="hover:text-purple-700">DESKTOPS</a>
                    <a href="{{ route('store.laptops') }}" class="hover:text-purple-700">LAPTOPS</a>
                    <a href="{{ route('store.categories') }}" class="hover:text-purple-700">CATEGORIES</a>
                    <a href="{{ route('store.top-selling') }}" class="hover:text-purple-700">TOP SELLING</a>
                    <a href="{{ route('buildpc.customer') }}" class="hover:text-purple-700">BUILD PC</a>
                    <a href="{{ route('store.special-offers') }}" class="text-purple-700 hover:text-purple-800">OFFERS</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-purple-600">{{ Auth::user()->isAdmin() ? 'DASHBOARD' : 'MY ORDERS' }}</a>
                    @endauth
                </nav>

                <div class="flex shrink-0 items-center gap-2 text-gray-700 sm:gap-5">
                    @auth
                    <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 rounded-lg px-2 py-2 text-xs font-bold uppercase tracking-wide hover:bg-purple-50 hover:text-purple-700" aria-label="Shopping cart, {{ array_sum(session('cart', [])) }} items">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.1 10.1a2 2 0 002 1.6h8.8a2 2 0 002-1.6L21 8H6"></path><circle cx="10" cy="20" r="1"></circle><circle cx="18" cy="20" r="1"></circle>
                        </svg>
                        <span>CART ({{ array_sum(session('cart', [])) }})</span>
                    </a>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="inline-flex items-center whitespace-nowrap text-xs font-bold uppercase tracking-wide hover:text-purple-700">Sign In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center whitespace-nowrap text-xs font-bold uppercase tracking-wide hover:text-purple-700">Register</a>
                        @endif
                    @else
                    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                            <button type="button"
                                    @click="open = !open"
                                    aria-haspopup="true"
                                    :aria-expanded="open"
                                    class="inline-flex cursor-pointer items-center gap-2 text-xs font-semibold uppercase tracking-wide text-gray-500 hover:text-purple-600">
                                <span>Hi, {{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open"
                                 x-transition
                                 @click.outside="open = false"
                                 class="absolute right-0 z-50 mt-2 w-52 rounded-md border border-gray-200 bg-white py-1 shadow-lg"
                                 style="display: none;">
                                @if (Auth::user()?->isAdmin())
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endguest
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()" aria-controls="product-navigation" aria-label="Toggle product navigation" class="storefront-menu-toggle inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-700 hover:bg-gray-50 xl:hidden">
                        <svg x-show="!mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"></path></svg>
                    </button>
                </div>
            </div>

            <nav id="product-navigation" x-show="mobileMenuOpen" x-cloak aria-label="Product navigation" class="storefront-responsive-navigation border-t border-gray-100 bg-white px-4 py-3 xl:hidden">
                <div class="mx-auto grid max-w-7xl gap-1 sm:grid-cols-2">
                    <a href="{{ route('store.desktops') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Desktops</a>
                    <a href="{{ route('store.laptops') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Laptops</a>
                    <a href="{{ route('store.categories') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Categories</a>
                    <a href="{{ route('store.top-selling') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Top Selling</a>
                    <a href="{{ route('buildpc.customer') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Build PC</a>
                    <a href="{{ route('store.special-offers') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Special offers</a>
                </div>
            </nav>
        </header>

        <main>
            @if (session('status'))
                <div class="mx-auto mt-4 max-w-7xl px-4 text-sm text-green-700" role="status">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->has('quantity'))
                <div class="mx-auto mt-4 max-w-7xl px-4 text-sm text-red-700" role="alert">
                    {{ $errors->first('quantity') }}
                </div>
            @endif
            @if ($errors->has('image'))
                <div class="mx-auto mt-4 max-w-7xl px-4 text-sm text-red-700" role="alert">
                    {{ $errors->first('image') }}
                </div>
            @endif
            <section class="relative overflow-hidden bg-black">
                <div class="placeholder-img absolute inset-0 opacity-40"></div>
                <div class="relative mx-auto grid min-h-[420px] max-w-7xl grid-cols-1 items-center gap-8 px-4 py-12 lg:grid-cols-2"
                     data-product-carousel
                     x-data="{ activeSlide: 0, slides: @js($carouselSlides), next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length; }, previous() { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length; } }"
                     role="region"
                     aria-roledescription="carousel"
                     aria-label="Product carousel"
                     @keydown.right.prevent="next()"
                     @keydown.left.prevent="previous()"
                     tabindex="0">
                    <div class="z-10 min-w-0 text-white">
                        <h1 x-text="slides[activeSlide]?.name ?? ''" class="mb-4 break-words text-3xl font-extrabold leading-snug sm:text-4xl md:text-5xl">
                            {{ $carouselSlides[0]['name'] ?? 'Browse our products' }}
                        </h1>
                        <p x-text="slides[activeSlide]?.description ?? ''" class="mb-6 max-w-md whitespace-pre-line break-words text-gray-300">
                            {{ $carouselSlides[0]['description'] ?? 'Browse computers, parts, and accessories at Davao Boss Computer.' }}
                        </p>
                        <a href="{{ route('store.special-offers') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm tracking-wide px-6 py-3">
                            START NOW
                        </a>
                    </div>

                    <div class="relative z-10 min-w-0">
                        @forelse ($specialOffers as $index => $product)
                            @php($availableStock = $product->availableStock())
                            <article x-show="activeSlide === {{ $index }}" x-cloak class="rounded-2xl border border-white/10 bg-white p-4 text-gray-900 shadow-2xl sm:p-5" role="group" aria-roledescription="slide" aria-label="{{ $index + 1 }} of {{ $specialOffers->count() }}">
                                <div class="relative flex h-48 items-center justify-center overflow-hidden rounded-xl bg-gray-100 sm:h-56 md:h-64">
                                    @if ($product->featured_image_path)
                                        <img src="{{ asset('storage/'.$product->featured_image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                                    @else
                                        <div class="placeholder-img flex h-full w-full items-center justify-center text-sm font-semibold uppercase tracking-widest text-white/70">Product image coming soon</div>
                                    @endif
                                    @if ($specialOffers->count() > 1)
                                        <button type="button" @click="previous()" aria-label="Previous special offer" class="absolute left-3 top-1/2 inline-flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/70 text-xl text-white transition hover:bg-black">
                                            <span aria-hidden="true">‹</span>
                                        </button>
                                        <button type="button" @click="next()" aria-label="Next special offer" class="absolute right-3 top-1/2 inline-flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/70 text-xl text-white transition hover:bg-black">
                                            <span aria-hidden="true">›</span>
                                        </button>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-start justify-between gap-3 pt-4">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-purple-700">Special offer</p>
                                        <h2 class="break-words text-lg font-black text-gray-900 sm:text-xl">{{ $product->name }}</h2>
                                        <p class="mt-1 text-xl font-black text-purple-700">₱{{ number_format($product->price, 0) }}</p>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $availableStock <= $product->low_stock_threshold ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $availableStock }} in stock
                                    </span>
                                </div>
                            </article>
                        @empty
                            <div class="flex min-h-64 items-center justify-center rounded-2xl border border-white/10 bg-white/10 p-6 text-center text-sm text-gray-300">
                                Products will appear here when added to inventory.
                            </div>
                        @endforelse

                        @if ($specialOffers->count() > 1)
                            <div class="mt-4 flex justify-center gap-2" aria-label="Choose a special offer">
                                @foreach ($specialOffers as $index => $product)
                                    <button type="button" @click="activeSlide = {{ $index }}" :aria-current="activeSlide === {{ $index }} ? 'true' : 'false'" aria-label="Show {{ $product->name }}" class="h-2.5 w-2.5 rounded-full bg-white/40 transition hover:bg-white" :class="activeSlide === {{ $index }} ? 'bg-purple-400' : 'bg-white/40'"></button>
                                @endforeach
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-[#1c1c1c] py-12">
                <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-5 gap-8">
                    @foreach($categoryCards as $category)
                        <div class="group relative flex flex-col items-center gap-4 text-center">
                            <a href="{{ $category['route'] }}" class="flex w-full flex-col items-center gap-4">
                                <div class="relative h-28 w-full overflow-hidden rounded md:h-32">
                                    <img data-category-preview src="{{ $category['image_path'] ? asset('storage/'.$category['image_path']) : '' }}" alt="{{ $category['label'] }}" class="{{ $category['image_path'] ? '' : 'hidden' }} h-full w-full object-contain">
                                    <div data-category-placeholder class="placeholder-img flex h-full w-full flex-col items-center justify-center gap-2 px-2 text-center text-[11px] text-gray-300 {{ $category['image_path'] ? 'hidden' : '' }}">
                                        <span class="text-3xl font-light leading-none text-white/80">＋</span>
                                        @if (! Auth::user()?->canManageOrders())
                                            <span class="text-[9px] font-semibold uppercase tracking-[0.2em] text-white/80">Image</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs font-bold tracking-wide text-white group-hover:text-purple-500 md:text-sm">
                                    {{ strtoupper($category['label']) }}
                                </span>
                            </a>
                            @if (Auth::user()?->canManageOrders() && ! $category['image_path'])
                                <form method="POST" action="{{ route('inventory.categories.image', $category['slug']) }}" enctype="multipart/form-data" class="absolute right-2 top-2 flex flex-col items-end gap-1">
                                    @csrf
                                    <input type="hidden" name="category_name" value="{{ $category['name'] }}">
                                    <label class="inline-flex cursor-pointer items-center gap-1.5 rounded bg-white/95 px-2.5 py-1.5 text-[9px] font-bold uppercase tracking-wide text-gray-800 shadow transition hover:bg-purple-100 hover:text-purple-800">
                                        <span aria-hidden="true" class="text-sm leading-none">＋</span>
                                        <span>Upload Photo</span>
                                        <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/bmp,image/webp" required class="sr-only" onchange="previewCategoryPhoto(this)">
                                    </label>
                                    <button type="submit" data-category-submit class="hidden rounded bg-purple-700 px-2.5 py-1.5 text-[9px] font-bold uppercase tracking-wide text-white shadow transition hover:bg-purple-800">Save Photo</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        </main>

        @include('layouts.footer')
        <script>
            function previewCategoryPhoto(input) {
                const file = input.files?.[0];
                const card = input.closest('[data-photo-card], .group');
                const image = card?.querySelector('[data-category-preview]');
                const placeholder = card?.querySelector('[data-category-placeholder]');
                const submitButton = card?.querySelector('[data-category-submit]');

                if (!file || !image || !placeholder || !submitButton) {
                    return;
                }

                image.src = URL.createObjectURL(file);
                image.classList.remove('hidden');
                placeholder.classList.add('hidden');
                submitButton.classList.remove('hidden');
            }
        </script>
    </body>
</html>
