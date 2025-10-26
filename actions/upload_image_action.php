<?php
session_start();
require_once(dirname(__FILE__) . '../controllers/product_controller.php');

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

// Check if file was uploaded
if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

$file = $_FILES['product_image'];
$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

// Validate file type
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$file_type = mime_content_type($file['tmp_name']);

if (!in_array($file_type, $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed']);
    exit();
}

// Validate file size (max 5MB)
$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit']);
    exit();
}

// Get file extension
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Create directory structure: uploads/u{user_id}/p{product_id}/
$base_upload_dir = dirname(__FILE__) . '/../uploads/';
$user_dir = $base_upload_dir . 'u' . $user_id . '/';
$product_dir = $user_dir . 'p' . $product_id . '/';

// Verify we're within the uploads directory
$real_base = realpath($base_upload_dir);
$real_target = realpath(dirname($product_dir));

if ($real_target === false || strpos($real_target, $real_base) !== 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid upload directory']);
    exit();
}

// Create directories if they don't exist
if (!file_exists($product_dir)) {
    if (!mkdir($product_dir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
        exit();
    }
}

// Generate unique filename
$filename = 'image_' . time() . '_' . uniqid() . '.' . $file_extension;
$file_path = $product_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // Store relative path for database
    $relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $filename;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Image uploaded successfully',
        'image_path' => $relative_path
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
}
?>