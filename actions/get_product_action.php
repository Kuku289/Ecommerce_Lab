<?php
session_start();
require_once(dirname(__FILE__) . '/../controllers/product_controller.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Validate product ID
if (empty($_GET['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

$product_id = intval($_GET['product_id']);

// Get product
$product = get_product_by_id_ctr($product_id);

if ($product) {
    echo json_encode(['success' => true, 'data' => $product]);
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
}
?>