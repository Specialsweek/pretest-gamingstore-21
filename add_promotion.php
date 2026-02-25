<?php
require_once 'db.php';
require_once 'auth_check.php';

requireAdmin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $promo_code = trim($_POST['promo_code']);
    $discount_type = $_POST['discount_type'];
    $discount_value = (float) $_POST['discount_value'];
    $min_order_amount = (float) $_POST['min_order_amount'];
    $max_discount = !empty($_POST['max_discount']) ? (float) $_POST['max_discount'] : null;
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $usage_limit = !empty($_POST['usage_limit']) ? (int) $_POST['usage_limit'] : null;
    $status = $_POST['status'];

    if (empty($promo_code)) {
        $error = "Promo code is required.";
    } else {
        try {
            $sql = "INSERT INTO promotions (promo_code, discount_type, discount_value, min_order_amount, max_discount, start_date, end_date, usage_limit, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$promo_code, $discount_type, $discount_value, $min_order_amount, $max_discount, $start_date, $end_date, $usage_limit, $status])) {
                $message = "Advanced Promotion added successfully!";
            } else {
                $error = "Failed to add promotion.";
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Advanced Promotion - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <?php require_once 'navbar.php'; ?>
    <div class="container" style="padding-top: 2rem;">
        <div class="form-container" style="max-width: 800px; margin: 0 auto;">
            <h1>CREATE <span style="color: var(--neon-purple);">PROMO CODE</span></h1>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Configure advanced discount logic for your
                gear.</p>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Promo Code *</label>
                        <input type="text" name="promo_code" required placeholder="e.g., MIRAI2024">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Discount Type *</label>
                        <select name="discount_type" required>
                            <option value="percent">Percentage (%)</option>
                            <option value="fixed">Fixed Amount ($)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Discount Value *</label>
                        <input type="number" step="0.01" name="discount_value" required placeholder="e.g., 20">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Min. Order Amount ($)</label>
                        <input type="number" step="0.01" name="min_order_amount" value="0.00">
                    </div>
                    <div class="form-group">
                        <label>Max. Discount ($) (Percent only)</label>
                        <input type="number" step="0.01" name="max_discount" placeholder="Optional">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="datetime-local" name="start_date">
                    </div>
                    <div class="form-group">
                        <label>End Date (Expiry)</label>
                        <input type="datetime-local" name="end_date">
                    </div>
                </div>

                <div class="form-group">
                    <label>Usage Limit (Total times code can be used)</label>
                    <input type="number" name="usage_limit" placeholder="Unlimited if empty">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="simple-btn" style="background: var(--neon-purple);">Launch
                        Promotion</button>
                    <a href="promotions.php" class="simple-btn"
                        style="background: transparent; border: 1px solid var(--border-color);">Back</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>