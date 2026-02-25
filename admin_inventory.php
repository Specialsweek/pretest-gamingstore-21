<?php
require_once 'db.php';
require_once 'products.php';
require_once 'auth_check.php';

requireAdmin();

$productObj = new Product($pdo);

// Handle AJAX stock updates
if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'update_stock') {
    $id = (int) $_POST['id'];
    $change = (int) $_POST['change'];

    $stmt = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock + ?) WHERE id = ?");
    $stmt->execute([$change, $id]);

    // Fetch new stock
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['status' => 'success', 'new_stock' => $stmt->fetchColumn()]);
    exit;
}

$allProducts = $productObj->getAllProducts();
$lowStockItems = array_filter($allProducts, function ($p) {
    return $p['stock'] <= $p['low_stock_threshold'];
});
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Mirai Gear Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }

        .stat-val {
            font-size: 2rem;
            font-weight: bold;
            color: var(--neon-cyan);
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .inventory-table th,
        .inventory-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .inventory-table th {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .stock-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .control-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .control-btn:hover {
            background: var(--neon-purple);
            border-color: var(--neon-purple);
        }

        .stock-display {
            min-width: 40px;
            text-align: center;
            font-weight: bold;
        }

        .alert-tag {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .tag-low {
            background: rgba(255, 184, 0, 0.1);
            color: #FFB800;
            border: 1px solid #FFB800;
        }

        .tag-out {
            background: rgba(255, 0, 85, 0.1);
            color: #FF0055;
            border: 1px solid #FF0055;
        }
    </style>
</head>

<body>
    <?php require_once 'navbar.php'; ?>
    <div class="container" style="padding-top: 2rem;">
        <h1>INVENTORY <span style="color: var(--neon-cyan);">MANAGEMENT</span></h1>
        <p style="color: var(--text-secondary); margin-bottom: 2rem;">Monitor and control your gear stock levels.</p>

        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-val">
                    <?= count($allProducts) ?>
                </div>
                <div class="stat-label">Total SKUs</div>
            </div>
            <div class="stat-card" style="border-color: #FFB800;">
                <div class="stat-val" style="color: #FFB800;">
                    <?= count($lowStockItems) ?>
                </div>
                <div class="stat-label">Low Stock Alerts</div>
            </div>
            <div class="stat-card" style="border-color: #FF0055;">
                <div class="stat-val" style="color: #FF0055;">
                    <?= count(array_filter($allProducts, fn($p) => $p['stock'] == 0)) ?>
                </div>
                <div class="stat-label">Out of Stock</div>
            </div>
        </div>

        <div class="form-container" style="max-width: 100%; padding: 0;">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Stock Control</th>
                        <th>Low Limit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allProducts as $p): ?>
                        <tr id="row-<?= $p['id'] ?>">
                            <td>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <img src="<?= htmlspecialchars($p['image']) ?>"
                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    <div>
                                        <div style="font-weight: bold;">
                                            <?= htmlspecialchars($p['name']) ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary);">$
                                            <?= number_format($p['price'], 2) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?= htmlspecialchars($p['category']) ?>
                            </td>
                            <td>
                                <?php if ($p['stock'] == 0): ?>
                                    <span class="alert-tag tag-out">OUT OF STOCK</span>
                                <?php elseif ($p['stock'] <= $p['low_stock_threshold']): ?>
                                    <span class="alert-tag tag-low">LOW STOCK</span>
                                <?php else: ?>
                                    <span style="color: #00FF9D; font-size: 0.75rem; font-weight: bold;">HEALTHY</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="stock-control">
                                    <button class="control-btn" onclick="updateStock(<?= $p['id'] ?>, -1)">-</button>
                                    <span class="stock-display" id="stock-<?= $p['id'] ?>">
                                        <?= $p['stock'] ?>
                                    </span>
                                    <button class="control-btn" onclick="updateStock(<?= $p['id'] ?>, 1)">+</button>
                                </div>
                            </td>
                            <td>
                                <?= $p['low_stock_threshold'] ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $p['id'] ?>"
                                    style="color: var(--neon-cyan); text-decoration: none; font-size: 0.85rem;">Edit
                                    Full</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        async function updateStock(id, change) {
            const display = document.getElementById(`stock-${id}`);
            const row = document.getElementById(`row-${id}`);

            try {
                const response = await fetch('admin_inventory.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `ajax_action=update_stock&id=${id}&change=${change}`
                });

                const data = await response.json();
                if (data.status === 'success') {
                    display.textContent = data.new_stock;

                    // Optional: Visually indicate update
                    display.style.color = change > 0 ? '#00FF9D' : '#FF0055';
                    setTimeout(() => display.style.color = '', 300);

                    // Reload if we want status tags to update immediately, 
                    // or we could update the tag via JS here too.
                }
            } catch (e) {
                console.error("Stock update failed", e);
            }
        }
    </script>
</body>

</html>