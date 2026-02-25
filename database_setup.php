<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="setup-container">
        <h1>Database Setup</h1>
        <div style="text-align: left; margin-top: 2rem;">
            <?php
            require_once 'db.php';

            try {
                // Disable foreign key checks to allow dropping tables with dependencies
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

                // Drop old table to reset schema
                $pdo->exec("DROP TABLE IF EXISTS products");

                $sql = "CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        image LONGTEXT,
        category VARCHAR(50),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

                $pdo->exec($sql);
                echo "<p class='success'>&#10004; Table 'products' recreated successfully.</p>";

                // Create users table
                $pdo->exec("DROP TABLE IF EXISTS users");
                $userSql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) DEFAULT NULL,
        profit DECIMAL(10, 2) DEFAULT 0.00,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
                $pdo->exec($userSql);
                echo "<p class='success'>&#10004; Table 'users' created successfully with email and profit columns.</p>";

                // Seed users
                $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
                $userPass = password_hash('user123', PASSWORD_DEFAULT);

                $seedSql = "INSERT INTO users (username, password, email, profit, role) VALUES
                ('admin', '$adminPass', 'admin@miraigear.com', 5000.00, 'admin'),
                ('user', '$userPass', 'user@example.com', 120.50, 'user')";
                $pdo->exec($seedSql);
                echo "<p class='success'>&#10004; Default users seeded with email and profit values.</p>";

                // Create orders table
                $pdo->exec("DROP TABLE IF EXISTS order_items");
                $pdo->exec("DROP TABLE IF EXISTS orders");

                $ordersSql = "CREATE TABLE orders (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    total_price DECIMAL(10, 2) NOT NULL,
                    address TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                )";
                $pdo->exec($ordersSql);
                echo "<p class='success'>&#10004; Table 'orders' created successfully.</p>";

                // Create order_items table
                $itemsSql = "CREATE TABLE order_items (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    order_id INT NOT NULL,
                    product_id INT NOT NULL,
                    quantity INT NOT NULL,
                    price DECIMAL(10, 2) NOT NULL,
                    FOREIGN KEY (order_id) REFERENCES orders(id),
                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
                )";
                $pdo->exec($itemsSql);
                echo "<p class='success'>&#10004; Table 'order_items' created successfully.</p>";

                // Create promotions table
                $pdo->exec("DROP TABLE IF EXISTS promotions");
                $promoSql = "CREATE TABLE promotions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    discount_code VARCHAR(100),
                    expiry_date DATE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                $pdo->exec($promoSql);
                echo "<p class='success'>&#10004; Table 'promotions' created successfully.</p>";

                // Create contact_info table
                $pdo->exec("DROP TABLE IF EXISTS contact_info");
                $contactSql = "CREATE TABLE contact_info (
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
                echo "<p class='success'>&#10004; Table 'contact_info' created successfully.</p>";

                // Seed contact info
                $seedContactSql = "INSERT INTO contact_info (store_name, email, phone, address, hours, social_links) VALUES 
                ('Mirai Gear HQ', 'support@miraigear.com', '+1 (555) 123-4567', '777 Neon Street, Cyber City, Futureland', 'Mon - Sat: 10:00 AM - 10:00 PM', 'Discord: miraigear, Twitter: @MiraiGear, FB: MiraiGearOfficial')";
                $pdo->exec($seedContactSql);
                echo "<p class='success'>&#10004; Initial contact info seeded successfully.</p>";

                // Re-enable foreign key checks
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            } catch (PDOException $e) {
                // Ensure foreign key checks are re-enabled even on failure
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
                echo "<p class='error'>&#10008; Error: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
        <div style="margin-top: 2rem;">
            <a href="index.php" class="btn">Go to Store</a>
        </div>
    </div>
</body>

</html>