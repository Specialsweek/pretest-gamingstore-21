<?php
require_once 'db.php';
require_once 'auth_check.php';

requireAdmin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store_name = trim($_POST['store_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $hours = trim($_POST['hours']);
    $social_links = trim($_POST['social_links']);
    $id = (int) $_POST['id'];

    if (empty($store_name) || empty($email) || empty($phone) || empty($address)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE contact_info SET store_name = ?, email = ?, phone = ?, address = ?, hours = ?, social_links = ? WHERE id = ?");
            if ($stmt->execute([$store_name, $email, $phone, $address, $hours, $social_links, $id])) {
                $message = "Contact information updated successfully!";
            } else {
                $error = "Failed to update contact information.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch the contact record
$contact = $pdo->query("SELECT * FROM contact_info LIMIT 1")->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Contact Edit - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container" style="padding-top: 2rem;">
        <div class="form-container" style="max-width: 800px; margin: 0 auto;">
            <h1>Edit <span style="color: var(--neon-cyan);">Contact Info</span></h1>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Manage official store information.</p>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($contact): ?>
                <form method="POST" action="admin_contact.php">
                    <input type="hidden" name="id" value="<?= $contact['id'] ?>">

                    <div class="form-group">
                        <label>Store Name *</label>
                        <input type="text" name="store_name" value="<?= htmlspecialchars($contact['store_name']) ?>"
                            required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Support Email *</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($contact['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($contact['phone']) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Physical Address *</label>
                        <textarea name="address" rows="3" required><?= htmlspecialchars($contact['address']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Business Hours</label>
                        <input type="text" name="hours" value="<?= htmlspecialchars($contact['hours']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Social Media Links</label>
                        <textarea name="social_links" rows="2"><?= htmlspecialchars($contact['social_links']) ?></textarea>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem;">
                        <button type="submit" class="simple-btn"
                            onclick="return confirm('Update store information?');">Update Contact</button>
                        <span style="color: var(--text-secondary); font-size: 0.85rem;">
                            Last Updated: <?= date('M j, Y - H:i', strtotime($contact['last_updated'])) ?>
                        </span>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-error">No contact record found. Please run database setup.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>