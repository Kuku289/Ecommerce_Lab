<?php
session_start();
header('Content-Type: application/json');

require_once '../controllers/cart_controller.php';
require_once '../controllers/order_controller.php';

$response = ['success' => false, 'message' => '', 'order_id' => null, 'invoice_no' => ''];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please login first';
    echo json_encode($response);
    exit();
}

$customer_id = $_SESSION['user_id'];

// Get cart items
$cart_items = get_cart_items_ctr($customer_id);

if (empty($cart_items)) {
    $response['message'] = 'Your cart is empty';
    echo json_encode($response);
    exit();
}

// Calculate total
$total_amount = get_cart_total_ctr($customer_id);

// Generate unique invoice number
$invoice_no = 'INV-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));

// Start transaction
$db = new db_connection();
$conn = $db->db_conn();
$conn->begin_transaction();

try {
    // Create order
    $order_id = create_order_ctr($customer_id, $invoice_no, 'Completed');
    
    if (!$order_id) {
        throw new Exception('Failed to create order');
    }
    
    // Add order details for each cart item
    foreach ($cart_items as $item) {
        if (!add_order_details_ctr($order_id, $item['p_id'], $item['qty'])) {
            throw new Exception('Failed to add order details');
        }
    }
    
    // Record payment
    if (!record_payment_ctr($order_id, $customer_id, $total_amount)) {
        throw new Exception('Failed to record payment');
    }
    
    // Empty cart
    if (!empty_cart_ctr($customer_id)) {
        throw new Exception('Failed to empty cart');
    }
    
    // Commit transaction
    $conn->commit();
    
    $response['success'] = true;
    $response['message'] = 'Order placed successfully';
    $response['order_id'] = $order_id;
    $response['invoice_no'] = $invoice_no;
    $response['total_amount'] = number_format($total_amount, 2);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>