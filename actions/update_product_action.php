<?php
session_start();
require_once(dirname(__FILE__) . '/../controllers/product_controller.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Validate required fields
if (empty($_POST['product_id']) || empty($_POST['product_cat']) || 
    empty($_POST['product_brand']) || empty($_POST['product_title']) || 
    empty($_POST['product_price']) || empty($_POST['product_desc']) || 
    empty($_POST['product_keywords'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Get form data
$product_id = intval($_POST['product_id']);
$category_id = intval($_POST['product_cat']);
$brand_id = intval($_POST['product_brand']);
$title = trim($_POST['product_title']);
$price = floatval($_POST['product_price']);
$description = trim($_POST['product_desc']);
$keywords = trim($_POST['product_keywords']);
$image = isset($_POST['product_image']) ? trim($_POST['product_image']) : '';

// Validate price
if ($price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Price must be greater than 0']);
    exit();
}

// Update product
$result = update_product_ctr($product_id, $category_id, $brand_id, $title, $price, $description, $image, $keywords);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update product']);
}
?>