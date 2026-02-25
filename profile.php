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
    <style>
        .profile-container {
            max-width: 500px;
            margin: 100px auto;
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border-color);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--neon-cyan), var(--neon-purple));
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--neon-cyan);
            color: var(--neon-cyan);
            font-size: 3rem;
        }

        .profile-info {
            margin-bottom: 2rem;
        }

        .profile-item {
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .label {
            display: block;
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.3rem;
        }

        .value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .profit-value {
            color: var(--neon-cyan);
            font-size: 1.5rem;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(0, 242, 255, 0.3);
        }

        .edit-form {
            display: none;
            margin-top: 1.5rem;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.1);
            color: #00ff88;
            border: 1px solid rgba(0, 255, 136, 0.2);
        }

        .alert-error {
            background: rgba(255, 0, 85, 0.1);
            color: #ff0055;
            border: 1px solid rgba(255, 0, 85, 0.2);
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="index.php" class="back-link">&larr; Back to Store</a>

        <div class="profile-container">
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

            <div class="profile-avatar">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>

            <div class="profile-info">
                <div class="profile-item">
                    <span class="label">Username</span>
                    <span class="value">
                        <?= htmlspecialchars($user['username']) ?>
                    </span>
                </div>
                <div class="profile-item">
                    <span class="label">Email Address</span>
                    <span class="value">
                        <?= htmlspecialchars($user['email'] ?? 'Not set') ?>
                    </span>
                </div>
                <div class="profile-item">
                    <span class="label">Current Profit</span>
                    <span class="value profit-value">$
                        <?= number_format($user['profit'], 2) ?>
                    </span>
                </div>
            </div>

            <button type="button" class="simple-btn" id="edit-btn">Edit Profit</button>

            <form method="POST" class="edit-form" id="edit-form">
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