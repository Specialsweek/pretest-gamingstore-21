<?php
require_once 'db.php';

echo "<h2>Mirai Gear: Promotions Database Repair</h2>";
echo "<p>Starting database migration...</p>";

try {
    // 1. Create promotions table if it doesn't exist
    $promoSql = "CREATE TABLE IF NOT EXISTS promotions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        discount_code VARCHAR(100),
        expiry_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($promoSql);

    // Check if discount_code column exists
    $columns = $pdo->query("DESCRIBE promotions")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('discount_code', $columns)) {
        echo "<p>Adding missing 'discount_code' column...</p>";
        $pdo->exec("ALTER TABLE promotions ADD COLUMN discount_code VARCHAR(100) AFTER description");
        echo "<p style='color: green;'>&#10004; Column 'discount_code' added.</p>";
    }

    echo "<p style='color: green;'>&#10004; Table 'promotions' verified successfully.</p>";

    echo "<h3>Migration Complete!</h3>";
    echo "<p><a href='promotions.php'>Click here to go to Promotions Page</a></p>";
    echo "<p style='color: #ff0055;'><strong>Security Note:</strong> Please delete this file (fix_promotions.php) after running it.</p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>&#10008; Error: " . $e->getMessage() . "</p>";
}
?>