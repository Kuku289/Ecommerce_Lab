<?php
/**
 * Update Category Action - Updates an existing category
 * File: actions/update_category_action.php
 */

// Start session FIRST
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Initialize response array
$response = array();

try {
    // Require core functions
    require_once '../settings/core.php';

    // Check if user is logged in
    if (!check_login()) {
        $response['status'] = 'error';
        $response['message'] = 'You must be logged in';
        echo json_encode($response);
        exit();
    }

    // Check if user is admin
    if (!check_admin()) {
        $response['status'] = 'error';
        $response['message'] = 'Admin access required';
        echo json_encode($response);
        exit();
    }

    // Check if required fields are provided
    if (!isset($_POST['cat_id']) || !isset($_POST['category_name'])) {
        $response['status'] = 'error';
        $response['message'] = 'Category ID and name are required';
        echo json_encode($response);
        exit();
    }

    // Require category controller
    require_once '../controllers/category_controller.php';

    // Get and validate inputs
    $cat_id = intval($_POST['cat_id']);
    $category_name = trim($_POST['category_name']);
    $user_id = get_user_id();

    // Validate category ID
    if (empty($cat_id) || $cat_id <= 0) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid category ID';
        echo json_encode($response);
        exit();
    }

    // Validate category name
    if (empty($category_name)) {
        $response['status'] = 'error';
        $response['message'] = 'Category name cannot be empty';
        echo json_encode($response);
        exit();
    }

    if (strlen($category_name) < 2) {
        $response['status'] = 'error';
        $response['message'] = 'Category name must be at least 2 characters';
        echo json_encode($response);
        exit();
    }

    if (strlen($category_name) > 100) {
        $response['status'] = 'error';
        $response['message'] = 'Category name must not exceed 100 characters';
        echo json_encode($response);
        exit();
    }

    // Validate user ID
    if (empty($user_id) || !is_numeric($user_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid user session';
        echo json_encode($response);
        exit();
    }

    // Update category
    $result = update_category_ctr($cat_id, $category_name, $user_id);

    if ($result === true) {
        $response['status'] = 'success';
        $response['message'] = 'Category updated successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to update category. Name may already exist.';
    }

} catch (Exception $e) {
    error_log("Exception in update_category_action: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

// Log final response for debugging
error_log("UPDATE CATEGORY RESPONSE: " . json_encode($response));

// Send response
echo json_encode($response);
exit();
?>