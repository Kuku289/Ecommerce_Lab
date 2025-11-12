<?php
/**
 * Order Controller - Business logic for order operations
 */

require_once(dirname(__FILE__) . '/../classes/order_class.php');

/**
 * Create a new order
 */
function create_order_ctr($customer_id, $invoice_no, $order_status = 'Pending') {
    $order = new Order();
    return $order->create_order($customer_id, $invoice_no, $order_status);
}

/**
 * Add order details
 */
function add_order_details_ctr($order_id, $product_id, $quantity) {
    $order = new Order();
    return $order->add_order_details($order_id, $product_id, $quantity);
}

/**
 * Record payment
 */
function record_payment_ctr($order_id, $customer_id, $amount) {
    $order = new Order();
    return $order->record_payment($order_id, $customer_id, $amount);
}

/**
 * Get customer orders
 */
function get_customer_orders_ctr($customer_id) {
    $order = new Order();
    return $order->get_customer_orders($customer_id);
}

/**
 * Get order details
 */
function get_order_details_ctr($order_id) {
    $order = new Order();
    return $order->get_order_details($order_id);
}

/**
 * Get single order
 */
function get_order_ctr($order_id) {
    $order = new Order();
    return $order->get_order($order_id);
}
?>