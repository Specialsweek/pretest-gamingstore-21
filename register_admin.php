<?php
ob_start();
require_once 'db.php';
session_start();

// Optional: Require a secret code to prevent public admin registration
// For this test, we'll leave it open or require a simple key if you prefer.
// Let's keep it simple as requested.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $secret_code = $_POST['secret_code']; // Simple security

    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif ($secret_code !== 'admin_secret') { // Simple hardcoded secret
        $error = "Invalid Admin Secret Code";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken";
        } else {
            // Create ADMIN user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'admin'; // FORCE ADMIN ROLE

            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $hashed_password, $role])) {
                    header("Location: login.php?registered=1&role=admin");
                    exit();
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account - Gaming Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .register-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 2rem;
            background: #2a2a2a;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border: 1px solid #ff4d4d;
            /* Red border to indicate Admin stuff */
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #444;
            border-radius: 5px;
            background: #333;
            color: #fff;
        }

        .btn-full {
            width: 100%;
            background-color: #ff4d4d;
        }

        /* Red button for admin */
        .btn-full:hover {
            background-color: #ff3333;
        }

        .error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2 style="text-align: center; color: #fff; margin-bottom: 2rem;">Create Admin Account</h2>

        <?php if (isset($error)): ?>
            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required minlength="3" placeholder="Admin username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required minlength="6" placeholder="Min 6 characters">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="Confirm password">
            </div>
            <div class="form-group">
                <label style="color: #ff4d4d;">Admin Secret Code</label>
                <input type="password" name="secret_code" required placeholder="Enter 'admin_secret'">
            </div>
            <button type="submit" class="btn btn-full">Create Admin</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
            <a href="login.php" style="color: #888;">Back to Login</a>
        </p>
    </div>
</body>

</html>