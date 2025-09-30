<?php
/**
 * Add Category Action - Handles category creation requests
 * File: actions/add_category_action.php
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

    // Check if category name is provided
    if (!isset($_POST['category_name']) || empty(trim($_POST['category_name']))) {
        $response['status'] = 'error';
        $response['message'] = 'Category name is required';
        echo json_encode($response);
        exit();
    }

    // Get and sanitize category name
    $category_name = trim($_POST['category_name']);

    // Validate category name length
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

    // Get user ID
    $user_id = get_user_id();

    // Validate user ID
    if (empty($user_id) || !is_numeric($user_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid user session';
        echo json_encode($response);
        exit();
    }

    // Require category controller
    require_once '../controllers/category_controller.php';

    // Check if category already exists
    if (check_category_exists_ctr($category_name, $user_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Category name already exists';
        echo json_encode($response);
        exit();
    }

    // Attempt to add category
    $category_id = add_category_ctr($category_name, $user_id);

    // Check if category was added successfully
    if ($category_id !== false && is_numeric($category_id) && $category_id > 0) {
        $response['status'] = 'success';
        $response['message'] = 'Category added successfully';
        $response['category_id'] = intval($category_id);
        $response['category_name'] = $category_name;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to add category. Please try again.';
    }

} catch (Exception $e) {
    error_log("Exception in add_category_action: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

// Log final response for debugging
error_log("FINAL RESPONSE: " . json_encode($response));

// Send response
echo json_encode($response);
exit();
?>