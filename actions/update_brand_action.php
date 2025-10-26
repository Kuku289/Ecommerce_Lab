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

    if (!isset($_POST['brand_id']) || !isset($_POST['brand_name'])) {
        $response['status'] = 'error';
        $response['message'] = 'Brand ID and name are required';
        echo json_encode($response);
        exit();
    }

    require_once '../controllers/brand_controller.php';

    $brand_id = intval($_POST['brand_id']);
    $brand_name = trim($_POST['brand_name']);
    $user_id = get_user_id();

    if (empty($brand_id) || $brand_id <= 0) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid brand ID';
        echo json_encode($response);
        exit();
    }

    if (empty($brand_name) || strlen($brand_name) < 2) {
        $response['status'] = 'error';
        $response['message'] = 'Brand name must be at least 2 characters';
        echo json_encode($response);
        exit();
    }

    $result = update_brand_ctr($brand_id, $brand_name, $user_id);

    if ($result === true) {
        $response['status'] = 'success';
        $response['message'] = 'Brand updated successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to update brand. Name may already exist.';
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>