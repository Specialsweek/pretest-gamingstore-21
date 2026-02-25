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
    <div class="container" style="padding-top: 0;">
        <header class="header-container">
            <h1 class="logo">MIRAI GEAR</h1>
            <div class="header-actions">
                <a href="cart.php" class="simple-btn">Cart</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="simple-btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="simple-btn">Login</a>
                <?php endif; ?>
            </div>
        </header>

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
                <div class="detail-description">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </div>

                <!-- Add to Cart Form -->
                <form method="POST" action="cart_action.php?action=add" style="margin-top: 2rem;">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="number" name="quantity" value="1" min="1" max="100"
                            style="padding: 10px; width: 60px; border-radius: 5px; border: 1px solid #ddd; background: #fff; color: #333;">
                        <button type="submit" class="btn">Add to Cart</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>