<?php
ob_start();
require_once 'db.php';
require_once 'products.php';
require_once 'auth_check.php';

requireAdmin();

$productObj = new Product($pdo);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $product = $productObj->getProductById($id);

    if (!$product) {
        die("Product not found!");
    }
} else {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $low_stock_threshold = $_POST['low_stock_threshold'];

    if ($productObj->updateProduct($id, $name, $price, $image, $category, $description, $stock, $low_stock_threshold)) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Failed to update product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Computer Store</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once 'navbar.php'; ?>
    <div class="container">
        <div class="form-container" style="max-width: 700px;">
            <h1>Edit Product</h1>
            <a href="index.php" class="back-link">&larr; Back to Store</a>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>"
                        required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <?php
                        $categories = ["Graphics Card", "Processor", "Motherboard", "RAM", "Storage", "Power Supply", "Cooling", "Case", "Monitor", "Peripherals", "Accessories"];
                        foreach ($categories as $cat) {
                            $selected = ($product['category'] == $cat) ? 'selected' : '';
                            echo "<option value=\"$cat\" $selected>$cat</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Image URL (or Base64)</label>
                    <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>">
                </div>

                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" min="0"
                        required>
                </div>

                <div class="form-group">
                    <label>Low Stock Threshold</label>
                    <input type="number" name="low_stock_threshold"
                        value="<?= htmlspecialchars($product['low_stock_threshold']) ?>" min="0" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="5"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>
                <div style="text-align: center;">
                    <button type="submit" class="simple-btn">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>