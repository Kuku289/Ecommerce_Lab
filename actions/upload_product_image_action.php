<?php
// Start session
session_start();

// Include necessary files
require_once('../settings/core.php');

// Check if user is logged in and is admin
if (!check_login()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if (!check_admin()) {
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit();
}

require_once(dirname(__FILE__) . '/../controllers/product_controller.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Check if product_id is provided
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        exit();
    }
    
    $product_id = intval($_POST['product_id']);
    
    // Check if file was uploaded
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] == UPLOAD_ERR_NO_FILE) {
        echo json_encode(['success' => false, 'message' => 'Please select an image to upload']);
        exit();
    }
    
    $file = $_FILES['product_image'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        echo json_encode(['success' => false, 'message' => $error_messages[$file['error']] ?? 'Unknown upload error']);
        exit();
    }
    
    // Validate file size (5MB max)
    $maxFileSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxFileSize) {
        echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
        exit();
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed']);
        exit();
    }
    
    // Get file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Generate unique filename
    $new_filename = 'product_' . $product_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
    
    // Define upload directory
    $upload_dir = dirname(dirname(__FILE__)) . '/uploads/';
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create uploads directory']);
            exit();
        }
    }
    
    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        echo json_encode(['success' => false, 'message' => 'Uploads directory is not writable']);
        exit();
    }
    
    $destination = $upload_dir . $new_filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Store relative path in database
        $relative_path = 'uploads/' . $new_filename;
        
        // Update product image in database
        $result = update_product_image_ctr($product_id, $relative_path);
        
        if ($result) {
            echo json_encode([
                'success' => true, 
                'message' => 'Product image uploaded successfully!',
                'image_path' => $relative_path
            ]);
        } else {
            // Delete uploaded file if database update fails
            if (file_exists($destination)) {
                unlink($destination);
            }
            echo json_encode(['success' => false, 'message' => 'Failed to update product image in database']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file. Check permissions.']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>