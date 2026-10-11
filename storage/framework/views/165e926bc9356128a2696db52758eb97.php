<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-700">Custom configuration</p>
                <h1 class="mt-1 text-2xl font-black text-gray-900">Build your PC</h1>
            </div>
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="text-sm font-semibold text-purple-700 hover:underline">View my orders and builds</a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-4">
        <div class="mx-auto max-w-7xl space-y-7 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div>
                <form id="customer-build-form" method="POST" action="<?php echo e(route('buildpc.customer.store')); ?>" class="space-y-5">
                    <?php echo csrf_field(); ?>

                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="mb-5 grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="customer_name" class="mb-2 block text-sm font-bold text-gray-800">Your name</label>
                                <input id="customer_name" name="customer_name" type="text" value="<?php echo e(old('customer_name', auth()->user()?->name ?? '')); ?>" required maxlength="150" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="John Doe">
                            </div>
                            <div>
                                <label for="customer_email" class="mb-2 block text-sm font-bold text-gray-800">Email address <span class="font-normal text-gray-400">(optional)</span></label>
                                <input id="customer_email" name="customer_email" type="email" value="<?php echo e(old('customer_email', auth()->user()?->email ?? '')); ?>" maxlength="150" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="you@example.com">
                            </div>
                        </div>
                        <div class="mb-5">
                            <h2 class="text-lg font-black text-gray-900">Choose your components</h2>
                            <p class="mt-1 text-sm text-gray-500">Choose from currently available products. Select at least one component; our team will review your request and provide a quote.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <?php $__currentLoopData = $componentGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <label for="component-<?php echo e($type); ?>" class="mb-2 block text-sm font-bold text-gray-800"><?php echo e($label); ?></label>
                                    <select id="component-<?php echo e($type); ?>" name="components[<?php echo e($type); ?>]" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm transition focus:border-purple-500 focus:ring-purple-500">
                                        <option value="">Select <?php echo e($label); ?> (optional)</option>
                                        <?php $__currentLoopData = $groupedProducts[$type] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($product->id); ?>" <?php if((string) old('components.'.$type) === (string) $product->id): echo 'selected'; endif; ?>>
                                                <?php echo e($product->name); ?> — <?php echo e($product->availableStock()); ?> available
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <label for="build-notes" class="mt-5 block text-sm font-bold text-gray-800">
                            Additional notes <span class="font-normal text-gray-400">(optional)</span>
                            <textarea id="build-notes" name="notes" rows="3" maxlength="2000" placeholder="Budget, intended use, or other preferences" class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500"><?php echo e(old('notes')); ?></textarea>
                        </label>
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <?php if(auth()->guard()->check()): ?>
                                <button type="submit" class="inline-flex justify-center rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">Submit build request</button>
                            <?php else: ?>
                                <a href="<?php echo e(route('login')); ?>" class="inline-flex justify-center rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800">Sign in to submit a request</a>
                            <?php endif; ?>
                            <p class="text-xs text-gray-500">Our team will review your specifications and contact you with a quote.</p>
                        </div>
                    </section>
                </form>

            </div>

            <?php if(auth()->guard()->check()): ?>
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-xl font-black text-gray-900">PC builds</h2>
                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold text-purple-800"><?php echo e($builds->count()); ?> build(s)</span>
                    </div>
                    <?php $__empty_1 = true; $__currentLoopData = $builds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $build): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="mb-4 rounded-xl border border-gray-200 p-4 last:mb-0">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Build ID · <?php echo e($build->build_number); ?></p>
                                    <h3 class="mt-1 text-lg font-bold text-gray-900"><?php echo e($build->customer_name); ?></h3>
                                    <p class="text-sm text-gray-500"><?php echo e($build->created_at->format('M j, Y')); ?></p>
                                    <?php if($build->notes): ?>
                                        <p class="mt-2 whitespace-pre-line text-sm text-gray-700"><?php echo e($build->notes); ?></p>
                                    <?php endif; ?>
                                </div>
                                <span class="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800"><?php echo e(ucfirst($build->status)); ?></span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="<?php echo e(route('buildpc.show', $build)); ?>" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">View</a>
                                <a href="<?php echo e(route('buildpc.print', $build)); ?>" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Print</a>
                                <a href="<?php echo e(route('buildpc.customer.edit', $build)); ?>" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Edit</a>
                                <form action="<?php echo e(route('buildpc.customer.destroy', $build)); ?>" method="POST" onsubmit="return confirm('Delete this build?');" class="inline-block">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="inline-flex items-center justify-center rounded border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-red-700 transition hover:border-red-300 hover:bg-red-100">Delete</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">No builds yet. Start by building your first custom PC.</div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/store/build-pc.blade.php ENDPATH**/ ?>