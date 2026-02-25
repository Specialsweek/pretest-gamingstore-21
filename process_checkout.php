<?php
ob_start();
session_start();
require_once 'auth_check.php';
require_once 'db.php';
require_once 'products.php';

requireLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        header("Location: index.php");
        exit();
    }

    $address = trim($_POST['address']);
    $promoCode = trim($_POST['promo_code'] ?? '');
    $userId = $_SESSION['user_id'];
    $productObj = new Product($pdo);
    $totalPrice = 0;

    // Calculate subtotal
    $cartItems = [];
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $product = $productObj->getProductById($id);
        if ($product) {
            $totalPrice += $product['price'] * $quantity;
            $cartItems[] = [
                'id' => $id,
                'price' => $product['price'],
                'quantity' => $quantity
            ];
        }
    }

    // Handle Promotion
    $discountAmount = 0;
    $promoId = null;
    if (!empty($promoCode)) {
        require_once 'Promotion.php';
        $promoObj = new Promotion($pdo);
        $promoRes = $promoObj->validateCode($promoCode, $totalPrice);
        if ($promoRes['valid']) {
            $discountAmount = $promoRes['discount'];
            $promoId = $promoRes['promo_id'];
        }
    }

    $finalTotal = max(0, $totalPrice - $discountAmount);

    try {
        $pdo->beginTransaction();

        // 1. Lock rows and check stock for all items
        foreach ($cartItems as &$item) {
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
            $stmt->execute([$item['id']]);
            $currentStock = $stmt->fetchColumn();

            if ($currentStock < $item['quantity']) {
                throw new Exception("Insufficient stock for one of the items. Please check your cart.");
            }
            $item['remaining_stock'] = $currentStock - $item['quantity'];
        }

        // 2. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, address, promo_code, discount_amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $finalTotal, $address, $promoCode ?: null, $discountAmount]);
        $orderId = $pdo->lastInsertId();

        // 3. Create Order Items & Deduct Stock
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stockStmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");

        foreach ($cartItems as $item) {
            $itemStmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
            $stockStmt->execute([$item['remaining_stock'], $item['id']]);
        }

        // 4. Increment Promo Usage
        if ($promoId) {
            $promoObj->incrementUsage($promoId);
        }

        $pdo->commit();

        // 5. Clear Cart
        unset($_SESSION['cart']);

        // 6. Redirect to Orders page
        header("Location: orders.php?success=1");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Transaction failed: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}
?>