<?php
require_once 'db.php';

/**
 * Utility script to fix the missing contact_info table error.
 * This script is safer than running the full database_setup.php 
 * as it avoids resetting products or users.
 */

echo "<h2>Mirai Gear: Database Repair Script</h2>";
echo "<p>Starting database migration...</p>";

try {
    // 1. Create contact_info table if it doesn't exist
    $contactSql = "CREATE TABLE IF NOT EXISTS contact_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        address TEXT NOT NULL,
        hours TEXT NOT NULL,
        social_links TEXT NOT NULL,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $pdo->exec($contactSql);
    echo "<p style='color: green;'>&#10004; Table 'contact_info' verified/created successfully.</p>";

    // 2. Seed initial data only if table is empty
    $checkSql = "SELECT COUNT(*) FROM contact_info";
    $count = $pdo->query($checkSql)->fetchColumn();

    if ($count == 0) {
        $seedSql = "INSERT INTO contact_info (store_name, email, phone, address, hours, social_links) VALUES 
        ('Mirai Gear HQ', 'support@miraigear.com', '+1 (555) 123-4567', '777 Neon Street, Cyber City, Futureland', 'Mon - Sat: 10:00 AM - 10:00 PM', 'Discord: miraigear, Twitter: @MiraiGear, FB: MiraiGearOfficial')";
        $pdo->exec($seedSql);
        echo "<p style='color: green;'>&#10004; Initial contact info seeded successfully.</p>";
    } else {
        echo "<p style='color: blue;'>&#x2139; Skipping seeding: Table already contains data.</p>";
    }

    echo "<h3>Migration Complete!</h3>";
    echo "<p><a href='admin_contact.php'>Click here to go to Admin Contact Management</a></p>";
    echo "<p style='color: #ff0055;'><strong>Security Note:</strong> Please delete this file (fix_db.php) after running it.</p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>&#10008; Error: " . $e->getMessage() . "</p>";
}
?>