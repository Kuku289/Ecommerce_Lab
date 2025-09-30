<?php
/**
 * Get Categories Action - Retrieves all categories for logged in user
 * File: actions/get_categories_action.php
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

    // Require category controller
    require_once '../controllers/category_controller.php';

    // Get user ID
    $user_id = get_user_id();

    // Validate user ID
    if (empty($user_id) || !is_numeric($user_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid user session';
        echo json_encode($response);
        exit();
    }

    // Get categories
    $categories = get_categories_by_user_ctr($user_id);

    // Check if we got categories
    if ($categories !== false) {
        $response['status'] = 'success';
        $response['categories'] = $categories;
        $response['count'] = count($categories);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to retrieve categories';
        $response['categories'] = array();
        $response['count'] = 0;
    }

} catch (Exception $e) {
    error_log("Exception in get_categories_action: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An error occurred: ' . $e->getMessage();
    $response['categories'] = array();
    $response['count'] = 0;
}

// Log final response for debugging
error_log("GET CATEGORIES RESPONSE: " . json_encode($response));

// Send response
echo json_encode($response);
exit();
?>