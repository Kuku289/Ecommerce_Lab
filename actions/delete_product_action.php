<?php
// actions/delete_product_action.php
header('Content-Type: application/json');
require_once '../controllers/product_controller.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate product ID
        if (empty($_POST['product_id'])) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            exit();
        }
        
        $product_id = intval($_POST['product_id']);
        
        // Get product details before deleting (to delete image)
        $product = get_product_ctr($product_id);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit();
        }
        
        // Delete product from database
        $result = delete_product_ctr($product_id);
        
        if ($result) {
            // Delete product image if it exists and is not placeholder
            if (!empty($product['product_image']) && 
                $product['product_image'] !== 'uploads/placeholder.png' && 
                file_exists("../" . $product['product_image'])) {
                unlink("../" . $product['product_image']);
                
                // Optionally delete the product directory if empty
                $dir = dirname("../" . $product['product_image']);
                if (is_dir($dir) && count(scandir($dir)) == 2) { // Only . and ..
                    rmdir($dir);
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>