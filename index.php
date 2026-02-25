<?php
require_once 'db.php';
require_once 'products.php';
require_once 'auth_check.php'; // Session started here

$productObj = new Product($pdo);
$products = $productObj->getAllProducts();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">

        <!-- Content starts here -->
    </div>

    <?php if (isAdmin()): ?>
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="create.php" class="btn">Add New Product</a>
        </div>
    <?php endif; ?>

    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <a href="product_details.php?id=<?= $product['id'] ?>">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                        class="product-image" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                </a>
                <div class="product-info">
                    <span class="product-platform">
                        <?= htmlspecialchars($product['category']) ?>
                    </span>
                    <h3 class="product-title">
                        <a href="product_details.php?id=<?= $product['id'] ?>"
                            style="color: inherit; text-decoration: none;">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </h3>
                    <p class="product-price">$
                        <?= number_format($product['price'], 2) ?>
                    </p>

                    <?php if (isAdmin()): ?>
                        <div class="actions">
                            <a href="edit.php?id=<?= $product['id'] ?>" class="btn">Edit</a>
                            <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('Are
                    you sure?')">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    </div>
</body>

</html>