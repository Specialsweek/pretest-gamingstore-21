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
        <?= htmlspecialchars($product['name']) ?> - Gaming Store
    </title>
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-container {
            max-width: 900px;
            margin: 50px auto;
            background: #2a2a2a;
            padding: 30px;
            border-radius: 10px;
            display: flex;
            gap: 40px;
            align-items: start;
        }

        .detail-image {
            width: 100%;
            max-width: 400px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .detail-info {
            flex: 1;
            color: #fff;
        }

        .detail-price {
            font-size: 2rem;
            color: #4CAF50;
            margin: 1rem 0;
            font-weight: bold;
        }

        .detail-category {
            display: inline-block;
            background: #444;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #bbb;
        }

        .detail-description {
            line-height: 1.6;
            color: #ccc;
            margin-bottom: 2rem;
            white-space: pre-wrap;
            /* Preserve newlines */
        }
    </style>
</head>

<body>
    <div class="container">
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

                <!-- Placeholder for future functionality like "Add to Cart" -->
                <button class="btn" onclick="alert('Added to cart! (Demo)')">Add to Cart</button>
            </div>
        </div>
    </div>
</body>

</html>