<?php
session_start();
header('Content-Type: application/json');

require_once '../controllers/cart_controller.php';

$response = ['success' => false, 'message' => ''];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please login to add items to cart';
    echo json_encode($response);
    exit();
}

// Check if required parameters are set
if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    $response['message'] = 'Missing required parameters';
    echo json_encode($response);
    exit();
}

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);
$customer_id = $_SESSION['user_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];

// Validate quantity
if ($quantity <= 0) {
    $response['message'] = 'Invalid quantity';
    echo json_encode($response);
    exit();
}

// Add to cart
if (add_to_cart_ctr($product_id, $customer_id, $quantity, $ip_address)) {
    $response['success'] = true;
    $response['message'] = 'Product added to cart successfully';
    $response['cart_count'] = get_cart_count_ctr($customer_id);
} else {
    $response['message'] = 'Failed to add product to cart';
}

echo json_encode($response);
?>