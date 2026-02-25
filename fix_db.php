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
        discount_code VARCHAR(100),
        expiry_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color: green;'>&#10004; Table 'promotions' verified.</p>";

    // 2. Contact Table
    echo "<p>Checking contact_info table...</p>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        address TEXT NOT NULL,
        hours TEXT NOT NULL,
        social_links TEXT NOT NULL,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $count = $pdo->query("SELECT COUNT(*) FROM contact_info")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO contact_info (store_name, email, phone, address, hours, social_links) VALUES 
        ('Mirai Gear HQ', 'support@miraigear.com', '+1 (555) 123-4567', '777 Neon Street, Cyber City, Futureland', 'Mon - Sat: 10:00 AM - 10:00 PM', 'Discord: miraigear, Twitter: @MiraiGear, FB: MiraiGearOfficial')");
        echo "<p style='color: green;'>&#10004; Contact info seeded.</p>";
    }
    echo "<p style='color: green;'>&#10004; Table 'contact_info' verified.</p>";

    echo "<h3>Database Repair Complete!</h3>";
    echo "<p><a href='index.php'>Go to Store</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>&#10008; Error: " . $e->getMessage() . "</p>";
}
?>