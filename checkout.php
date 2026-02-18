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
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #ddd;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            color: #333;
        }

        .summary-table th,
        .summary-table td {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .total-row {
            font-weight: bold;
            color: #ff4d4d;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
            color: #333;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #ff4d4d;
            box-shadow: 0 0 5px rgba(255, 77, 77, 0.2);
            outline: none;
        }

        .btn-pay {
            width: 100%;
            background-color: #ff4d4d;
            font-size: 1.2rem;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-pay:hover {
            background-color: #d32f2f;
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