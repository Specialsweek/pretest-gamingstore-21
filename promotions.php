<?php
require_once 'db.php';
require_once 'auth_check.php';

// Fetch active promotions
$now = date('Y-m-d H:i:s');
$stmt = $pdo->query("SELECT * FROM promotions 
    WHERE status = 'active' 
    AND (end_date >= '$now' OR end_date IS NULL)
    AND (usage_limit IS NULL OR used_count < usage_limit)
    ORDER BY created_at DESC");
$promotions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .promo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .promo-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .promo-card:hover {
            border-color: var(--neon-purple);
            box-shadow: 0 0 20px rgba(188, 19, 254, 0.2);
            transform: translateY(-5px);
        }

        .promo-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--neon-purple);
        }

        .promo-tag {
            background: rgba(188, 19, 254, 0.1);
            color: var(--neon-purple);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .promo-code-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px dashed var(--border-color);
            padding: 15px;
            text-align: center;
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-cyan);
            font-size: 1.4rem;
            margin: 1.5rem 0;
            cursor: pointer;
            transition: all 0.2s;
        }

        .promo-code-box:hover {
            background: rgba(0, 255, 157, 0.1);
            border-color: var(--neon-cyan);
        }

        .discount-info {
            font-weight: 800;
            color: white;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .expiry {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
    </style>
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container" style="padding-top: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
            <div>
                <h1 style="font-size: 2.5rem;">EXCLUSIVE <span style="color: var(--neon-purple);">GEAR DEALS</span></h1>
                <p style="color: var(--text-secondary);">Unlock special discounts with our limited-time promo codes.</p>
            </div>
            <?php if (isAdmin()): ?>
                <a href="add_promotion.php" class="simple-btn">Manage Promotions</a>
            <?php endif; ?>
        </div>

        <?php if (empty($promotions)): ?>
            <div class="alert alert-info" style="text-align: center; padding: 4rem;">
                <p>No active promotions at the moment. Keep an eye on our social channels!</p>
            </div>
        <?php else: ?>
            <div class="promo-grid">
                <?php foreach ($promotions as $promo): ?>
                    <div class="promo-card">
                        <span
                            class="promo-tag"><?= $promo['discount_type'] === 'percent' ? $promo['discount_value'] . '%' : '$' . $promo['discount_value'] ?>
                            OFF</span>
                        <div class="discount-info">
                            <?= $promo['discount_type'] === 'percent' ? $promo['discount_value'] . '% Discount' : '$' . $promo['discount_value'] . ' Discount' ?>
                        </div>
                        <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.9rem;">
                            Min. Spend: $<?= number_format($promo['min_order_amount'], 2) ?>
                        </p>

                        <div class="promo-code-box"
                            onclick="navigator.clipboard.writeText('<?= $promo['promo_code'] ?>'); alert('Code copied!')"
                            title="Click to copy">
                            <?= htmlspecialchars($promo['promo_code']) ?>
                        </div>

                        <div class="expiry">
                            <?php if ($promo['end_date']): ?>
                                Valid until: <?= date('M j, Y', strtotime($promo['end_date'])) ?>
                            <?php else: ?>
                                Valid for a limited time
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>