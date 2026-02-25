<?php
require_once 'db.php';
require_once 'auth_check.php';

requireAdmin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $discount_code = trim($_POST['discount_code']);
    $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;

    if (empty($title) || empty($description)) {
        $error = "Title and Description are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO promotions (title, description, discount_code, expiry_date) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$title, $description, $discount_code, $expiry_date])) {
                $message = "Promotion added successfully!";
            } else {
                $error = "Failed to add promotion.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Promotion - Mirai Gear Admin</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container" style="padding-top: 2rem;">
        <div class="form-container" style="max-width: 600px; margin: 0 auto;">
            <h1>ADD NEW <span style="color: var(--neon-purple);">PROMOTION</span></h1>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Create a new deal for your customers.</p>

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

            <form method="POST" action="add_promotion.php">
                <div class="form-group">
                    <label>Promotion Title *</label>
                    <input type="text" name="title" required placeholder="e.g., Summer Sale 20% Off">
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" rows="4" required placeholder="Describe the offer..."></textarea>
                </div>

                <div class="form-group">
                    <label>Discount Code (Optional)</label>
                    <input type="text" name="discount_code" placeholder="e.g., MIRAI20">
                </div>

                <div class="form-group">
                    <label>Expiry Date (Optional)</label>
                    <input type="date" name="expiry_date">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="simple-btn"
                        style="background: var(--neon-purple); box-shadow: 0 0 15px rgba(188, 19, 254, 0.4);">Add
                        Promotion</button>
                    <a href="promotions.php" class="simple-btn"
                        style="background: transparent; border: 1px solid var(--border-color);">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>