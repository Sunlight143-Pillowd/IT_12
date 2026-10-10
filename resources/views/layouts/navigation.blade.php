<nav x-data="{ open: false }" @keydown.escape.window="open = false" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex min-w-0 items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" title="Main Storefront">
                        <x-application-logo class="block w-auto" />
                    </a>
                </div>

            </div>

            <div class="ms-auto flex items-center gap-1">
            <!-- Settings Dropdown -->
            @auth
                <div class="app-settings-navigation flex shrink-0 items-center">
                    <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()?->name ?? 'Guest' }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if (Auth::user()?->isAdmin())
                            <x-dropdown-link :href="route('dashboard')">
                                {{ __('Admin Dashboard') }}
                            </x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('home')">
                            {{ __('Go back to Main Dashboard') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                    </x-dropdown>
                </div>
            @endauth

            <!-- Hamburger -->
            <div class="app-mobile-navigation-toggle flex shrink-0 items-center">
                <button @click="open = ! open" :aria-expanded="open.toString()" aria-controls="app-responsive-navigation" aria-label="Toggle navigation" class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div id="app-responsive-navigation" x-show="open" x-cloak @click.outside="open = false" class="app-responsive-navigation">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Store') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('store.desktops')" :active="request()->routeIs('store.desktops')">
                {{ __('Desktops') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('store.laptops')" :active="request()->routeIs('store.laptops')">
                {{ __('Laptops') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('store.categories')" :active="request()->routeIs('store.categories')">
                {{ __('Categories') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('store.top-selling')" :active="request()->routeIs('store.top-selling')">
                {{ __('Top Selling') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('buildpc.customer')" :active="request()->routeIs('buildpc.customer')">
                {{ __('Build PC') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('store.special-offers')" :active="request()->routeIs('store.special-offers')">
                {{ __('Special offers') }}
            </x-responsive-nav-link>
            @auth
                @if (Auth::user()->isAdmin())
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')">
                        {{ __('Inventory') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('stock-in.index')" :active="request()->routeIs('stock-in.*')">
                        {{ __('Stock In') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
                        {{ __('POS') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('quotation.index')" :active="request()->routeIs('quotation.*')">
                        {{ __('Quotations') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('buildpc.index')" :active="request()->routeIs('buildpc.index', 'buildpc.show', 'buildpc.edit')">
                        {{ __('Build PC Orders') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('My Orders') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                        {{ __('Cart') }} ({{ array_sum(session('cart', [])) }})
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                    {{ __('Sign In') }}
                </x-responsive-nav-link>
                @if (Route::has('register'))
                    <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>
    </div>
</nav>
