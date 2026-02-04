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
    <title>Shopping Cart - Gaming Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 2rem;
            background: #2a2a2a;
            border-radius: 10px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            color: #fff;
        }

        .cart-table th,
        .cart-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #444;
        }

        .cart-table th {
            background: #333;
        }

        .cart-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .total-section {
            text-align: right;
            font-size: 1.5rem;
            color: #4CAF50;
            font-weight: bold;
            margin-bottom: 2rem;
        }

        .empty-cart {
            text-align: center;
            color: #bbb;
            padding: 3rem;
        }

        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="index.php" class="back-link">&larr; Continue Shopping</a>

        <div class="cart-container">
            <h1 style="margin-bottom: 2rem; border-bottom: 1px solid #444; padding-bottom: 1rem;">Your Shopping Cart
            </h1>

            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <p>Your cart is empty.</p>
                    <a href="index.php" class="btn" style="margin-top: 1rem;">Browse Products</a>
                </div>
            <?php else: ?>
                <table class="cart-table">
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
                                <td><img src="<?= htmlspecialchars($item['image']) ?>" class="cart-image"
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
                                    <a href="cart_action.php?action=remove&id=<?= $item['id'] ?>"
                                        class="btn btn-danger btn-sm">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="total-section">
                    Total: $
                    <?= number_format($totalPrice, 2) ?>
                </div>

                <div style="text-align: right;">
                    <a href="cart_action.php?action=clear" class="btn"
                        style="background-color: #777; margin-right: 10px;">Clear Cart</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="checkout.php" class="btn">Proceed to Checkout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn">Login to Checkout</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>