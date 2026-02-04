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
    echo "Table 'products' recreated successfully with 'category' column.<br>";

    // Create users table
    $pdo->exec("DROP TABLE IF EXISTS users");
    $userSql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($userSql);
    echo "Table 'users' created successfully.<br>";

    // Seed users
    // Admin: admin / admin123
    // User: user / user123
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $userPass = password_hash('user123', PASSWORD_DEFAULT);

    $seedSql = "INSERT INTO users (username, password, role) VALUES 
                ('admin', '$adminPass', 'admin'),
                ('user', '$userPass', 'user')";
    $pdo->exec($seedSql);
    echo "Default users created (admin/admin123, user/user123).<br>";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>