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
if (!isset($_POST['supplier_id']) || !isset($_POST['verification_status'])) {
    $response['message'] = 'Missing required fields';
    echo json_encode($response);
    exit();
}

$supplier_id = intval($_POST['supplier_id']);
$admin_id = $_SESSION['user_id'];
$status = $_POST['verification_status']; // 'Verified' or 'Rejected'

$fda = isset($_POST['fda_approved']) ? 1 : 0;
$organic = isset($_POST['organic_certified']) ? 1 : 0;
$fair_trade = isset($_POST['fair_trade_certified']) ? 1 : 0;
$local = isset($_POST['local_farmer']) ? 1 : 0;

// Verify supplier
if (verify_supplier_ctr($supplier_id, $admin_id, $status, $fda, $organic, $fair_trade, $local)) {
    $response['success'] = true;
    $response['message'] = 'Supplier verification updated successfully';
} else {
    $response['message'] = 'Failed to update verification status';
}

echo json_encode($response);
?>