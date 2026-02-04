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
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $quantity;
        } else {
            $_SESSION['cart'][$id] = $quantity;
        }
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