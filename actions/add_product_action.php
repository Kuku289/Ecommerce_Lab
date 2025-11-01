<?php
// actions/add_product_action.php
session_start();
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = 'User not logged in';
        echo json_encode($response);
        exit();
    }

    // Check if user is admin
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
        $response['message'] = 'Unauthorized access. Admin rights required.';
        echo json_encode($response);
        exit();
    }

    require_once '../controllers/product_controller.php';

    // Validate required POST fields
    if (empty($_POST['product_cat']) || empty($_POST['product_brand']) || 
        empty($_POST['product_title']) || empty($_POST['product_price']) || 
        empty($_POST['product_desc'])) {
        $response['message'] = 'All required fields must be filled';
        echo json_encode($response);
        exit();
    }

    // Validate image upload
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'Please upload a product image';
        echo json_encode($response);
        exit();
    }

    // Get form data
    $cat_id = intval($_POST['product_cat']);
    $brand_id = intval($_POST['product_brand']);
    $title = trim($_POST['product_title']);
    $price = floatval($_POST['product_price']);
    $desc = trim($_POST['product_desc']);
    $keywords = isset($_POST['product_keywords']) ? trim($_POST['product_keywords']) : '';
    $user_id = intval($_SESSION['user_id']);

    // Validate price
    if ($price <= 0) {
        $response['message'] = 'Price must be greater than 0';
        echo json_encode($response);
        exit();
    }

    // Validate title length
    if (strlen($title) < 3) {
        $response['message'] = 'Product title must be at least 3 characters';
        echo json_encode($response);
        exit();
    }

    // Handle image upload
    $file = $_FILES['product_image'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Validate file type using mime_content_type
    $file_type = mime_content_type($file['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        $response['message'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed';
        echo json_encode($response);
        exit();
    }

    // Validate file size
    if ($file['size'] > $max_size) {
        $response['message'] = 'Image size must be less than 5MB';
        echo json_encode($response);
        exit();
    }

    // Step 1: Insert product with temporary image path
    $temp_image_path = 'uploads/placeholder.png';
    
    $product_id = add_product_ctr($cat_id, $brand_id, $title, $price, $desc, $temp_image_path, $keywords);

    if (!$product_id || !is_numeric($product_id) || $product_id <= 0) {
        $response['message'] = 'Failed to add product to database';
        echo json_encode($response);
        exit();
    }

    // Step 2: Create directory structure: uploads/u{USER_ID}/p{PRODUCT_ID}/
    $base_upload_dir = dirname(__FILE__) . '/../uploads/';
    $user_dir = $base_upload_dir . 'u' . $user_id . '/';
    $product_dir = $user_dir . 'p' . $product_id . '/';

    // Create directories if they don't exist
    if (!file_exists($product_dir)) {
        if (!mkdir($product_dir, 0755, true)) {
            $response['message'] = 'Failed to create upload directory. Check folder permissions.';
            echo json_encode($response);
            exit();
        }
    }

    // Step 3: Generate unique filename and upload
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'product_' . $product_id . '_' . time() . '.' . $file_extension;
    $file_path = $product_dir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        $response['message'] = 'Failed to upload image file';
        echo json_encode($response);
        exit();
    }

    // Step 4: Update product with actual image path (relative path for database)
    $relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $filename;
    
    if (!update_product_ctr($product_id, $cat_id, $brand_id, $title, $price, $desc, $relative_path, $keywords)) {
        $response['message'] = 'Product added but failed to update image path';
        echo json_encode($response);
        exit();
    }

    // Success!
    $response['success'] = true;
    $response['message'] = 'Product added successfully!';
    $response['product_id'] = $product_id;
    $response['image_path'] = $relative_path;

} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
    error_log("Add Product Error: " . $e->getMessage());
}

echo json_encode($response);
?>