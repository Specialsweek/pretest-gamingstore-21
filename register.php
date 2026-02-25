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
    <title>Register - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="form-container" style="max-width: 400px;">
            <h2 style="text-align: center; margin-bottom: 2rem;">Create Account</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
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
                <button type="submit" class="simple-btn" style="width: 100%;">Register</button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem; color: var(--text-secondary);">
                Already have an account? <a href="login.php" style="color: var(--neon-cyan); font-weight: 600;">Login
                    here</a>
            </p>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="index.php" class="back-link" style="margin-bottom: 0;">Back to Store</a>
            </div>
        </div>
    </div>
</body>

</html>