<?php
ob_start();
require_once 'db.php';
require_once 'products.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$productObj = new Product($pdo);
$product = $productObj->getProductById($_GET['id']);

if (!$product) {
    die("Product not found!");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($product['name']) ?> - Mirai Gear
    </title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <a href="index.php" class="back-link" style="margin-left: 0;">&larr; Back to Store</a>

    <div class="detail-container">
        <div>
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                class="detail-image" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
        </div>
        <div class="detail-info">
            <span class="detail-category">
                <?= htmlspecialchars($product['category']) ?>
            </span>
            <h1 style="margin: 10px 0;">
                <?= htmlspecialchars($product['name']) ?>
            </h1>
            <p class="detail-price">$
                <?= number_format($product['price'], 2) ?>
            </p>
            <div class="stock-status" style="margin-bottom: 1.5rem; font-size: 1rem; font-weight: bold;">
                <?php if ($product['stock'] > $product['low_stock_threshold']): ?>
                    <span style="color: #00FF9D;">In Stock (<?= $product['stock'] ?> left)</span>
                <?php elseif ($product['stock'] > 0): ?>
                    <span style="color: #FFB800;">Low Stock! (<?= $product['stock'] ?> left)</span>
                <?php else: ?>
                    <span style="color: #FF0055;">Out of Stock</span>
                <?php endif; ?>
            </div>

            <div class="detail-description">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>

            <!-- Add to Cart Form -->
            <form method="POST" action="cart_action.php?action=add" style="margin-top: 2rem;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <?php if ($product['stock'] > 0): ?>
                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>"
                            style="padding: 10px; width: 80px; border-radius: 5px; border: 1px solid var(--border-color); background: rgba(0,0,0,0.3); color: white;">
                        <button type="submit" class="btn">Add to Cart</button>
                    <?php else: ?>
                        <button type="button" class="btn" disabled
                            style="opacity: 0.5; cursor: not-allowed; background: #333;">Out of Stock</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    </div>
</body>

</html>