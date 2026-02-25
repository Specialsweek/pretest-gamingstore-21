<?php
require_once 'db.php';

echo "<h2>Mirai Gear: Unified Database Repair</h2>";

try {
    // 1. Promotions Table
    echo "<p>Checking promotions table...</p>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS promotions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        discount_code VARCHAR(100) DEFAULT NULL,
        expiry_date DATE DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $stmt = $pdo->query("DESCRIBE promotions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $columnNames = array_column($columns, 'Field');

    // Check for discount_code
    if (!in_array('discount_code', $columnNames)) {
        echo "<p>Adding missing 'discount_code' column to promotions...</p>";
        $pdo->exec("ALTER TABLE promotions ADD COLUMN discount_code VARCHAR(100) DEFAULT NULL AFTER description");
        echo "<p style='color: green;'>&#10004; Column 'discount_code' added.</p>";
    }

    // Aggressive fix: If promo_code exists and is required, make it optional
    foreach ($columns as $col) {
        if ($col['Field'] === 'promo_code' && $col['Null'] === 'NO' && $col['Default'] === null) {
            echo "<p>Detected required 'promo_code' field. Normalizing...</p>";
            $pdo->exec("ALTER TABLE promotions MODIFY COLUMN promo_code VARCHAR(100) DEFAULT NULL");
            echo "<p style='color: green;'>&#10004; Field 'promo_code' made optional.</p>";
        }
    }

    echo "<p style='color: green;'>&#10004; Table 'promotions' verified.</p>";


    echo "<h3>Database Repair Complete!</h3>";
    echo "<p><a href='index.php'>Go to Store</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>&#10008; Error: " . $e->getMessage() . "</p>";
}
?>