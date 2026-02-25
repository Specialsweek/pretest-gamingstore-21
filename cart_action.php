<?php
ob_start();
session_start();
require_once 'db.php';
require_once 'products.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    $id = $_POST['product_id'] ?? null;
    $quantity = (int) ($_POST['quantity'] ?? 1);

    if ($id && $quantity > 0) {
        $productObj = new Product($pdo);
        $product = $productObj->getProductById($id);

        if ($product) {
            $currentInCart = $_SESSION['cart'][$id] ?? 0;
            $newQuantity = $currentInCart + $quantity;

            if ($newQuantity <= $product['stock']) {
                $_SESSION['cart'][$id] = $newQuantity;
                $success = true;
            } else {
                $error = "Insufficient stock. Only " . $product['stock'] . " left.";
            }
        }
    }

    // Check if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        if (isset($error)) {
            echo json_encode(['status' => 'error', 'message' => $error]);
        } else {
            echo json_encode(['status' => 'success', 'cartCount' => array_sum($_SESSION['cart'])]);
        }
        exit();
    }

    header("Location: cart.php");
    exit();
}

if ($action === 'remove') {
    $id = $_GET['id'] ?? null;
    if ($id && isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit();
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit();
}

header("Location: index.php");
exit();
?>