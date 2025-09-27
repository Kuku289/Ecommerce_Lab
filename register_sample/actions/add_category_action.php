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

// Check if category name is provided
if (!isset($_POST['category_name']) || empty($_POST['category_name'])) {
    $response['status'] = 'error';
    $response['message'] = 'Category name is required';
    echo json_encode($response);
    exit();
}

$category_name = trim($_POST['category_name']);
$user_id = get_user_id();

// Check if category already exists
if (check_category_exists_ctr($category_name, $user_id)) {
    $response['status'] = 'error';
    $response['message'] = 'Category name already exists';
    echo json_encode($response);
    exit();
}

$category_id = add_category_ctr($category_name, $user_id);

if ($category_id) {
    $response['status'] = 'success';
    $response['message'] = 'Category added successfully';
    $response['category_id'] = $category_id;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to add category';
}

echo json_encode($response);
?>