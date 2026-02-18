<?php
ob_start();
session_start();
require_once 'auth_check.php';
require_once 'db.php';

requireLogin();
$userId = $_SESSION['user_id'];

// Fetch orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Gaming Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .orders-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #ddd;
        }

        .order-card {
            background: #f8f9fa;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 5px solid #ff4d4d;
            border: 1px solid #eee;
            border-left-width: 5px;
            border-left-color: #ff4d4d;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .order-id {
            font-weight: bold;
            color: #333;
        }

        .order-date {
            color: #666;
            font-size: 0.9rem;
        }

        .order-total {
            color: #ff4d4d;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .item-list {
            list-style: none;
            padding: 0;
            margin: 0;
            color: #555;
        }

        .item-list li {
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
        }

        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 2rem;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="index.php" class="back-link">&larr; Back to Store</a>

        <div class="orders-container">
            <h1 style="margin-bottom: 2rem;">My Order History</h1>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-msg">Thank you! Your order has been placed successfully.</div>
            <?php endif; ?>

            <?php if (empty($orders)): ?>
                <p style="text-align: center; color: #bbb;">You haven't placed any orders yet.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-id">Order #
                                    <?= $order['id'] ?>
                                </div>
                                <div class="order-date">
                                    <?= $order['created_at'] ?>
                                </div>
                            </div>
                            <div class="order-total">$
                                <?= number_format($order['total_price'], 2) ?>
                            </div>
                        </div>
                        <div style="margin-bottom: 1rem; font-size: 0.9rem; color: #aaa;">
                            <strong>Shipping to:</strong>
                            <?= htmlspecialchars($order['address']) ?>
                        </div>

                        <!-- Fetch Items for this order -->
                        <?php
                        $itemStmt = $pdo->prepare("
                            SELECT oi.*, p.name 
                            FROM order_items oi 
                            JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?
                        ");
                        $itemStmt->execute([$order['id']]);
                        $items = $itemStmt->fetchAll();
                        ?>
                        <ul class="item-list">
                            <?php foreach ($items as $item): ?>
                                <li>
                                    <span>
                                        <?= htmlspecialchars($item['name']) ?> (x
                                        <?= $item['quantity'] ?>)
                                    </span>
                                    <span>$
                                        <?= number_format($item['price'] * $item['quantity'], 2) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>