<?php
require_once 'db.php';

echo "<h2>Mirai Gear: Comprehensive Database System Repair</h2>";
echo "<p>Performing deep integrity check on all tables...</p>";

try {
    // 1. Repair orders Table
    echo "<h4>Checking table: orders</h4>";
    $stmt = $pdo->query("DESCRIBE orders");
    $ordersCols = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $requiredOrders = [
        'promo_code' => "ALTER TABLE orders ADD COLUMN promo_code VARCHAR(100) DEFAULT NULL AFTER address",
        'discount_amount' => "ALTER TABLE orders ADD COLUMN discount_amount DECIMAL(10, 2) DEFAULT 0.00 AFTER promo_code"
    ];

    foreach ($requiredOrders as $col => $sql) {
        if (!in_array($col, $ordersCols)) {
            echo "<p>Adding missing column '$col' to orders...</p>";
            $pdo->exec($sql);
            echo "<p style='color: green;'>&#10004; Column '$col' added.</p>";
        }
    }

    // 2. Repair promotions Table
    echo "<h4>Checking table: promotions</h4>";
    $stmt = $pdo->query("DESCRIBE promotions");
    $promoCols = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // If 'promo_code' is missing, it's a very old table. Rebuild it.
    if (!in_array('promo_code', $promoCols)) {
        echo "<p style='color: orange;'>Outdated schema detected. Rebuilding promotions table...</p>";
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
        echo "<p style='color: green;'>&#10004; Table 'promotions' rebuilt.</p>";
    } else {
        // Table exists but might be missing new columns (like 'status')
        $requiredPromo = [
            'discount_type' => "ALTER TABLE promotions ADD COLUMN discount_type ENUM('percent', 'fixed') NOT NULL AFTER promo_code",
            'discount_value' => "ALTER TABLE promotions ADD COLUMN discount_value DECIMAL(10, 2) NOT NULL AFTER discount_type",
            'min_order_amount' => "ALTER TABLE promotions ADD COLUMN min_order_amount DECIMAL(10, 2) DEFAULT 0.00 AFTER discount_value",
            'max_discount' => "ALTER TABLE promotions ADD COLUMN max_discount DECIMAL(10, 2) DEFAULT NULL AFTER min_order_amount",
            'start_date' => "ALTER TABLE promotions ADD COLUMN start_date DATETIME DEFAULT NULL AFTER max_discount",
            'end_date' => "ALTER TABLE promotions ADD COLUMN end_date DATETIME DEFAULT NULL AFTER start_date",
            'usage_limit' => "ALTER TABLE promotions ADD COLUMN usage_limit INT DEFAULT NULL AFTER end_date",
            'used_count' => "ALTER TABLE promotions ADD COLUMN used_count INT DEFAULT 0 AFTER usage_limit",
            'status' => "ALTER TABLE promotions ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER used_count"
        ];

        foreach ($requiredPromo as $col => $sql) {
            if (!in_array($col, $promoCols)) {
                echo "<p>Adding missing column '$col' to promotions...</p>";
                try {
                    $pdo->exec($sql);
                    echo "<p style='color: green;'>&#10004; Column '$col' added.</p>";
                } catch (Exception $e) {
                    echo "<p style='color: red;'>&#10008; Failed to add '$col': " . $e->getMessage() . "</p>";
                }
            }
        }
    }

    echo "<h3>System Repair Complete!</h3>";
    echo "<p><a href='promotions.php' class='btn'>Back to Promotions</a> | <a href='index.php' class='btn'>Go to Store</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>&#10008; Critical Error during repair: " . $e->getMessage() . "</p>";
    echo "<p>Suggestion: If the error persists, run <a href='database_setup.php'>database_setup.php</a> to reset everything.</p>";
}
?>

<style>
    body { background: #0a0a0a; color: #eee; font-family: 'Segoe UI', sans-serif; padding: 2rem; }
    h2, h3, h4 { color: #00f2ff; }
    .btn { display: inline-block; padding: 10px 20px; background: #bc13fe; color: white; text-decoration: none; border-radius: 5px; margin-right: 1rem; }
</style>