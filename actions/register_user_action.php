<?php

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$response = array();

try {
    // Check if the user is already logged in
    if (isset($_SESSION['user_id'])) {
        $response['status'] = 'error';
        $response['message'] = 'You are already logged in';
        echo json_encode($response);
        exit();
    }

    require_once '../controllers/user_controller.php';

    // Check if required fields are present
    if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['phone_number'])) {
        $response['status'] = 'error';
        $response['message'] = 'Required fields are missing';
        $response['debug'] = $_POST;
        echo json_encode($response);
        exit();
    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone_number = trim($_POST['phone_number']);
    $role = isset($_POST['role']) ? $_POST['role'] : 2;
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : '';

    // Validate inputs
    if (empty($name)) {
        $response['status'] = 'error';
        $response['message'] = 'Name is required';
        echo json_encode($response);
        exit();
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = 'Valid email is required';
        echo json_encode($response);
        exit();
    }

    if (empty($password) || strlen($password) < 6) {
        $response['status'] = 'error';
        $response['message'] = 'Password must be at least 6 characters';
        echo json_encode($response);
        exit();
    }

    if (empty($phone_number)) {
        $response['status'] = 'error';
        $response['message'] = 'Phone number is required';
        echo json_encode($response);
        exit();
    }

    $user_id = register_user_ctr($name, $email, $password, $phone_number, $role, $country, $city);

    if ($user_id) {
        $response['status'] = 'success';
        $response['message'] = 'Registered successfully';
        $response['user_id'] = $user_id;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to register. Email may already exist or database error.';
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'Exception: ' . $e->getMessage();
}

echo json_encode($response);
?>