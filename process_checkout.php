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

        // 1. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, address, promo_code, discount_amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $finalTotal, $address, $promoCode ?: null, $discountAmount]);
        $orderId = $pdo->lastInsertId();

        // 2. Create Order Items
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $itemStmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        }

        // 3. Increment Promo Usage
        if ($promoId) {
            $promoObj->incrementUsage($promoId);
        }

        $pdo->commit();

        // 3. Clear Cart
        unset($_SESSION['cart']);

        // 4. Redirect to Orders page
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