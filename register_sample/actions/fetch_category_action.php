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

$user_id = get_user_id();

$categories = get_categories_by_user_ctr($user_id);

if ($categories !== false) {
    $response['status'] = 'success';
    $response['categories'] = $categories;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to fetch categories';
}

echo json_encode($response);
?>