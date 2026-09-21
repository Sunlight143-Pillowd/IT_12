<?php
$activeEmployeeNav = 'quotation';
$pageTitle = 'Quotations — Davao Boss Computer';
include __DIR__ . '/header.php';

$products = $pdo->query(
    "SELECT id, name, category, type, price, stock_quantity
     FROM products
     WHERE is_active = 1
     ORDER BY name"
)->fetchAll();
?>

<section class="max-w-6xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2">
        <h1 class="text-2xl font-bold mb-4">Build a Quotation</h1>
        <input type="text" id="product-search" placeholder="Search products…"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-4">

        <div id="product-grid" class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <?php foreach ($products as $product): ?>
                <button type="button" class="pos-product-btn text-left border border-gray-200 rounded p-3 hover:border-purple-600 bg-white"
                        data-search="<?= htmlspecialchars(strtolower($product['name'] . ' ' . $product['category']), ENT_QUOTES, 'UTF-8') ?>"
                        data-id="<?= (int) $product['id'] ?>"
                        data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                        data-price="<?= (float) $product['price'] ?>">
                    <p class="text-sm font-semibold"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-xs text-gray-500"><?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-sm font-bold text-purple-600 mt-1">₱<?= number_format($product['price']) ?></p>
                    <?php if ($product['stock_quantity'] <= 0): ?>
                        <p class="text-[11px] text-red-500">Out of stock</p>
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-bold mb-4">Quotation Details</h2>
        <form id="quote-form" method="post" action="quotation-save.php" class="bg-white border border-gray-200 rounded p-4">
            <input type="hidden" name="cart_json" id="cart-json-input">

            <input type="text" name="customer_name" placeholder="Customer name"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-3">
            <input type="text" name="customer_contact" placeholder="Phone or email"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-3">
            <textarea name="notes" placeholder="Notes (optional)" rows="2"
                      class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-4"></textarea>

            <div id="cart-items" class="space-y-2 mb-4 text-sm max-h-64 overflow-y-auto">
                <p class="text-gray-400 text-sm" id="cart-empty-msg">No items yet.</p>
            </div>

            <div class="border-t border-gray-200 pt-3 flex items-center justify-between font-bold">
                <span>Total</span>
                <span id="cart-total">₱0.00</span>
            </div>

            <button type="submit" id="checkout-btn" disabled
                    class="w-full mt-4 bg-purple-600 hover:bg-purple-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold text-sm tracking-wide px-6 py-3 rounded">
                Save &amp; Print Quotation
            </button>
        </form>
    </div>
</section>

<script>
(function () {
    const cart = new Map();

    const cartItemsEl = document.getElementById('cart-items');
    const cartEmptyMsg = document.getElementById('cart-empty-msg');
    const cartTotalEl = document.getElementById('cart-total');
    const cartJsonInput = document.getElementById('cart-json-input');
    const checkoutBtn = document.getElementById('checkout-btn');

    function money(n) {
        return '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function render() {
        cartItemsEl.innerHTML = '';
        if (cart.size === 0) {
            cartItemsEl.appendChild(cartEmptyMsg);
            checkoutBtn.disabled = true;
            cartTotalEl.textContent = money(0);
            cartJsonInput.value = '';
            return;
        }

        let total = 0;
        cart.forEach((item, id) => {
            const subtotal = item.price * item.qty;
            total += subtotal;

            const row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-2';
            row.innerHTML = `
                <div class="flex-1 min-w-0">
                    <p class="font-medium truncate">${item.name}</p>
                    <p class="text-xs text-gray-500">${money(item.price)} × ${item.qty} = ${money(subtotal)}</p>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" class="qty-btn w-6 h-6 border border-gray-300 rounded" data-id="${id}" data-delta="-1">−</button>
                    <button type="button" class="qty-btn w-6 h-6 border border-gray-300 rounded" data-id="${id}" data-delta="1">+</button>
                    <button type="button" class="remove-btn text-red-500 text-xs ml-1" data-id="${id}">Remove</button>
                </div>
            `;
            cartItemsEl.appendChild(row);
        });

        cartTotalEl.textContent = money(total);
        checkoutBtn.disabled = false;

        const cartArray = Array.from(cart, ([id, item]) => ({
            product_id: Number(id), name: item.name, price: item.price, quantity: item.qty,
        }));
        cartJsonInput.value = JSON.stringify(cartArray);
    }

    document.querySelectorAll('.pos-product-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const existing = cart.get(id);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.set(id, { name: btn.dataset.name, price: Number(btn.dataset.price), qty: 1 });
            }
            render();
        });
    });

    cartItemsEl.addEventListener('click', (e) => {
        const qtyBtn = e.target.closest('.qty-btn');
        const removeBtn = e.target.closest('.remove-btn');

        if (qtyBtn) {
            const id = qtyBtn.dataset.id;
            const item = cart.get(id);
            const newQty = item.qty + Number(qtyBtn.dataset.delta);
            if (newQty <= 0) {
                cart.delete(id);
            } else {
                item.qty = newQty;
            }
            render();
        }

        if (removeBtn) {
            cart.delete(removeBtn.dataset.id);
            render();
        }
    });

    document.getElementById('product-search').addEventListener('input', function () {
        const term = this.value.trim().toLowerCase();
        document.querySelectorAll('.pos-product-btn').forEach((btn) => {
            btn.style.display = btn.dataset.search.includes(term) ? '' : 'none';
        });
    });

    document.getElementById('quote-form').addEventListener('submit', (e) => {
        if (cart.size === 0) e.preventDefault();
    });
})();
</script>

<?php include __DIR__ . '/footer.php'; ?>
