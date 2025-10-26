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

// Validate product ID
if (empty($_POST['product_id']) && empty($_GET['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

// Get product ID from POST or GET
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : intval($_GET['product_id']);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit();
}

// Get product details before deleting (to delete image file)
try {
    $product = get_product_by_id_ctr($product_id);
    
    // Delete product from database
    $result = delete_product_ctr($product_id);
    
    if ($result) {
        // Try to delete the product image file if it exists
        if ($product && !empty($product['product_image'])) {
            $image_path = dirname(__FILE__) . '/../' . $product['product_image'];
            if (file_exists($image_path)) {
                @unlink($image_path); // @ suppresses errors if file can't be deleted
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>