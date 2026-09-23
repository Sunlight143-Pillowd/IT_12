<?php
session_start();

// Handle quantity updates / removals before rendering
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $action    = $_POST['action'] ?? '';

    if (isset($_SESSION['cart'][$productId])) {
        if ($action === 'increase') {
            $_SESSION['cart'][$productId]['qty'] += 1;
        } elseif ($action === 'decrease') {
            $_SESSION['cart'][$productId]['qty'] = max(1, $_SESSION['cart'][$productId]['qty'] - 1);
        } elseif ($action === 'remove') {
            unset($_SESSION['cart'][$productId]);
        }
    }

    header('Location: /cart.php');
    exit;
}

$pageTitle = 'Your Cart — Davao Boss Computer';
$activeNav = 'cart';
require __DIR__ . '/header.php';

$cart = $_SESSION['cart'] ?? [];
$subtotal = 0;
foreach ($cart as $line) {
    $subtotal += $line['price'] * $line['qty'];
}
?>

<section class="bg-white">
    <div class="max-w-4xl mx-auto px-4 py-10">
        <h1 class="text-2xl md:text-3xl font-extrabold mb-8">Your Cart</h1>

        <?php if (empty($cart)): ?>
            <div class="text-center py-16 border border-dashed border-gray-300 rounded">
                <p class="text-gray-500 mb-4">Your cart is empty.</p>
                <a href="accessories.php" class="inline-block bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-6 py-3">
                    BROWSE THE GEAR SHOP
                </a>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-200 border-t border-b border-gray-200">
                <?php foreach ($cart as $line): ?>
                    <div class="py-4 flex items-center gap-4">
                        <div class="placeholder-img w-20 h-20 rounded flex items-center justify-center text-gray-400 text-[9px] text-center shrink-0">
                            [ Image ]
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold"><?= htmlspecialchars($line['name']) ?></p>
                            <p class="text-xs text-gray-500">₱<?= number_format($line['price']) ?> each</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <form method="post">
                                <input type="hidden" name="product_id" value="<?= (int) $line['id'] ?>">
                                <input type="hidden" name="action" value="decrease">
                                <button type="submit" class="border border-gray-300 w-7 h-7 text-sm">−</button>
                            </form>
                            <span class="text-sm w-6 text-center"><?= (int) $line['qty'] ?></span>
                            <form method="post">
                                <input type="hidden" name="product_id" value="<?= (int) $line['id'] ?>">
                                <input type="hidden" name="action" value="increase">
                                <button type="submit" class="border border-gray-300 w-7 h-7 text-sm">+</button>
                            </form>
                        </div>
                        <p class="text-sm font-bold w-20 text-right">₱<?= number_format($line['price'] * $line['qty']) ?></p>
                        <form method="post">
                            <input type="hidden" name="product_id" value="<?= (int) $line['id'] ?>">
                            <input type="hidden" name="action" value="remove">
                            <button type="submit" class="text-gray-400 hover:text-red-600 text-xs">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6 flex flex-col items-end gap-2">
                <div class="flex justify-between w-full max-w-xs text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>₱<?= number_format($subtotal) ?></span>
                </div>
                <div class="flex justify-between w-full max-w-xs text-base font-bold">
                    <span>Total</span>
                    <span>₱<?= number_format($subtotal) ?></span>
                </div>
                <a href="checkout.php" class="mt-4 inline-block bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-6 py-3">
                    CHECKOUT
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
