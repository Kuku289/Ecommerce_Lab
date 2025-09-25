<?php

header('Content-Type: application/json');

session_start();

$response = array();

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
    echo json_encode($response);
    exit();
}

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$phone_number = $_POST['phone_number'];
$role = isset($_POST['role']) ? $_POST['role'] : 2; // Default to customer role
$country = isset($_POST['country']) ? $_POST['country'] : null;
$city = isset($_POST['city']) ? $_POST['city'] : null;

// Check if email already exists
if (check_email_exists_ctr($email)) {
    $response['status'] = 'error';
    $response['message'] = 'Email already exists';
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
    $response['message'] = 'Failed to register';
}

echo json_encode($response);