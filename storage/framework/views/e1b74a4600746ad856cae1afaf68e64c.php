<nav class="sticky top-0 z-50 border-b border-gray-100 bg-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 flex-wrap items-center justify-between gap-x-3">
            <div class="flex min-w-0 shrink-0 items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="<?php echo e(route('home')); ?>" title="Main Storefront">
                        <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['class' => 'block w-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'block w-auto']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $attributes = $__attributesOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $component = $__componentOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__componentOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
                    </a>
                </div>

            </div>

            <nav aria-label="Primary navigation" class="app-desktop-navigation order-3 flex w-full min-w-0 flex-wrap items-center gap-x-4 gap-y-2 border-t border-gray-100 py-2 text-xs font-bold tracking-wide text-gray-800 xl:order-none xl:w-auto xl:flex-1 xl:flex-nowrap xl:gap-5 xl:border-0 xl:py-0">
                <?php
                    $navigationLinkClasses = 'shrink-0 whitespace-nowrap transition hover:text-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500';
                    $activeNavigationLinkClasses = 'text-purple-700';
                ?>
                <a href="<?php echo e(route('home')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('home')]); ?>"><?php echo e(__('Store')); ?></a>
                <a href="<?php echo e(route('store.desktops')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('store.desktops')]); ?>"><?php echo e(__('Desktops')); ?></a>
                <a href="<?php echo e(route('store.laptops')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('store.laptops')]); ?>"><?php echo e(__('Laptops')); ?></a>
                <div class="group relative shrink-0">
                    <a href="<?php echo e(route('store.categories')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('store.categories')]); ?>" aria-haspopup="true">
                        <?php echo e(__('Categories')); ?>

                    </a>
                    <div class="invisible absolute left-0 top-full z-50 mt-2 max-h-72 min-w-52 translate-y-1 overflow-y-auto rounded-lg border border-gray-200 bg-white py-2 opacity-0 shadow-lg transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                        <?php $__empty_1 = true; $__currentLoopData = $navigationCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e(route('store.category', ['categorySlug' => $category->slug])); ?>" class="block whitespace-nowrap px-4 py-2 text-sm font-medium tracking-normal text-gray-700 transition hover:bg-purple-50 hover:text-purple-700 focus:bg-purple-50 focus:text-purple-700 focus:outline-none">
                                <?php echo e($category->name); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <span class="block px-4 py-2 text-sm font-normal tracking-normal text-gray-500"><?php echo e(__('No categories available')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="<?php echo e(route('store.top-selling')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('store.top-selling')]); ?>"><?php echo e(__('Top Selling')); ?></a>
                <a href="<?php echo e(route('buildpc.customer')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('buildpc.customer')]); ?>"><?php echo e(__('Build PC')); ?></a>
                <a href="<?php echo e(route('store.special-offers')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('store.special-offers')]); ?>"><?php echo e(__('Special offers')); ?></a>

                <?php if(auth()->guard()->check()): ?>
                    <?php if(Auth::user()->canManageOrders()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('dashboard')]); ?>"><?php echo e(__('Dashboard')); ?></a>
                    <?php else: ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('dashboard')]); ?>"><?php echo e(__('My Orders')); ?></a>
                        <a href="<?php echo e(route('cart.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('cart.*')]); ?>"><?php echo e(__('Cart')); ?> (<?php echo e(array_sum(session('cart', []))); ?>)</a>
                    <?php endif; ?>
                    <?php if(Auth::user()->isEmployee()): ?>
                        <div class="group relative shrink-0">
                            <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e($navigationLinkClasses); ?>" aria-haspopup="true">Back Office</a>
                            <div class="invisible absolute left-0 top-full z-50 mt-2 max-h-96 min-w-56 translate-y-1 overflow-y-auto rounded-lg border border-gray-200 bg-white py-2 opacity-0 shadow-lg transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                                <?php if(Auth::user()->hasPermission('inventory.manage')): ?>
                                    <a href="<?php echo e(route('inventory.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Inventory</a>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasPermission('stock_in.manage')): ?>
                                    <a href="<?php echo e(route('stock-in.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Stock In</a>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasPermission('sales.create')): ?>
                                    <a href="<?php echo e(route('pos.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">POS</a>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasPermission('quotations.manage')): ?>
                                    <a href="<?php echo e(route('quotation.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Quotations</a>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasPermission('builds.manage')): ?>
                                    <a href="<?php echo e(route('buildpc.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Build PC</a>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasPermission('customers.manage')): ?>
                                    <a href="<?php echo e(route('customers.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Customers</a>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasPermission('suppliers.manage')): ?>
                                    <a href="<?php echo e(route('suppliers.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Suppliers</a>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasPermission('warranty.verify')): ?>
                                    <a href="<?php echo e(route('warranty.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Warranty & Service</a>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasPermission('expenses.manage')): ?>
                                    <a href="<?php echo e(route('expenses.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Expenses</a>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasPermission('reports.view')): ?>
                                    <a href="<?php echo e(route('reports.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Reports</a>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasPermission('employees.manage')): ?>
                                    <a href="<?php echo e(route('employees.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">Employees & Roles</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('login')]); ?>"><?php echo e(__('Sign In')); ?></a>
                    <?php if(Route::has('register')): ?>
                        <a href="<?php echo e(route('register')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$navigationLinkClasses, $activeNavigationLinkClasses => request()->routeIs('register')]); ?>"><?php echo e(__('Register')); ?></a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <div class="ms-auto flex shrink-0 items-center gap-1">
            <!-- Settings Dropdown -->
            <?php if(auth()->guard()->check()): ?>
                <div class="app-settings-navigation flex shrink-0 items-center">
                    <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48']); ?>
                     <?php $__env->slot('trigger', null, []); ?> 
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div><?php echo e(Auth::user()?->name ?? 'Guest'); ?></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                     <?php $__env->endSlot(); ?>

                     <?php $__env->slot('content', null, []); ?> 
                        <?php if(Auth::user()?->isAdmin()): ?>
                            <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('dashboard'))]); ?>
                                <?php echo e(__('Admin Dashboard')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                        <?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('home')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('home'))]); ?>
                            <?php echo e(__('Go back to Main Dashboard')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('profile.edit')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('profile.edit'))]); ?>
                            <?php echo e(__('Profile')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>

                        <!-- Authentication -->
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>

                            <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('logout'),'onclick' => 'event.preventDefault();
                                                this.closest(\'form\').submit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('logout')),'onclick' => 'event.preventDefault();
                                                this.closest(\'form\').submit();']); ?>
                                <?php echo e(__('Log Out')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                        </form>
                     <?php $__env->endSlot(); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
                </div>
            <?php endif; ?>

            </div>
        </div>
    </div>
</nav>
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>