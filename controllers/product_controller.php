<?php
require_once(dirname(__FILE__) . '/../classes/product_class.php');

// Add product controller
function add_product_ctr($category_id, $brand_id, $title, $price, $description, $image, $keywords) {
    $product = new Product();
    return $product->add_product($category_id, $brand_id, $title, $price, $description, $image, $keywords);
}

// Get all products controller
function get_all_products_ctr() {
    $product = new Product();
    return $product->get_all_products();
}

// Get single product controller
function get_product_by_id_ctr($product_id) {
    $product = new Product();
    return $product->get_product_by_id($product_id);
}

// Update product controller
function update_product_ctr($product_id, $category_id, $brand_id, $title, $price, $description, $image, $keywords) {
    $product = new Product();
    return $product->update_product($product_id, $category_id, $brand_id, $title, $price, $description, $image, $keywords);
}

// Delete product controller
function delete_product_ctr($product_id) {
    $product = new Product();
    return $product->delete_product($product_id);
}

// Get products by category controller
function get_products_by_category_ctr($category_id) {
    $product = new Product();
    return $product->get_products_by_category($category_id);
}

// Get products by brand controller
function get_products_by_brand_ctr($brand_id) {
    $product = new Product();
    return $product->get_products_by_brand($brand_id);
}
?>