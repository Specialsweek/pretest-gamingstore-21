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
            $newTotal = $currentInCart + $quantity;

            if ($newTotal <= $product['stock']) {
                $_SESSION['cart'][$id] = $newTotal;
            } else {
                // If AJAX, return error
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo json_encode(['status' => 'error', 'message' => 'Not enough stock!']);
                    exit();
                }
                // If normal request, maybe set a session flash message (optional)
            }
        }
    }

    // Check if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['status' => 'success', 'cartCount' => array_sum($_SESSION['cart'])]);
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