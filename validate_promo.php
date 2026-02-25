<?php
session_start();
require_once 'db.php';
require_once 'Promotion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $total = (float) ($_POST['total'] ?? 0);

    if (empty($code)) {
        echo json_encode(['valid' => false, 'message' => 'Please enter a code.']);
        exit;
    }

    $promoObj = new Promotion($pdo);
    $result = $promoObj->validateCode($code, $total);

    echo json_encode($result);
    exit;
}

echo json_encode(['valid' => false, 'message' => 'Invalid request.']);
