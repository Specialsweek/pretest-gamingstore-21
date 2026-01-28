<?php
require_once 'db.php';

try {
    // Drop old table to reset schema
    $pdo->exec("DROP TABLE IF EXISTS products");

    $sql = "CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        image VARCHAR(255),
        category VARCHAR(50),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);
    echo "Table 'products' recreated successfully with 'category' column.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>