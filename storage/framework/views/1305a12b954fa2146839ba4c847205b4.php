<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product', 'availableStock']));

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

foreach (array_filter((['product', 'availableStock']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
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
                    aria-label="Add <?php echo e($product->name); ?> to cart"
                    title="Add to cart"
                    <?php if($cartQuantity >= $availableStock): echo 'disabled'; endif; ?>
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:bg-gray-300">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                    <path d="M3 4h2l2.1 9.2a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.76L18.9 7H6.1" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="10" cy="17.5" r="1.25"/>
                    <circle cx="17" cy="17.5" r="1.25"/>
                </svg>
                <span>Add to cart</span>
            </button>
        </form>
    <?php else: ?>
        <button type="button" disabled class="cursor-not-allowed rounded-md bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-600">
            Out of stock
        </button>
    <?php endif; ?>

</div>
<?php /**PATH C:\Users\MYPC\Downloads\IT_12-main (3)\IT_12-main\resources\views/components/store/cart-controls.blade.php ENDPATH**/ ?>