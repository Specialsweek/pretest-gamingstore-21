<?php
require_once 'db.php';
require_once 'auth_check.php';

requireAdmin();

$message = '';
$error = '';

// Handle Submit/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $store_name = trim($_POST['store_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $hours = trim($_POST['hours']);
        $social_links = trim($_POST['social_links']);
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        // Validation
        if (empty($store_name) || empty($email) || empty($phone) || empty($address)) {
            $error = "Please fill in all required fields (Store Name, Email, Phone, Address).";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE contact_info SET store_name = ?, email = ?, phone = ?, address = ?, hours = ?, social_links = ? WHERE id = ?");
                $stmt->execute([$store_name, $email, $phone, $address, $hours, $social_links, $id]);
                $message = "Contact information updated successfully!";
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Fetch the contact record (assuming one for now, or edit by ID if specified)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 1;
$stmt = $pdo->prepare("SELECT * FROM contact_info WHERE id = ?");
$stmt->execute([$id]);
$contact = $stmt->fetch();

if (!$contact) {
    // Fallback if record 1 is missing
    $contact = $pdo->query("SELECT * FROM contact_info LIMIT 1")->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Contact Edit - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-layout {
            display: flex;
            gap: 2rem;
            min-height: calc(100vh - 100px);
        }

        .admin-sidebar {
            width: 250px;
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        .admin-main {
            flex: 1;
        }

        .sidebar-link {
            display: block;
            padding: 12px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            margin-bottom: 5px;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(0, 242, 255, 0.1);
            color: var(--neon-cyan);
        }

        .timestamp {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 1rem;
            display: block;
            text-align: right;
        }
    </style>
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container" style="padding-top: 2rem;">
        <div class="admin-layout">
            <aside class="admin-sidebar" style="display: none;">
                <!-- Hidden for now but structured for the 'extra' requirement -->
                <h3 style="font-size: 1rem; margin-bottom: 1.5rem;">ADMIN PANEL</h3>
                <a href="index.php" class="sidebar-link">Dashboard</a>
                <a href="admin_contact.php" class="sidebar-link active">Manage Contact</a>
            </aside>

            <main class="admin-main">
                <header style="margin-bottom: 2rem;">
                    <h1>Edit <span style="color: var(--neon-cyan);">Contact Info</span></h1>
                    <p style="color: var(--text-secondary);">Manage the official store information displayed to
                        customers.</p>
                </header>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?= $message ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($contact): ?>
                    <div class="form-container" style="margin: 0; max-width: 800px;">
                        <form method="POST" action="admin_contact.php" id="contactForm">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= $contact['id'] ?>">

                            <div class="form-group">
                                <label>Store Name *</label>
                                <input type="text" name="store_name" value="<?= htmlspecialchars($contact['store_name']) ?>"
                                    required>
                            </div>

                            <div class="admin-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <div class="form-group">
                                    <label>Support Email *</label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($contact['email']) ?>"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number *</label>
                                    <input type="text" name="phone" value="<?= htmlspecialchars($contact['phone']) ?>"
                                        required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Physical Address *</label>
                                <textarea name="address" rows="3"
                                    required><?= htmlspecialchars($contact['address']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Business Hours</label>
                                <input type="text" name="hours" value="<?= htmlspecialchars($contact['hours']) ?>"
                                    placeholder="e.g. Mon-Fri 9AM-10PM">
                            </div>

                            <div class="form-group">
                                <label>Social Media Links</label>
                                <textarea name="social_links" rows="2"
                                    placeholder="Discord: link, Twitter: @handle"><?= htmlspecialchars($contact['social_links']) ?></textarea>
                            </div>

                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem;">
                                <button type="submit" class="simple-btn"
                                    onclick="return confirm('Are you sure you want to save these changes?');">Update
                                    Contact</button>
                                <span class="timestamp">Last Updated:
                                    <?= date('M j, Y - H:i', strtotime($contact['last_updated'])) ?></span>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-error">No contact records found. Please run database setup.</div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>

</html>