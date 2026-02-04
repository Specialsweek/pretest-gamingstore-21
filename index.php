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
    <title>Gaming Store</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Gaming Store</h1>
            <div>
                <?php
                $cartCount = 0;
                if (isset($_SESSION['cart'])) {
                    $cartCount = array_sum($_SESSION['cart']);
                }
                ?>
            <a href="cart.php" class="btn" style="background-color: #f39c12; margin-right: 15px;">
                Cart (
                <?= $cartCount ?>)
            </a>

            <?php if (isLoggedIn()): ?>
                <span style="color: #fff; margin-right: 15px;">Welcome,
                    <strong>
                        <?= htmlspecialchars($_SESSION['username']) ?>
                    </strong> (
                    <?= $_SESSION['role'] ?>)
                </span>
                        <a href="logout.php" class="btn" style="background-color: #ff4d4d;">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn">Login</a>
                        <a href="register.php" class="btn" style="background-color: #4CAF50;">Register</a>
            <?php endif; ?>
        </div>
    </div>
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
                        <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-danger"      onclick="return confirm('Are
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