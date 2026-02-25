<?php
require_once 'db.php';
require_once 'auth_check.php';

requireAdmin();

// Handle Search and Sort
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// Base Query with Aggregation
$query = "
    SELECT 
        u.id, u.username, u.email, u.role, u.created_at,
        COALESCE(SUM(o.total_price), 0) as total_spent,
        COALESCE(SUM(oi.quantity), 0) as total_items
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE u.username LIKE :search OR u.email LIKE :search
    GROUP BY u.id
";

// Sorting logic
if ($sort === 'highest_spending') {
    $query .= " ORDER BY total_spent DESC";
} else {
    $query .= " ORDER BY u.created_at DESC";
}

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute(['search' => "%$search%"]);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Query error: " . $e->getMessage());
}

$message = '';
$error = '';
if (isset($_GET['message'])) {
    if ($_GET['message'] === 'user_deleted')
        $message = "User has been removed from the mainframe.";
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'cannot_delete_self')
        $error = "System Error: You cannot terminate your own account.";
    elseif ($_GET['error'] === 'cannot_delete_admin')
        $error = "Access Denied: Administrative accounts cannot be deleted.";
    else
        $error = "Error: " . htmlspecialchars($_GET['error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: User Management - Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-box {
            display: flex;
            gap: 10px;
            background: rgba(255, 255, 255, 0.05);
            padding: 5px 15px;
            border-radius: 30px;
            border: 1px solid var(--border-color);
            align-items: center;
            width: 100%;
            max-width: 400px;
        }

        .search-box input {
            background: transparent;
            border: none;
            color: white;
            padding: 8px;
            outline: none;
            flex-grow: 1;
        }

        .stats-badge {
            background: rgba(0, 242, 255, 0.1);
            color: var(--neon-cyan);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .role-tag {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            font-weight: 800;
        }

        .role-admin {
            background: rgba(188, 19, 254, 0.2);
            color: var(--neon-purple);
            border: 1px solid var(--neon-purple);
        }

        .role-user {
            background: rgba(0, 242, 255, 0.1);
            color: var(--neon-cyan);
            border: 1px solid var(--neon-cyan);
        }

        .user-table-container {
            overflow-x: auto;
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        table {
            border-spacing: 0;
            margin-bottom: 0;
        }

        th {
            background: rgba(255, 255, 255, 0.02);
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.04);
        }

        .action-btns {
            display: flex;
            gap: 10px;
        }

        .view-orders-btn {
            color: var(--neon-cyan);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .delete-user-btn {
            color: var(--accent);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            background: none;
            border: none;
        }
    </style>
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container" style="padding-top: 2rem;">
        <div class="admin-header">
            <div>
                <h1 style="margin: 0;">USER <span style="color: var(--neon-cyan);">MANAGEMENT</span></h1>
                <p style="color: var(--text-secondary);">Monitor activity and manage gear enthusiasts.</p>
            </div>

            <form action="admin_users.php" method="GET" class="search-box">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--text-secondary)"
                    stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="search" placeholder="Search by name or email..."
                    value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
            </form>
        </div>

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

        <div style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;">
            <span style="color: var(--text-secondary); font-size: 0.9rem;">Sort By:</span>
            <a href="admin_users.php?sort=newest&search=<?= urlencode($search) ?>" class="view-orders-btn"
                style="<?= $sort === 'newest' ? 'color: var(--neon-purple);' : '' ?>">Newest</a>
            <span style="color: var(--border-color);">|</span>
            <a href="admin_users.php?sort=highest_spending&search=<?= urlencode($search) ?>" class="view-orders-btn"
                style="<?= $sort === 'highest_spending' ? 'color: var(--neon-purple);' : '' ?>">Highest Spending</a>
        </div>

        <div class="user-table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Spending Info</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 4rem; color: var(--text-secondary);">No
                                users found in the system.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td style="color: var(--text-secondary); font-family: 'Orbitron', sans-serif;">#
                                    <?= $u['id'] ?>
                                </td>
                                <td>
                                    <div style="font-weight: 700;">
                                        <?= htmlspecialchars($u['username']) ?>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                        <?= htmlspecialchars($u['email']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin-bottom: 4px;">Spent: <span
                                            style="color: var(--neon-cyan); font-weight: 700;">$
                                            <?= number_format($u['total_spent'], 2) ?>
                                        </span></div>
                                    <div style="font-size: 0.8rem; color: var(--text-secondary);">Items:
                                        <?= $u['total_items'] ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-tag role-<?= $u['role'] ?>">
                                        <?= $u['role'] ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-secondary);">
                                    <?= date('M j, Y', strtotime($u['created_at'])) ?>
                                </td>
                                <td class="action-btns">
                                    <a href="orders.php?user_id=<?= $u['id'] ?>" class="view-orders-btn">Orders</a>

                                    <?php if ($u['role'] !== 'admin' && $u['id'] !== $_SESSION['user_id']): ?>
                                        <a href="delete_user.php?id=<?= $u['id'] ?>" class="delete-user-btn"
                                            onclick="return confirm('WARNING: Are you sure you want to PERMANENTLY delete user \'<?= htmlspecialchars($u['username']) ?>\'? This action cannot be undone.');">
                                            Delete
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>