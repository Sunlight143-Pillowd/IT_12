<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product', 'availableStock', 'showQuantityControls' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['product', 'availableStock', 'showQuantityControls' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $cartQuantity = (int) (session('cart', [])[$product->id] ?? 0);
?>

<div class="mt-4 flex flex-wrap items-center gap-2">
    <?php if($availableStock > 0): ?>
        <form method="POST" action="<?php echo e(route('cart.items.store', $product)); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
                    <?php if($cartQuantity >= $availableStock): echo 'disabled'; endif; ?>
                    class="rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:bg-gray-300">
                Add to cart
            </button>
        </form>
    <?php else: ?>
        <button type="button" disabled class="cursor-not-allowed rounded-md bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-600">
            Out of stock
        </button>
    <?php endif; ?>

    <?php if($cartQuantity > 0 || $showQuantityControls): ?>
        <?php if($cartQuantity > 0): ?>
            <form method="POST" action="<?php echo e(route('cart.items.update', $product)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="quantity" value="<?php echo e(max(1, min($availableStock, $cartQuantity - 1))); ?>">
                <button type="submit"
                        aria-label="Decrease quantity of <?php echo e($product->name); ?>"
                        <?php if($cartQuantity <= 1 || $availableStock < 1): echo 'disabled'; endif; ?>
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">−</button>
            </form>
        <?php else: ?>
            <button type="button" aria-label="Decrease quantity of <?php echo e($product->name); ?>" disabled class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 opacity-40">−</button>
        <?php endif; ?>
        <span class="min-w-6 text-center text-sm font-semibold text-gray-900" aria-label="Quantity in cart"><?php echo e($cartQuantity); ?></span>
        <?php if($cartQuantity > 0): ?>
            <form method="POST" action="<?php echo e(route('cart.items.update', $product)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="quantity" value="<?php echo e(min($availableStock, $cartQuantity + 1)); ?>">
                <button type="submit"
                        aria-label="Increase quantity of <?php echo e($product->name); ?>"
                        <?php if($cartQuantity >= $availableStock): echo 'disabled'; endif; ?>
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">+</button>
            </form>
            <form method="POST" action="<?php echo e(route('cart.items.destroy', $product)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit"
                        aria-label="Remove <?php echo e($product->name); ?> from cart"
                        class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                    Remove
                </button>
            </form>
        <?php else: ?>
            <form method="POST" action="<?php echo e(route('cart.items.store', $product)); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                        aria-label="Increase quantity of <?php echo e($product->name); ?>"
                        <?php if($availableStock < 1): echo 'disabled'; endif; ?>
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-lg font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">+</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\MYPC\Downloads\IT_12-main (2)\IT_12-main\resources\views\components\store\cart-controls.blade.php ENDPATH**/ ?>