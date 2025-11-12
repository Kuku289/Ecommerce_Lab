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

$customer_id = $_SESSION['user_id'];

// Empty cart
if (empty_cart_ctr($customer_id)) {
    $response['success'] = true;
    $response['message'] = 'Cart emptied successfully';
    $response['cart_count'] = 0;
} else {
    $response['message'] = 'Failed to empty cart';
}

echo json_encode($response);
?>