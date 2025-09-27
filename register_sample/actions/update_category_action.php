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

// Check if required fields are provided
if (!isset($_POST['cat_id']) || !isset($_POST['category_name']) || 
    empty($_POST['cat_id']) || empty($_POST['category_name'])) {
    $response['status'] = 'error';
    $response['message'] = 'Category ID and name are required';
    echo json_encode($response);
    exit();
}

$cat_id = $_POST['cat_id'];
$category_name = trim($_POST['category_name']);
$user_id = get_user_id();

$result = update_category_ctr($cat_id, $category_name, $user_id);

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Category updated successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to update category or name already exists';
}

echo json_encode($response);
?>