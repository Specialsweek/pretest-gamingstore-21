<?php
require_once 'db.php';
session_start();

// Fetch contact info
try {
    $stmt = $pdo->query("SELECT * FROM contact_info ORDER BY updated_at DESC LIMIT 1");
    $contact = $stmt->fetch();
} catch (PDOException $e) {
    $contact = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .contact-item {
            background: rgba(255, 255, 255, 0.02);
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            border-color: var(--neon-cyan);
            box-shadow: 0 0 20px rgba(0, 242, 255, 0.1);
            transform: translateY(-5px);
        }

        .contact-icon {
            font-size: 2rem;
            color: var(--neon-cyan);
            margin-bottom: 1rem;
            display: block;
        }

        .contact-label {
            display: block;
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .contact-value {
            font-size: 1.1rem;
            color: var(--text-primary);
            font-weight: 500;
            white-space: pre-line;
        }
    </style>
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container" style="padding-top: 2rem;">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h1 style="font-size: 3rem; margin-bottom: 1rem;">GET IN <span style="color: var(--neon-cyan);">TOUCH</span>
            </h1>
            <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto;">Have questions about our
                hardware? Our team of gaming experts is here to help you build your ultimate battle station.</p>
        </div>

        <?php if ($contact): ?>
            <div class="contact-grid">
                <div class="contact-item">
                    <span class="contact-icon">🏢</span>
                    <span class="contact-label">Store Name</span>
                    <div class="contact-value">
                        <?= htmlspecialchars($contact['store_name']) ?>
                    </div>
                </div>

                <div class="contact-item">
                    <span class="contact-icon">📧</span>
                    <span class="contact-label">Email Support</span>
                    <div class="contact-value"><a href="mailto:<?= htmlspecialchars($contact['email']) ?>"
                            style="color: var(--neon-cyan); text-decoration: none;">
                            <?= htmlspecialchars($contact['email']) ?>
                        </a></div>
                </div>

                <div class="contact-item">
                    <span class="contact-icon">📞</span>
                    <span class="contact-label">Phone Hotline</span>
                    <div class="contact-value">
                        <?= htmlspecialchars($contact['phone']) ?>
                    </div>
                </div>

                <div class="contact-item">
                    <span class="contact-icon">📍</span>
                    <span class="contact-label">Headquarters</span>
                    <div class="contact-value">
                        <?= htmlspecialchars($contact['address']) ?>
                    </div>
                </div>

                <div class="contact-item">
                    <span class="contact-icon">⏰</span>
                    <span class="contact-label">Business Hours</span>
                    <div class="contact-value">
                        <?= htmlspecialchars($contact['hours']) ?>
                    </div>
                </div>

                <div class="contact-item">
                    <span class="contact-icon">🌐</span>
                    <span class="contact-label">Social Media</span>
                    <div class="contact-value">
                        <?= htmlspecialchars($contact['social_links']) ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-error" style="text-align: center;">
                Contact information is currently unavailable. Please check back later.
            </div>
        <?php endif; ?>
    </div>
</body>

</html>