<?php
/**
 * Cart Controller - Business logic for cart operations
 */

require_once(dirname(__FILE__) . '/../classes/cart_class.php');

/**
 * Add item to cart
 */
function add_to_cart_ctr($product_id, $customer_id, $quantity, $ip_address) {
    $cart = new Cart();
    return $cart->add_to_cart($product_id, $customer_id, $quantity, $ip_address);
}

/**
 * Get all cart items for a customer
 */
function get_cart_items_ctr($customer_id) {
    $cart = new Cart();
    return $cart->get_cart_items($customer_id);
}

/**
 * Update cart item quantity
 */
function update_cart_quantity_ctr($product_id, $customer_id, $quantity) {
    $cart = new Cart();
    return $cart->update_cart_quantity($product_id, $customer_id, $quantity);
}

/**
 * Remove item from cart
 */
function remove_from_cart_ctr($product_id, $customer_id) {
    $cart = new Cart();
    return $cart->remove_from_cart($product_id, $customer_id);
}

/**
 * Empty entire cart
 */
function empty_cart_ctr($customer_id) {
    $cart = new Cart();
    return $cart->empty_cart($customer_id);
}

/**
 * Get cart total
 */
function get_cart_total_ctr($customer_id) {
    $cart = new Cart();
    return $cart->get_cart_total($customer_id);
}

/**
 * Get cart item count
 */
function get_cart_count_ctr($customer_id) {
    $cart = new Cart();
    return $cart->get_cart_count($customer_id);
}
?>