<?php
session_start();
header('Content-Type: application/json');

require_once '../controllers/supplier_controller.php';

$response = ['success' => false, 'message' => '', 'supplier_id' => null];

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    $response['message'] = 'Unauthorized access';
    echo json_encode($response);
    exit();
}

// Validate required fields
if (!isset($_POST['supplier_name']) || !isset($_POST['supplier_email'])) {
    $response['message'] = 'Missing required fields';
    echo json_encode($response);
    exit();
}

$name = trim($_POST['supplier_name']);
$email = trim($_POST['supplier_email']);
$phone = isset($_POST['supplier_phone']) ? trim($_POST['supplier_phone']) : '';
$address = isset($_POST['supplier_address']) ? trim($_POST['supplier_address']) : '';
$description = isset($_POST['supplier_description']) ? trim($_POST['supplier_description']) : '';

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// Handle logo upload
$logo_path = null;
if (isset($_FILES['supplier_logo']) && $_FILES['supplier_logo']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['supplier_logo']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (in_array($ext, $allowed)) {
        $upload_dir = '../uploads/suppliers/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $new_filename = 'supplier_' . time() . '_' . uniqid() . '.' . $ext;
        $destination = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['supplier_logo']['tmp_name'], $destination)) {
            $logo_path = 'uploads/suppliers/' . $new_filename;
        }
    }
}

// Add supplier
$supplier_id = add_supplier_ctr($name, $email, $phone, $address, $description, $logo_path);

if ($supplier_id) {
    $response['success'] = true;
    $response['message'] = 'Supplier added successfully';
    $response['supplier_id'] = $supplier_id;
} else {
    $response['message'] = 'Failed to add supplier';
}

echo json_encode($response);
?>