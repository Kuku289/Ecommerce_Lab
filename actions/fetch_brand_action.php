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

    require_once '../controllers/brand_controller.php';

    $user_id = get_user_id();

    if (empty($user_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid user session';
        echo json_encode($response);
        exit();
    }

    $brands = get_brands_by_user_ctr($user_id);

    if ($brands !== false) {
        $response['status'] = 'success';
        $response['brands'] = $brands;
        $response['count'] = count($brands);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to retrieve brands';
        $response['brands'] = array();
        $response['count'] = 0;
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'An error occurred: ' . $e->getMessage();
    $response['brands'] = array();
    $response['count'] = 0;
}

echo json_encode($response);
?>