<?php
require_once(dirname(__FILE__) . '/../classes/product_class.php');

// ⭐ LAB REQUIRED: View all products controller
function view_all_products_ctr() {
    $product = new Product();
    return $product->view_all_products();
}

// ⭐ LAB REQUIRED: Search products controller
function search_products_ctr($query) {
    $product = new Product();
    return $product->search_products($query);
}

// ⭐ LAB REQUIRED: Filter products by category controller
function filter_products_by_category_ctr($cat_id) {
    $product = new Product();
    return $product->filter_products_by_category($cat_id);
}

// ⭐ LAB REQUIRED: Filter products by brand controller
function filter_products_by_brand_ctr($brand_id) {
    $product = new Product();
    return $product->filter_products_by_brand($brand_id);
}

// ⭐ LAB REQUIRED: View single product controller
function view_single_product_ctr($id) {
    $product = new Product();
    return $product->view_single_product($id);
}

// ⭐ EXTRA CREDIT: Search by keyword controller
function search_by_keyword_ctr($keyword) {
    $product = new Product();
    return $product->search_by_keyword($keyword);
}

// ⭐ EXTRA CREDIT: Composite search controller
function composite_search_ctr($filters) {
    $product = new Product();
    return $product->composite_search($filters);
}

// Add product controller
function add_product_ctr($category_id, $brand_id, $title, $price, $description, $image, $keywords) {
    $product = new Product();
    return $product->add_product($category_id, $brand_id, $title, $price, $description, $image, $keywords);
}

// Get all products controller (legacy)
function get_all_products_ctr() {
    return view_all_products_ctr();
}

// Get single product controller (legacy)
function get_product_by_id_ctr($product_id) {
    return view_single_product_ctr($product_id);
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

// Get products by category controller (legacy)
function get_products_by_category_ctr($category_id) {
    return filter_products_by_category_ctr($category_id);
}

// Get products by brand controller (legacy)
function get_products_by_brand_ctr($brand_id) {
    return filter_products_by_brand_ctr($brand_id);
}

// Update product image controller
function update_product_image_ctr($product_id, $image_path) {
    $product = new Product();
    return $product->update_product_image($product_id, $image_path);
}


?>