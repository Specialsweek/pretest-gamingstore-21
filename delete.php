<?php
ob_start();
require_once 'db.php';
require_once 'products.php';
require_once 'auth_check.php';

requireAdmin();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $productObj = new Product($pdo);

    if ($productObj->deleteProduct($id)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Failed to delete product.";
    }
} else {
    header("Location: index.php");
    exit();
}
?>