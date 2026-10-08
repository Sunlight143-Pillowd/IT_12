<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Davao Boss Computer') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
<<<<<<< HEAD
        <style>
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
=======
    </head>
    <body class="bg-white text-gray-900 antialiased">
        <div class="bg-[#1c1c1c] text-gray-300 text-xs">
            <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-8">
                <div class="flex items-center gap-4">
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                    <span class="hover:text-white">CORSAIR</span>
                    <span class="hover:text-white">elgato <span class="text-[10px]">(R)</span></span>
                    <span class="hover:text-white">SCUF GAMING</span>
                    <span class="hover:text-white">GAMER SENSE</span>
                </div>
<<<<<<< HEAD
                <div class="flex items-center gap-2 sm:gap-4">
                    <span class="hidden sm:inline">24/7 Lifetime Support</span>
                    <a href="tel:{{ config('store.phone') }}" class="hover:text-white">{{ config('store.phone') }} (PH)</a>
                    <span class="hidden sm:inline">Contact</span>
=======
                <div class="flex items-center gap-4">
                    <span>24/7 Lifetime Support</span>
                    <span>09123456789 (PH)</span>
                    <span>Chat Offline</span>
                    <span>Contact</span>
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                </div>
            </div>
        </div>

<<<<<<< HEAD
        <header x-data="{ mobileMenuOpen: false }" class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2" aria-label="Davao Boss Computer home">
                    <x-application-logo class="shrink-0" />
                </a>

                <nav class="storefront-desktop-navigation hidden items-center gap-5 text-xs font-bold tracking-wide text-gray-800 xl:flex">
                    <a href="{{ route('store.desktops') }}" class="hover:text-purple-700">DESKTOPS</a>
                    <a href="{{ route('store.laptops') }}" class="hover:text-purple-700">LAPTOPS</a>
                    <a href="{{ route('store.categories') }}" class="hover:text-purple-700">CATEGORIES</a>
                    <a href="{{ route('store.top-selling') }}" class="hover:text-purple-700">TOP SELLING</a>
                    <a href="{{ route('buildpc.customer') }}" class="hover:text-purple-700">BUILD PC</a>
                    <a href="{{ route('store.special-offers') }}" class="text-purple-700 hover:text-purple-800">OFFERS</a>
=======
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-2" aria-label="Davao Boss Computer home">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full border-4 border-purple-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-purple-600"></span>
                    </span>
                    <span class="text-3xl font-black tracking-tight text-purple-600">COMPUTER BOSS DAVAO</span>
                </a>

                <nav class="hidden lg:flex items-center gap-10 text-sm font-semibold text-gray-800">
                    <a href="{{ route('store.desktops') }}" class="hover:text-purple-600">DESKTOPS</a>
                    <a href="{{ route('store.laptops') }}" class="hover:text-purple-600">LAPTOPS</a>
                    <a href="{{ route('store.categories') }}" class="hover:text-purple-600">CATEGORIES</a>
                    <a href="{{ route('store.special-offers') }}" class="text-purple-600 hover:text-purple-700">SPECIAL OFFERS</a>
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-purple-600">{{ Auth::user()->isAdmin() ? 'DASHBOARD' : 'MY ORDERS' }}</a>
                    @endauth
                </nav>

<<<<<<< HEAD
                <div class="flex items-center gap-3 text-gray-700 sm:gap-5">
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
=======
                <div class="flex items-center gap-6 text-gray-700">
                    <a href="{{ route('cart.index') }}" class="text-xs font-semibold uppercase tracking-wide hover:text-purple-600">
                        CART ({{ array_sum(session('cart', [])) }})
                    </a>
                    @guest
                    <a href="{{ route('login') }}" class="text-xs font-semibold uppercase tracking-wide hover:text-purple-600">Sign In</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-xs font-semibold uppercase tracking-wide hover:text-purple-600">Register</a>
                    @endif
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
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
<<<<<<< HEAD
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()" aria-label="Toggle navigation" class="storefront-menu-toggle inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-700 hover:bg-gray-50 xl:hidden">
                        <svg x-show="!mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"></path></svg>
                    </button>
                </div>
            </div>

            <nav x-show="mobileMenuOpen" x-cloak class="storefront-responsive-navigation border-t border-gray-100 bg-white px-4 py-3 xl:hidden">
                <div class="mx-auto grid max-w-7xl gap-1 sm:grid-cols-2">
                    <a href="{{ route('store.desktops') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Desktops</a>
                    <a href="{{ route('store.laptops') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Laptops</a>
                    <a href="{{ route('store.categories') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Categories</a>
                    <a href="{{ route('store.top-selling') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Top Selling</a>
                    <a href="{{ route('buildpc.customer') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Build PC</a>
                    <a href="{{ route('store.special-offers') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Special offers</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">{{ Auth::user()->isAdmin() ? 'Dashboard' : 'My orders' }}</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Sign in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-purple-50">Register</a>
                        @endif
                    @endauth
                </div>
            </nav>
=======
                </div>
            </div>
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
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
<<<<<<< HEAD
            @if ($errors->has('image'))
                <div class="mx-auto mt-4 max-w-7xl px-4 text-sm text-red-700" role="alert">
                    {{ $errors->first('image') }}
                </div>
            @endif
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
            <section class="relative bg-black overflow-hidden">
                <div class="placeholder-img absolute inset-0 opacity-40"></div>
                <div class="relative max-w-7xl mx-auto px-4 py-16 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center min-h-[520px]">
                    <div class="text-white z-10">
                        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                            It's as simple as 1, 2, 3!
                        </h1>
                        <p class="text-gray-300 mb-6 max-w-md">
                            With 3 easy steps, choose your next gaming PC with our new
                            <span class="font-semibold text-white">Gaming Desktop Advisor</span>
                        </p>
                        <a href="{{ route('store.desktops') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm tracking-wide px-6 py-3">
                            START NOW
                        </a>
                    </div>

                    <div class="relative z-10 flex items-center justify-center gap-4">
                        <div class="flex items-end gap-4">
<<<<<<< HEAD
                            @foreach ([
                                'hero-main' => ['width' => 'w-56 h-72 md:w-64 md:h-80', 'button' => 'Upload Photo'],
                                'hero-secondary' => ['width' => 'w-32 h-40 md:w-36 md:h-48', 'button' => 'Upload'],
                            ] as $imageKey => $heroImage)
                                @php($heroImagePath = $heroImages->get($imageKey))
                                <div class="group relative {{ $heroImage['width'] }}">
                                    <img data-category-preview src="{{ $heroImagePath ? asset('storage/'.$heroImagePath) : '' }}" alt="Homepage feature" class="{{ $heroImagePath ? '' : 'hidden' }} h-full w-full rounded object-cover">
                                    <div data-category-placeholder class="placeholder-img flex h-full w-full flex-col items-center justify-center gap-2 rounded p-4 text-center text-gray-300 {{ $heroImagePath ? 'hidden' : '' }}">
                                        <span class="text-4xl font-light leading-none text-white/80">＋</span>
                                        @if (! Auth::user()?->canManageOrders())
                                            <span class="text-[10px] font-semibold uppercase tracking-[0.22em] text-white/80">Image</span>
                                        @endif
                                    </div>
                                    @if (Auth::user()?->canManageOrders() && ! $heroImagePath)
                                        <form method="POST" action="{{ route('homepage-images.upload', $imageKey) }}" enctype="multipart/form-data" class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-2">
                                            @csrf
                                            <label class="inline-flex cursor-pointer items-center gap-2 rounded bg-white/95 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.18em] text-gray-800 shadow transition hover:bg-purple-100 hover:text-purple-800">
                                                <span aria-hidden="true" class="text-xl leading-none">＋</span>
                                                <span>{{ $heroImage['button'] }}</span>
                                                <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/bmp,image/webp" required class="sr-only" onchange="previewCategoryPhoto(this)">
                                            </label>
                                            <button type="submit" data-category-submit class="hidden rounded bg-purple-700 px-3 py-2 text-[10px] font-bold uppercase tracking-wide text-white shadow transition hover:bg-purple-800">Save Photo</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
=======
                            <div class="placeholder-img w-56 h-72 md:w-64 md:h-80 flex items-center justify-center text-gray-400 text-xs text-center p-4 rounded">
                                [ Image Placeholder<br>Gaming PC Case ]
                            </div>
                            <div class="placeholder-img w-32 h-40 md:w-36 md:h-48 flex items-center justify-center text-gray-400 text-xs text-center p-2 rounded">
                                [ Image Placeholder<br>Speaker/Unit ]
                            </div>
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-[#1c1c1c] py-12">
                <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-5 gap-8">
                    @foreach($categoryCards as $category)
<<<<<<< HEAD
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
=======
                        <a href="{{ $category['route'] }}" class="group flex flex-col items-center gap-4 text-center">
                            <div class="placeholder-img w-full h-28 md:h-32 rounded flex items-center justify-center text-gray-400 text-[11px] text-center px-2">
                                [ Image Placeholder ]
                            </div>
                            <span class="text-white text-xs md:text-sm font-bold tracking-wide group-hover:text-purple-500">
                                {{ strtoupper($category['label']) }}
                            </span>
                        </a>
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                    @endforeach
                </div>
            </section>

            <section class="py-12">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="mb-6 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-600">In stock now</p>
                            <h2 class="mt-2 text-2xl font-black text-gray-900">Featured Products</h2>
                        </div>
                        <a href="{{ route('store.desktops') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-700">Browse store</a>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @forelse ($featured as $product)
<<<<<<< HEAD
                            @php($availableStock = $product->availableStock())
                            <article data-photo-card class="relative flex h-full flex-col border border-gray-200 bg-white p-4">
                                <div class="relative mt-3 flex h-52 w-full items-center justify-center overflow-hidden bg-gray-100">
                                    <img data-category-preview src="{{ $product->featured_image_path ? asset('storage/'.$product->featured_image_path) : '' }}" alt="{{ $product->name }}" class="{{ $product->featured_image_path ? '' : 'hidden' }} h-full w-full object-contain">
                                    <div data-category-placeholder class="h-full w-full {{ $product->featured_image_path ? 'hidden' : '' }}"></div>
                                </div>
                                <h3 class="mt-3 min-h-14 text-lg font-bold text-gray-900">{{ $product->name }}</h3>
                                <div class="mt-auto flex items-center justify-between gap-3 pt-4">
                                    <span class="font-black text-purple-600">₱{{ number_format($product->price, 0) }}</span>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $availableStock <= $product->low_stock_threshold ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $availableStock }} in stock
                                    </span>
                                </div>
                                <x-store.cart-controls :product="$product" :available-stock="$availableStock" />
=======
                            <article class="border border-gray-200 bg-white p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $product->category }}</p>
                                <h3 class="mt-2 text-lg font-bold text-gray-900">{{ $product->name }}</h3>
                                <p class="mt-2 min-h-10 text-sm text-gray-600">{{ $product->description ?? 'Available in store now.' }}</p>
                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <span class="font-black text-purple-600">₱{{ number_format($product->price, 0) }}</span>
                                    <span class="text-xs font-semibold text-gray-500">{{ $product->stock_quantity }} in stock</span>
                                </div>
                                @if ($product->stock_quantity > 0)
                                    <form method="POST" action="{{ route('cart.items.store', $product) }}" class="mt-4 flex items-end gap-2">
                                        @csrf
                                        <label class="text-xs font-medium text-gray-700">
                                            Quantity
                                            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="{{ $product->stock_quantity }}" required
                                                   class="mt-1 block w-20 rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        </label>
                                        <button type="submit" class="rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white hover:bg-purple-700">
                                            Add to cart
                                        </button>
                                    </form>
                                @else
                                    <button type="button" disabled class="mt-4 cursor-not-allowed rounded-md bg-gray-300 px-3 py-2 text-sm font-semibold text-gray-600">
                                        Out of stock
                                    </button>
                                @endif
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                            </article>
                        @empty
                            <p class="col-span-full text-sm text-gray-500">Products will appear here when they are added to inventory.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </main>

<<<<<<< HEAD
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
=======
        <footer class="bg-[#111827] text-gray-300 py-10">
            <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between gap-4 text-sm">
                <div>
                    <p class="font-semibold text-white">Davao Boss Computer</p>
                    <p class="mt-2 max-w-md text-gray-400">Performance builds, workstations, and gaming gear for everyday power users.</p>
                </div>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white">Support</a>
                    <a href="#" class="hover:text-white">Shipping</a>
                    <a href="#" class="hover:text-white">Privacy</a>
                </div>
            </div>
        </footer>
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
    </body>
</html>
