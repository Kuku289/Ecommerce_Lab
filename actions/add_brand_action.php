<?php
session_start();
header('Content-Type: application/json');

$response = array();

try {
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

    if (!isset($_POST['brand_name']) || !isset($_POST['cat_id'])) {
        $response['status'] = 'error';
        $response['message'] = 'Brand name and category are required';
        echo json_encode($response);
        exit();
    }

    require_once '../controllers/brand_controller.php';

    $brand_name = trim($_POST['brand_name']);
    $cat_id = intval($_POST['cat_id']);
    $user_id = get_user_id();

    if (empty($brand_name) || strlen($brand_name) < 2) {
        $response['status'] = 'error';
        $response['message'] = 'Brand name must be at least 2 characters';
        echo json_encode($response);
        exit();
    }

    if (empty($cat_id) || $cat_id <= 0) {
        $response['status'] = 'error';
        $response['message'] = 'Please select a valid category';
        echo json_encode($response);
        exit();
    }

    $brand_id = add_brand_ctr($brand_name, $cat_id, $user_id);

    if ($brand_id && is_numeric($brand_id) && $brand_id > 0) {
        $response['status'] = 'success';
        $response['message'] = 'Brand added successfully';
        $response['brand_id'] = $brand_id;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to add brand. Brand may already exist in this category.';
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>