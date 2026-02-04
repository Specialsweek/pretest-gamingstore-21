<?php
ob_start();
require_once 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken";
        } else {
            // Create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; // Default role

            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $hashed_password, $role])) {
                    header("Location: login.php?registered=1");
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
    <title>Register - Gaming Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .register-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 2rem;
            background: #2a2a2a;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
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
        <h2 style="text-align: center; color: #fff; margin-bottom: 2rem;">Create Account</h2>

        <?php if (isset($error)): ?>
            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required minlength="3" placeholder="Choose a username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required minlength="6" placeholder="Min 6 characters">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="Confirm password">
            </div>
            <button type="submit" class="btn btn-full">Register</button>
        </form>
        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #444; text-align: center;">
            <p style="color: #bbb; margin-bottom: 1rem;">Already have an account?</p>
            <a href="login.php" class="btn"
                style="background-color: #555; display: inline-block; width: 100%; box-sizing: border-box; text-decoration: none;">Login</a>
        </div>
        <p style="text-align: center; margin-top: 0.5rem;">
            <a href="index.php" style="color: #888; font-size: 0.9em;">Back to Store</a>
        </p>
    </div>
</body>

</html>