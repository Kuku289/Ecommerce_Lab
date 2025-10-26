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

    if (!isset($_POST['brand_id']) || empty($_POST['brand_id'])) {
        $response['status'] = 'error';
        $response['message'] = 'Brand ID is required';
        echo json_encode($response);
        exit();
    }

    require_once '../controllers/brand_controller.php';

    $brand_id = intval($_POST['brand_id']);
    $user_id = get_user_id();

    if (empty($brand_id) || $brand_id <= 0) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid brand ID';
        echo json_encode($response);
        exit();
    }

    $result = delete_brand_ctr($brand_id, $user_id);

    if ($result === true) {
        $response['status'] = 'success';
        $response['message'] = 'Brand deleted successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to delete brand';
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>