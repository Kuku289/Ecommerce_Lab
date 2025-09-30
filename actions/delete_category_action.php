<?php
/**
 * Delete Category Action - Deletes a category
 * File: actions/delete_category_action.php
 */

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

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

    if (!isset($_POST['cat_id']) || empty($_POST['cat_id'])) {
        $response['status'] = 'error';
        $response['message'] = 'Category ID is required';
        echo json_encode($response);
        exit();
    }

    require_once '../controllers/category_controller.php';

    $cat_id = intval($_POST['cat_id']);
    $user_id = get_user_id();

    if (empty($cat_id) || $cat_id <= 0) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid category ID';
        echo json_encode($response);
        exit();
    }

    if (empty($user_id) || !is_numeric($user_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid user session';
        echo json_encode($response);
        exit();
    }

    $result = delete_category_ctr($cat_id, $user_id);

    if ($result === true) {
        $response['status'] = 'success';
        $response['message'] = 'Category deleted successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to delete category';
    }

} catch (Exception $e) {
    error_log("Exception in delete_category_action: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

error_log("DELETE CATEGORY RESPONSE: " . json_encode($response));

echo json_encode($response);
exit();
?>