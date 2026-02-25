<?php
require_once 'db.php';
require_once 'auth_check.php';

requireLogin();

$userId = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle Profit Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profit'])) {
    $newProfit = filter_input(INPUT_POST, 'profit', FILTER_VALIDATE_FLOAT);

    if ($newProfit !== false && $newProfit >= 0) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET profit = ? WHERE id = ?");
            $stmt->execute([$newProfit, $userId]);
            $message = "Profit updated successfully!";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Please enter a valid positive numeric value for Profit.";
    }
}

// Fetch User Data
try {
    $stmt = $pdo->prepare("SELECT username, email, profit FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        die("User not found!");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once 'navbar.php'; ?>
    <div class="container">
        <a href="index.php" class="back-link">&larr; Back to Store</a>

        <div class="card-container" style="max-width: 500px; text-align: center;">
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <div
                style="width: 100px; height: 100px; background: rgba(255, 255, 255, 0.05); border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; border: 2px solid var(--neon-cyan); color: var(--neon-cyan);">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>

            <div style="margin-bottom: 2rem;">
                <div
                    style="margin-bottom: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.05);">
                    <span
                        style="display: block; font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.3rem;">Username</span>
                    <span class="value" style="font-size: 1.2rem; font-weight: 600; color: var(--text-primary);">
                        <?= htmlspecialchars($user['username']) ?>
                    </span>
                </div>
                <div
                    style="margin-bottom: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.05);">
                    <span
                        style="display: block; font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.3rem;">Email
                        Address</span>
                    <span class="value" style="font-size: 1.2rem; font-weight: 600; color: var(--text-primary);">
                        <?= htmlspecialchars($user['email'] ?? 'Not set') ?>
                    </span>
                </div>
                <div
                    style="margin-bottom: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.05);">
                    <span
                        style="display: block; font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.3rem;">Current
                        Profit</span>
                    <span class="value"
                        style="font-size: 1.5rem; font-weight: 700; color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 242, 255, 0.3);">$
                        <?= number_format($user['profit'], 2) ?>
                    </span>
                </div>
            </div>

            <button type="button" class="simple-btn" id="edit-btn">Edit Profit</button>

            <form method="POST" id="edit-form" style="display: none; margin-top: 1.5rem;">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="profit">New Profit Value</label>
                    <input type="number" step="0.01" name="profit" id="profit" value="<?= $user['profit'] ?>" required>
                </div>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button type="submit" name="update_profit" class="simple-btn">Save Changes</button>
                    <button type="button" class="simple-btn" id="cancel-btn"
                        style="background: rgba(255,255,255,0.05); color: var(--text-primary);">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const editBtn = document.getElementById('edit-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const editForm = document.getElementById('edit-form');

        editBtn.addEventListener('click', () => {
            editForm.style.display = 'block';
            editBtn.style.display = 'none';
        });

        cancelBtn.addEventListener('click', () => {
            editForm.style.display = 'none';
            editBtn.style.display = 'inline-block';
        });
    </script>
</body>

</html>