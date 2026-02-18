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
    <title>Login - Gaming Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
            color: #333;
        }

        .form-group input:focus {
            border-color: #ff4d4d;
            box-shadow: 0 0 5px rgba(255, 77, 77, 0.2);
            outline: none;
        }

        .btn-full {
            width: 100%;
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-full:hover {
            background-color: #d32f2f;
        }

        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 1rem;
        }

        h2 {
            color: #d32f2f !important;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2 style="text-align: center; color: #fff; margin-bottom: 2rem;">Login</h2>

        <?php if (isset($_GET['registered'])): ?>
            <p style="color: #4CAF50; text-align: center; margin-bottom: 1rem;">Registration successful! Please login.</p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>
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
            <button type="submit" class="btn btn-full">Sign In</button>
        </form>
        <p style="text-align: center; margin-top: 1rem; color: #bbb;">
            New here? <a href="register.php" style="color: #4CAF50;">Create an Account</a>
        </p>
        <p style="text-align: center; margin-top: 0.5rem;">
            <a href="index.php" style="color: #888; font-size: 0.9em;">Back to Store</a>
        </p>
    </div>
</body>

</html>