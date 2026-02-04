<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Gaming Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .setup-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 2rem;
            background: #2a2a2a;
            border-radius: 10px;
            color: #fff;
            text-align: center;
        }

        .success {
            color: #4CAF50;
            margin-bottom: 0.5rem;
        }

        .error {
            color: #ff6b6b;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="setup-container">
        <h1>Database Setup</h1>
        <div style="text-align: left; margin-top: 2rem;">
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
                echo "<p class='success'>&#10004; Table 'products' recreated successfully.</p>";

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
                echo "<p class='success'>&#10004; Table 'users' created successfully.</p>";

                // Seed users
                // Admin: admin / admin123
                // User: user / user123
                $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
                $userPass = password_hash('user123', PASSWORD_DEFAULT);

                $seedSql = "INSERT INTO users (username, password, role) VALUES
                ('admin', '$adminPass', 'admin'),
                ('user', '$userPass', 'user')";
                $pdo->exec($seedSql);
                echo "<p class='success'>&#10004; Default users seeded (admin, user).</p>";
            } catch (PDOException $e) {
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