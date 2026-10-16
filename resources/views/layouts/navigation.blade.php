<nav class="sticky top-0 z-50 border-b border-gray-100 bg-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 flex-wrap items-center justify-between gap-x-3">
            <div class="flex min-w-0 shrink-0 items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" title="Main Storefront">
                        <x-application-logo class="block w-auto" />
                    </a>
                </div>

            </div>

            <nav aria-label="Primary navigation" class="app-desktop-navigation order-3 flex w-full min-w-0 flex-wrap items-center gap-x-4 gap-y-2 border-t border-gray-100 py-2 text-xs font-bold tracking-wide text-gray-800 xl:order-none xl:w-auto xl:flex-1 xl:flex-nowrap xl:gap-5 xl:border-0 xl:py-0">
                @php
                    $navigationLinkClasses = 'shrink-0 whitespace-nowrap transition hover:text-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500';
                    $activeNavigationLinkClasses = 'text-purple-700';
                @endphp
                <a href="{{ route('home') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('home')])>{{ __('Store') }}</a>
                <a href="{{ route('store.desktops') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('store.desktops')])>{{ __('Desktops') }}</a>
                <a href="{{ route('store.laptops') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('store.laptops')])>{{ __('Laptops') }}</a>
                <div class="group relative shrink-0">
                    <a href="{{ route('store.categories') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('store.categories')]) aria-haspopup="true">
                        {{ __('Categories') }}
                    </a>
                    <div class="invisible absolute left-0 top-full z-50 mt-2 max-h-72 min-w-52 translate-y-1 overflow-y-auto rounded-lg border border-gray-200 bg-white py-2 opacity-0 shadow-lg transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                        @forelse ($navigationCategories as $category)
                            <a href="{{ route('store.category', ['categorySlug' => $category->slug]) }}" class="block whitespace-nowrap px-4 py-2 text-sm font-medium tracking-normal text-gray-700 transition hover:bg-purple-50 hover:text-purple-700 focus:bg-purple-50 focus:text-purple-700 focus:outline-none">
                                {{ $category->name }}
                            </a>
                        @empty
                            <span class="block px-4 py-2 text-sm font-normal tracking-normal text-gray-500">{{ __('No categories available') }}</span>
                        @endforelse
                    </div>
                </div>
                <a href="{{ route('store.top-selling') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('store.top-selling')])>{{ __('Top Selling') }}</a>
                <a href="{{ route('buildpc.customer') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('buildpc.customer')])>{{ __('Build PC') }}</a>
                <a href="{{ route('store.special-offers') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('store.special-offers')])>{{ __('Special offers') }}</a>

                @auth
                    @if (Auth::user()->canManageOrders())
                        <a href="{{ route('dashboard') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('dashboard')])>{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('dashboard') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('dashboard')])>{{ __('My Orders') }}</a>
                        <a href="{{ route('cart.index') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('cart.*')])>{{ __('Cart') }} ({{ array_sum(session('cart', [])) }})</a>
                    @endif
                    @if (Auth::user()->isEmployee())
                        <div class="group relative shrink-0">
                            <a href="{{ route('dashboard') }}" class="{{ $navigationLinkClasses }}" aria-haspopup="true">Back Office</a>
                            <div class="invisible absolute left-0 top-full z-50 mt-2 max-h-96 min-w-56 translate-y-1 overflow-y-auto rounded-lg border border-gray-200 bg-white py-2 opacity-0 shadow-lg transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                                @if (Auth::user()->hasPermission('inventory.manage'))
                                    <a href="{{ route('inventory.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Inventory</a>
                                @endif
                                @if (Auth::user()->hasPermission('stock_in.manage'))
                                    <a href="{{ route('stock-in.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Stock In</a>
                                @endif
                                @if (Auth::user()->hasPermission('sales.create'))
                                    <a href="{{ route('pos.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">POS</a>
                                @endif
                                @if (Auth::user()->hasPermission('quotations.manage'))
                                    <a href="{{ route('quotation.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Quotations</a>
                                @endif
                                @if (Auth::user()->hasPermission('builds.manage'))
                                    <a href="{{ route('buildpc.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Build PC</a>
                                @endif
                                @if (Auth::user()->hasPermission('customers.manage'))
                                    <a href="{{ route('customers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Customers</a>
                                @endif
                                @if (Auth::user()->hasPermission('suppliers.manage'))
                                    <a href="{{ route('suppliers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Suppliers</a>
                                @endif
                                @if (Auth::user()->hasPermission('warranty.verify'))
                                    <a href="{{ route('warranty.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Warranty & Service</a>
                                @endif
                                @if (Auth::user()->hasPermission('expenses.manage'))
                                    <a href="{{ route('expenses.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Expenses</a>
                                @endif
                                @if (Auth::user()->hasPermission('reports.view'))
                                    <a href="{{ route('reports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Reports</a>
                                @endif
                                @if (Auth::user()->hasPermission('employees.manage'))
                                    <a href="{{ route('employees.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Employees & Roles</a>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('login')])>{{ __('Sign In') }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" @class([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('register')])>{{ __('Register') }}</a>
                    @endif
                @endauth
            </nav>

            <div class="ms-auto flex shrink-0 items-center gap-1">
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

            </div>
        </div>
    </div>
</nav>
