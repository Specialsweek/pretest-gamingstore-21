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
    <title>Checkout - Gaming Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 2rem;
            background: #2a2a2a;
            border-radius: 10px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            color: #ccc;
        }

        .summary-table th,
        .summary-table td {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #444;
        }

        .total-row {
            font-weight: bold;
            color: #4CAF50;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #444;
            border-radius: 5px;
            background: #333;
            color: #fff;
        }

        .btn-pay {
            width: 100%;
            background-color: #4CAF50;
            font-size: 1.2rem;
        }

        .btn-pay:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="cart.php" class="back-link">&larr; Back to Cart</a>

        <div class="checkout-container">
            <h1 style="border-bottom: 1px solid #444; padding-bottom: 1rem; margin-bottom: 2rem;">Checkout</h1>

            <table class="summary-table">
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
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right;">Total To Pay:</td>
                        <td>$
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
                <button type="submit" class="btn btn-pay">Confirm Order ($
                    <?= number_format($totalPrice, 2) ?>)
                </button>
            </form>
        </div>
    </div>
</body>

</html>