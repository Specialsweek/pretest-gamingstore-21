<?php
ob_start();
require_once 'db.php';
require_once 'products.php';
session_start();

$productObj = new Product($pdo);
$cartItems = [];
$totalPrice = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $product = $productObj->getProductById($id);
        if ($product) {
            $product['quantity'] = $quantity;
            $product['line_total'] = $product['price'] * $quantity;
            $cartItems[] = $product;
            $totalPrice += $product['line_total'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once 'navbar.php'; ?>
    <div class="container">
        <a href="index.php" class="back-link">&larr; Continue Shopping</a>

        <div class="cart-container">
            <h1 style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Your
                Shopping Cart</h1>

            <?php if (empty($cartItems)): ?>
                <div class="empty-cart" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                    <p>Your cart is empty.</p>
                    <a href="index.php" class="simple-btn" style="margin-top: 1rem;">Browse Products</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><img src="<?= htmlspecialchars($item['image']) ?>"
                                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;"
                                        onerror="this.src='https://placehold.co/50'"></td>
                                <td>
                                    <?= htmlspecialchars($item['name']) ?>
                                </td>
                                <td>$
                                    <?= number_format($item['price'], 2) ?>
                                </td>
                                <td>
                                    <?= $item['quantity'] ?>
                                </td>
                                <td>$
                                    <?= number_format($item['line_total'], 2) ?>
                                </td>
                                <td>
                                    <a href="cart_action.php?action=remove&id=<?= $item['id'] ?>" class="simple-btn"
                                        style="background: var(--accent); color: #fff; padding: 0.5rem 1rem; font-size: 0.8rem;">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div
                    style="text-align: right; font-size: 1.5rem; color: var(--neon-cyan); font-weight: bold; margin-bottom: 2rem;">
                    Total: $<?= number_format($totalPrice, 2) ?>
                </div>

                <div style="text-align: right; display: flex; justify-content: flex-end; gap: 15px;">
                    <a href="cart_action.php?action=clear" class="simple-btn"
                        style="background: rgba(255,255,255,0.05); color: var(--text-primary);">Clear Cart</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="checkout.php" class="simple-btn">Proceed to Checkout</a>
                    <?php else: ?>
                        <a href="login.php" class="simple-btn">Login to Checkout</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>