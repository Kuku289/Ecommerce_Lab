<?php
session_start();
header('Content-Type: application/json');

require_once '../controllers/supplier_controller.php';

$response = ['success' => false, 'message' => ''];

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    $response['message'] = 'Unauthorized access';
    echo json_encode($response);
    exit();
}

// Validate required fields
if (!isset($_POST['supplier_id'])) {
    $response['message'] = 'Missing supplier ID';
    echo json_encode($response);
    exit();
}

$supplier_id = intval($_POST['supplier_id']);

// Delete supplier
if (delete_supplier_ctr($supplier_id)) {
    $response['success'] = true;
    $response['message'] = 'Supplier deleted successfully';
} else {
    $response['message'] = 'Failed to delete supplier';
}

echo json_encode($response);
?>