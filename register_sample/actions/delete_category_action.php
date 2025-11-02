<?php

header('Content-Type: application/json');

session_start();

$response = array();

// Check if user is logged in and is admin
require_once '../settings/core.php';

if (!check_login()) {
    $response['status'] = 'error';
    $response['message'] = 'You must be logged in';
    echo json_encode($response);
    exit();
}

if (!check_admin()) {
    $response['status'] = 'error';
    $response['message'] = 'Admin access required';
    echo json_encode($response);
    exit();
}

require_once '../controllers/category_controller.php';

// Check if category ID is provided
if (!isset($_POST['cat_id']) || empty($_POST['cat_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'Category ID is required';
    echo json_encode($response);
    exit();
}

$cat_id = $_POST['cat_id'];
$user_id = get_user_id();

$result = delete_category_ctr($cat_id, $user_id);

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Category deleted successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to delete category';
}

echo json_encode($response);
?>