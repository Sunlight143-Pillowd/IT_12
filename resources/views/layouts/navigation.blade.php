<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
<<<<<<< HEAD
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" title="Main Storefront">
                        <x-application-logo class="block w-auto" />
=======
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" title="Main Storefront">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                    </a>
                </div>

                <!-- Navigation Links -->
<<<<<<< HEAD
                <div class="app-desktop-navigation hidden items-center gap-6 lg:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Store') }}
                    </x-nav-link>
                    <x-nav-link :href="route('store.top-selling')" :active="request()->routeIs('store.top-selling')">
                        {{ __('Top Selling') }}
                    </x-nav-link>
                    <x-nav-link :href="route('buildpc.customer')" :active="request()->routeIs('buildpc.customer')">
                        {{ __('Build PC') }}
                    </x-nav-link>
                    @auth
                        @if (Auth::user()->isAdmin())
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @else
                            <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                                {{ __('Cart') }} ({{ array_sum(session('cart', [])) }})
                            </x-nav-link>
                        @endif
                    @else
                        <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                            {{ __('Sign In') }}
                        </x-nav-link>
                        @if (Route::has('register'))
                            <x-nav-link :href="route('register')" :active="request()->routeIs('register')">
                                {{ __('Register') }}
                            </x-nav-link>
                        @endif
                    @endauth
=======
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if (Auth::user()?->isAdmin())
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                            {{ __('Cart') }} ({{ array_sum(session('cart', [])) }})
                        </x-nav-link>
                    @endif
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                </div>
            </div>

            <!-- Settings Dropdown -->
<<<<<<< HEAD
            @auth
                <div class="app-settings-navigation hidden lg:flex lg:items-center lg:ms-6">
                    <x-dropdown align="right" width="48">
=======
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
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
<<<<<<< HEAD
                    </x-dropdown>
                </div>
            @endauth

            <!-- Hamburger -->
            <div class="app-mobile-navigation-toggle -me-2 flex items-center lg:hidden">
                <button @click="open = ! open" :aria-expanded="open.toString()" aria-label="Toggle navigation" class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
=======
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
<<<<<<< HEAD
    <div :class="{'block': open, 'hidden': ! open}" class="app-responsive-navigation hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
        <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
            {{ __('Store') }}
            </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('store.top-selling')" :active="request()->routeIs('store.top-selling')">
            {{ __('Top Selling') }}
            </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('buildpc.customer')" :active="request()->routeIs('buildpc.customer')">
            {{ __('Build PC') }}
        </x-responsive-nav-link>
        @auth
            @if (Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @else
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

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()?->name ?? 'Guest' }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()?->email ?? 'guest@example.com' }}</div>
                </div>
            @endauth
=======
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                {{ __('Cart') }} ({{ array_sum(session('cart', [])) }})
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()?->name ?? 'Guest' }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()?->email ?? 'guest@example.com' }}</div>
            </div>
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce

            <div class="mt-3 space-y-1">
                @if (Auth::user()?->isAdmin())
                    <x-responsive-nav-link :href="route('dashboard')">
                        {{ __('Admin Dashboard') }}
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
