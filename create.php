<?php
require_once 'db.php';
require_once 'products.php';
require_once 'auth_check.php';

requireAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productObj = new Product($pdo);
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    if ($productObj->createProduct($name, $price, $image, $category, $description)) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Failed to add product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Hardware - Computer Store</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Add New Hardware</h1>
        <div class="form-container">
            <a href="index.php" class="back-link">&larr; Back to Store</a>

            <?php if (isset($error)): ?>
                <p style="color: #ff4d4d; text-align: center;"><?= $error ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" required placeholder="e.g. GeForce RTX 4090">
                </div>
                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" required placeholder="e.g. 1599.99">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="Graphics Card">Graphics Card</option>
                        <option value="Processor">Processor</option>
                        <option value="Motherboard">Motherboard</option>
                        <option value="RAM">RAM</option>
                        <option value="Storage">Storage</option>
                        <option value="Power Supply">Power Supply</option>
                        <option value="Cooling">Cooling</option>
                        <option value="Case">Case</option>
                        <option value="Monitor">Monitor</option>
                        <option value="Peripherals">Peripherals</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="url" name="image" placeholder="https://example.com/gpu.jpg">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="5" placeholder="Product details..."></textarea>
                </div>
                <div style="text-align: center;">
                    <button type="submit" class="btn">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>