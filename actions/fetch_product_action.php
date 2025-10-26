<?php
session_start();
require_once(dirname(__FILE__) . '/../controllers/product_controller.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get all products
$products = get_all_products_ctr();

if ($products) {
    echo json_encode(['success' => true, 'data' => $products]);
} else {
    echo json_encode(['success' => true, 'data' => [], 'message' => 'No products found']);
}
?>