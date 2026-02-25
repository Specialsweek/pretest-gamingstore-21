<?php
require_once 'db.php';
require_once 'auth_check.php';

requireAdmin();

if (isset($_GET['id'])) {
    $userIdToDelete = (int) $_GET['id'];
    $currentAdminId = $_SESSION['user_id'];

    // 1. Prevent self-deletion
    if ($userIdToDelete === $currentAdminId) {
        header("Location: admin_users.php?error=cannot_delete_self");
        exit();
    }

    try {
        // 2. Fetch user to check role (requirements say "except admin accounts")
        // Note: For extra safety, we ensure we don't accidentally delete another admin 
        // if that's what the requirement implies.
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userIdToDelete]);
        $user = $stmt->fetch();

        if (!$user) {
            header("Location: admin_users.php?error=user_not_found");
            exit();
        }

        if ($user['role'] === 'admin') {
            header("Location: admin_users.php?error=cannot_delete_admin");
            exit();
        }

        // 3. Delete user (foreign keys should handle orders if using CASCADE, 
        // otherwise we might need to handle dependencies. Let's assume standard behavior for now).
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($deleteStmt->execute([$userIdToDelete])) {
            header("Location: admin_users.php?message=user_deleted");
            exit();
        } else {
            header("Location: admin_users.php?error=delete_failed");
            exit();
        }

    } catch (PDOException $e) {
        header("Location: admin_users.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: admin_users.php");
    exit();
}
