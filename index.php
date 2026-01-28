<?php
require_once 'db.php';
require_once 'products.php';

$productObj = new Product($pdo);
$products = $productObj->getAllProducts();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Store</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Gaming Store</h1>
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="create.php" class="btn">Add New Product</a>
        </div>

        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                        class="product-image" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                    <div class="product-info">
                        <span class="product-platform"><?= htmlspecialchars($product['category']) ?></span>
                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
                        <div class="actions">
                            <a href="edit.php?id=<?= $product['id'] ?>" class="btn">Edit</a>
                            <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>