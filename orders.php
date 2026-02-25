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
    <title>My Orders - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <a href="index.php" class="back-link">&larr; Back to Store</a>

        <div class="orders-container">
            <h1 style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">My
                Order History</h1>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success" style="margin-bottom: 2rem;">Thank you! Your order has been placed
                    successfully.</div>
            <?php endif; ?>

            <?php if (empty($orders)): ?>
                <p style="text-align: center; color: var(--text-secondary); padding: 3rem;">You haven't placed any orders
                    yet.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div
                        style="background: rgba(255,255,255,0.02); margin-bottom: 1.5rem; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); border-left: 4px solid var(--neon-cyan);">
                        <div
                            style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
                            <div>
                                <div style="font-weight: bold; color: var(--text-primary);">Order #<?= $order['id'] ?></div>
                                <div style="color: var(--text-secondary); font-size: 0.85rem;"><?= $order['created_at'] ?></div>
                            </div>
                            <div style="color: var(--neon-cyan); font-weight: bold; font-size: 1.2rem;">
                                $<?= number_format($order['total_price'], 2) ?>
                            </div>
                        </div>
                        <div style="margin-bottom: 1rem; font-size: 0.9rem; color: var(--text-secondary);">
                            <strong style="color: var(--text-primary);">Shipping to:</strong>
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
                        <ul style="list-style: none; padding: 0; margin: 0; color: var(--text-secondary);">
                            <?php foreach ($items as $item): ?>
                                <li
                                    style="margin-bottom: 0.5rem; display: flex; justify-content: space-between; font-size: 0.9rem;">
                                    <span><?= htmlspecialchars($item['name']) ?> <span
                                            style="color: var(--text-secondary)">(x<?= $item['quantity'] ?>)</span></span>
                                    <span
                                        style="color: var(--text-primary);">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
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