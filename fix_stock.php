<?php
require_once 'db.php';

echo "<h2>Mirai Gear: Stock System Migration</h2>";
echo "<p>Adding stock management columns to products table...</p>";

try {
    $stmt = $pdo->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('stock', $columns)) {
        echo "<p>Adding 'stock' column...</p>";
        $pdo->exec("ALTER TABLE products ADD COLUMN stock INT NOT NULL DEFAULT 50 AFTER image");
        echo "<p style='color: green;'>&#10004; Column 'stock' added (Defaulted to 50 for existing items).</p>";
    }

    if (!in_array('low_stock_threshold', $columns)) {
        echo "<p>Adding 'low_stock_threshold' column...</p>";
        $pdo->exec("ALTER TABLE products ADD COLUMN low_stock_threshold INT DEFAULT 5 AFTER stock");
        echo "<p style='color: green;'>&#10004; Column 'low_stock_threshold' added.</p>";
    }

    echo "<h3>Migration Successful!</h3>";
    echo "<p><a href='index.php'>Go to Shop</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>&#10008; Migration Failed: " . $e->getMessage() . "</p>";
}
?>