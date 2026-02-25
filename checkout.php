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

            <form action="process_checkout.php" method="POST">
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea name="address" rows="4" required
                        placeholder="Enter your full shipping address..."></textarea>
                </div>
                <div class="form-group">
                    <label>Credit Card Number (Fake)</label>
                    <input type="text" placeholder="XXXX-XXXX-XXXX-XXXX" required>
                </div>
                <button type="submit" class="simple-btn" style="width: 100%; padding: 15px; font-size: 1.1rem;">Confirm
                    Order ($
                    <?= number_format($totalPrice, 2) ?>)
                </button>
            </form>
        </div>
    </div>
</body>

</html>