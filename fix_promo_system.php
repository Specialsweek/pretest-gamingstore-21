<?php
require_once 'db.php';

try {
    // 1. Update Promotions Table
    echo "Updating promotions table...<br>";
    $pdo->exec("DROP TABLE IF EXISTS promotions");
    $pdo->exec("CREATE TABLE promotions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        promo_code VARCHAR(100) NOT NULL UNIQUE,
        discount_type ENUM('percent', 'fixed') NOT NULL,
        discount_value DECIMAL(10, 2) NOT NULL,
        min_order_amount DECIMAL(10, 2) DEFAULT 0.00,
        max_discount DECIMAL(10, 2) DEFAULT NULL,
        start_date DATETIME DEFAULT NULL,
        end_date DATETIME DEFAULT NULL,
        usage_limit INT DEFAULT NULL,
        used_count INT DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Promotions table reset successfully.<br><br>";

    // 2. Update Orders Table
    echo "Checking orders table for updates...<br>";
    $ordersCols = $pdo->query("DESCRIBE orders")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('promo_code', $ordersCols)) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN promo_code VARCHAR(100) DEFAULT NULL AFTER address");
        echo "Added promo_code to orders.<br>";
    }
    if (!in_array('discount_amount', $ordersCols)) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN discount_amount DECIMAL(10, 2) DEFAULT 0.00 AFTER promo_code");
        echo "Added discount_amount to orders.<br>";
    }

    echo "<h3>Database System Upgrade Complete!</h3>";
    echo "<a href='index.php'>Go to Store</a>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>