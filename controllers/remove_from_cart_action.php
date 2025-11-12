<?php
session_start();
header('Content-Type: application/json');

require_once '../controllers/cart_controller.php';

$response = ['success' => false, 'message' => ''];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please login first';
    echo json_encode($response);
    exit();
}

// Check if product_id is set
if (!isset($_POST['product_id'])) {
    $response['message'] = 'Missing product ID';
    echo json_encode($response);
    exit();
}

$product_id = intval($_POST['product_id']);
$customer_id = $_SESSION['user_id'];

// Remove from cart
if (remove_from_cart_ctr($product_id, $customer_id)) {
    $response['success'] = true;
    $response['message'] = 'Product removed from cart';
    $response['cart_count'] = get_cart_count_ctr($customer_id);
} else {
    $response['message'] = 'Failed to remove product from cart';
}

echo json_encode($response);
?>