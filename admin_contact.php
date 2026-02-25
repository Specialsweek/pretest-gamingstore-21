<?php
require_once 'db.php';
require_once 'auth_check.php';

requireAdmin();

$message = '';
$error = '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add' || $action === 'edit') {
            $store_name = trim($_POST['store_name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $address = trim($_POST['address']);
            $hours = trim($_POST['hours']);
            $social_links = trim($_POST['social_links']);
            $id = isset($_POST['id']) ? (int) $_POST['id'] : null;

            // Simple validation
            if (empty($store_name) || empty($email) || empty($phone) || empty($address)) {
                $error = "Please fill in all required fields.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format.";
            } else {
                try {
                    if ($action === 'add') {
                        $stmt = $pdo->prepare("INSERT INTO contact_info (store_name, email, phone, address, hours, social_links) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$store_name, $email, $phone, $address, $hours, $social_links]);
                        $message = "Contact info added successfully!";
                    } else {
                        $stmt = $pdo->prepare("UPDATE contact_info SET store_name = ?, email = ?, phone = ?, address = ?, hours = ?, social_links = ? WHERE id = ?");
                        $stmt->execute([$store_name, $email, $phone, $address, $hours, $social_links, $id]);
                        $message = "Contact info updated successfully!";
                    }
                } catch (PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                }
            }
        } elseif ($action === 'delete' && isset($_POST['id'])) {
            try {
                $stmt = $pdo->prepare("DELETE FROM contact_info WHERE id = ?");
                $stmt->execute([(int) $_POST['id']]);
                $message = "Contact record deleted successfully!";
            } catch (PDOException $e) {
                $error = "Delete failed: " . $e->getMessage();
            }
        }
    }
}

// Fetch all contact records
$contacts = $pdo->query("SELECT * FROM contact_info ORDER BY updated_at DESC")->fetchAll();

// If editing, fetch specific record
$edit_contact = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM contact_info WHERE id = ?");
    $stmt->execute([(int) $_GET['edit_id']]);
    $edit_contact = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Contact Management - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: start;
        }

        @media (max-width: 992px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }

        .data-table-container {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .action-btns {
            display: flex;
            gap: 8px;
        }
    </style>
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container" style="padding-top: 2rem;">
        <h1 style="margin-bottom: 2rem;">Contact <span style="color: var(--neon-cyan);">Management</span> Panel</h1>

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

        <div class="admin-grid">
            <!-- Form Layer -->
            <div class="form-container" style="margin: 0; max-width: 100%;">
                <h3>
                    <?= $edit_contact ? 'Edit existing' : 'Add new' ?> Contact Info
                </h3>
                <form method="POST" action="admin_contact.php">
                    <input type="hidden" name="action" value="<?= $edit_contact ? 'edit' : 'add' ?>">
                    <?php if ($edit_contact): ?>
                        <input type="hidden" name="id" value="<?= $edit_contact['id'] ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Store Name *</label>
                        <input type="text" name="store_name" value="<?= $edit_contact['store_name'] ?? '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?= $edit_contact['email'] ?? '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="text" name="phone" value="<?= $edit_contact['phone'] ?? '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Street Address *</label>
                        <textarea name="address" rows="3" required><?= $edit_contact['address'] ?? '' ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Business Hours</label>
                        <input type="text" name="hours" value="<?= $edit_contact['hours'] ?? '' ?>"
                            placeholder="e.g. Mon-Fri 9AM-6PM">
                    </div>

                    <div class="form-group">
                        <label>Social Media Links</label>
                        <textarea name="social_links" rows="2"
                            placeholder="Discord, Twitter, etc."><?= $edit_contact['social_links'] ?? '' ?></textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="simple-btn">
                            <?= $edit_contact ? 'Update Record' : 'Save Contact' ?>
                        </button>
                        <?php if ($edit_contact): ?>
                            <a href="admin_contact.php" class="simple-btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- List Layer -->
            <div class="data-table-container">
                <h3>Location History</h3>
                <?php if (empty($contacts)): ?>
                    <p style="color: var(--text-secondary); padding: 1rem;">No contact records found.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Store</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $c): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700;">
                                            <?= htmlspecialchars($c['store_name']) ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                            <?= htmlspecialchars(substr($c['address'], 0, 30)) ?>...
                                        </div>
                                    </td>
                                    <td style="font-size: 0.85rem;">
                                        <?= htmlspecialchars($c['email']) ?><br>
                                        <?= htmlspecialchars($c['phone']) ?>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="?edit_id=<?= $c['id'] ?>" class="btn"
                                                style="padding: 5px 10px; font-size: 0.75rem;">Edit</a>
                                            <form method="POST" onsubmit="return confirm('Really delete this location?');"
                                                style="display:inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                <button type="submit" class="btn btn-danger"
                                                    style="padding: 5px 10px; font-size: 0.75rem;">Del</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>