<?php
ob_start();
session_start();
require_once 'auth_check.php';
require_once 'db.php';
require_once 'products.php';

requireLogin();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$productObj = new Product($pdo);
$cartItems = [];
$totalPrice = 0;

foreach ($_SESSION['cart'] as $id => $quantity) {
    $product = $productObj->getProductById($id);
    if ($product) {
        $line_total = $product['price'] * $quantity;
        $cartItems[] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'line_total' => $line_total
        ];
        $totalPrice += $line_total;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once 'navbar.php'; ?>
    <div class="container">
        <a href="cart.php" class="back-link">&larr; Back to Cart</a>

        <div class="checkout-container">
            <h1 style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 2rem;">
                Checkout</h1>

            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <td>
                                <?= $item['quantity'] ?>
                            </td>
                            <td>$
                                <?= number_format($item['line_total'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight: bold; color: var(--neon-cyan); font-size: 1.2rem;">
                        <td colspan="2" style="text-align: right; border-bottom: none;">Total To Pay:</td>
                        <td style="border-bottom: none;">$
                            <?= number_format($totalPrice, 2) ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="promo-section"
                style="margin-top: 2rem; padding: 1.5rem; background: rgba(255, 255, 255, 0.05); border-radius: 8px; border: 1px dashed var(--border-color);">
                <h3 style="margin-bottom: 1rem; font-size: 1rem; color: var(--text-secondary);">PROMO CODE</h3>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="promo-input" placeholder="Enter code (e.g., MIRAI20)"
                        style="flex-grow: 1; padding: 10px; background: rgba(0,0,0,0.3); border: 1px solid var(--border-color); color: white; border-radius: 4px;">
                    <button type="button" id="apply-promo-btn" class="simple-btn"
                        style="padding: 10px 20px;">Apply</button>
                </div>
                <div id="promo-message" style="margin-top: 10px; font-size: 0.85rem;"></div>
            </div>

            <div id="summary-section"
                style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Subtotal:</span>
                    <span>$<?= number_format($totalPrice, 2) ?></span>
                </div>
                <div id="discount-row"
                    style="display: none; justify-content: space-between; margin-bottom: 0.5rem; color: var(--neon-purple);">
                    <span>Discount:</span>
                    <span>-$<span id="discount-amount">0.00</span></span>
                </div>
                <div
                    style="display: flex; justify-content: space-between; font-weight: bold; color: var(--neon-cyan); font-size: 1.4rem; margin-top: 1rem;">
                    <span>Total To Pay:</span>
                    <span>$<span id="final-total"><?= number_format($totalPrice, 2) ?></span></span>
                </div>
            </div>

            <form action="process_checkout.php" method="POST" id="checkout-form" style="margin-top: 2rem;">
                <input type="hidden" name="promo_code" id="hidden-promo-code" value="">
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea name="address" rows="4" required
                        placeholder="Enter your full shipping address..."></textarea>
                </div>
                <div class="form-group">
                    <label>Credit Card Number (Fake)</label>
                    <input type="text" placeholder="XXXX-XXXX-XXXX-XXXX" required>
                </div>
                <button type="submit" class="simple-btn" style="width: 100%; padding: 15px; font-size: 1.1rem;">
                    Confirm Order
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('apply-promo-btn').addEventListener('click', function () {
            const code = document.getElementById('promo-input').value.trim();
            const total = <?= $totalPrice ?>;
            const msgDiv = document.getElementById('promo-message');

            if (!code) return;

            fetch('validate_promo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `code=${encodeURIComponent(code)}&total=${total}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        msgDiv.style.color = '#00FF9D';
                        msgDiv.textContent = data.message;

                        document.getElementById('discount-row').style.display = 'flex';
                        document.getElementById('discount-amount').textContent = data.discount.toFixed(2);
                        document.getElementById('final-total').textContent = (total - data.discount).toFixed(2);
                        document.getElementById('hidden-promo-code').value = data.code;
                    } else {
                        msgDiv.style.color = '#FF0055';
                        msgDiv.textContent = data.message;

                        document.getElementById('discount-row').style.display = 'none';
                        document.getElementById('final-total').textContent = total.toFixed(2);
                        document.getElementById('hidden-promo-code').value = '';
                    }
                });
        });
    </script>
</body>

</html>