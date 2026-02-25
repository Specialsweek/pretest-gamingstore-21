<?php
ob_start();
require_once 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once 'navbar.php'; ?>
    <div class="container">
        <div class="form-container" style="max-width: 400px;">
            <h2 style="text-align: center; margin-bottom: 2rem;">Login</h2>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success" style="text-align: center;">Registration successful! Please login.</div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="simple-btn" style="width: 100%;">Sign In</button>
            </form>
            <p style="text-align: center; margin-top: 1.5rem; color: var(--text-secondary);">
                New here? <a href="register.php" style="color: var(--neon-cyan); font-weight: 600;">Create an
                    Account</a>
            </p>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="index.php" class="back-link" style="margin-bottom: 0;">Back to Store</a>
            </div>
        </div>
    </div>
</body>

</html>