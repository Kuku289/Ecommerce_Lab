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

    // ⭐ CHANGED: Image is now OPTIONAL during product creation
    $image_path = ''; // Default to empty if no image uploaded
    
    // Check if image was uploaded
    $image_uploaded = isset($_FILES['product_image']) && 
                     $_FILES['product_image']['error'] === UPLOAD_ERR_OK;

    if ($image_uploaded) {
        // Image was uploaded - validate and process it
        $file = $_FILES['product_image'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Validate file type
        $file_type = mime_content_type($file['tmp_name']);
        if (!in_array($file_type, $allowed_types)) {
            $response['message'] = 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed';
            echo json_encode($response);
            exit();
        }

        // Validate file size
        if ($file['size'] > $max_size) {
            $response['message'] = 'Image size must be less than 5MB';
            echo json_encode($response);
            exit();
        }

        // Step 1: Add product first to get product_id
        $temp_image_path = '';
        $product_id = add_product_ctr($cat_id, $brand_id, $title, $price, $desc, $temp_image_path, $keywords);

        if (!$product_id || !is_numeric($product_id) || $product_id <= 0) {
            $response['message'] = 'Failed to add product to database';
            echo json_encode($response);
            exit();
        }

        // Step 2: Upload the image
        $base_upload_dir = dirname(__FILE__) . '/../uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($base_upload_dir)) {
            if (!mkdir($base_upload_dir, 0755, true)) {
                $response['message'] = 'Failed to create upload directory';
                echo json_encode($response);
                exit();
            }
        }

        // Generate unique filename
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'product_' . $product_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
        $file_path = $base_upload_dir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            $response['message'] = 'Product added but failed to upload image';
            $response['success'] = true; // Product was still added
            $response['product_id'] = $product_id;
            echo json_encode($response);
            exit();
        }

        // Step 3: Update product with image path
        $image_path = 'uploads/' . $filename;
        
        if (!update_product_image_ctr($product_id, $image_path)) {
            $response['message'] = 'Product added but failed to update image path';
            $response['success'] = true;
            $response['product_id'] = $product_id;
            echo json_encode($response);
            exit();
        }

        // Success with image!
        $response['success'] = true;
        $response['message'] = 'Product added successfully with image!';
        $response['product_id'] = $product_id;
        $response['image_path'] = $image_path;

    } else {
        // ⭐ No image uploaded - just add product without image
        $image_path = '';
        $product_id = add_product_ctr($cat_id, $brand_id, $title, $price, $desc, $image_path, $keywords);

        if (!$product_id || !is_numeric($product_id) || $product_id <= 0) {
            $response['message'] = 'Failed to add product to database';
            echo json_encode($response);
            exit();
        }

        // Success without image!
        $response['success'] = true;
        $response['message'] = 'Product added successfully! You can upload an image by editing the product.';
        $response['product_id'] = $product_id;
    }

} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
    error_log("Add Product Error: " . $e->getMessage());
}

echo json_encode($response);
?>