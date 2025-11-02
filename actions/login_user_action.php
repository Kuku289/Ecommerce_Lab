<?php

header('Content-Type: application/json');

session_start();

$response = array();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/user_controller.php';

// Check if required fields are present
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    $response['status'] = 'error';
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];

$user = login_user_ctr($email, $password);

if ($user) {
    // Set session variables
    $_SESSION['user_id'] = $user['customer_id'];
    $_SESSION['user_name'] = $user['customer_name'];
    $_SESSION['user_email'] = $user['customer_email'];
    $_SESSION['user_role'] = $user['user_role'];
    
    // ⭐ CHANGED: Determine redirect based on role
    if ($user['user_role'] == 1) {
        // Admin - redirect to admin product management
        $redirect_url = '../admin/product.php';
    } else {
        // Regular user - redirect to shopping
        $redirect_url = '../view/all_product.php';
    }
    
    $response['status'] = 'success';
    $response['message'] = 'Login successful';
    $response['redirect'] = $redirect_url; // ⭐ ADDED: Redirect URL
    $response['user'] = array(
        'id' => $user['customer_id'],
        'name' => $user['customer_name'],
        'email' => $user['customer_email'],
        'role' => $user['user_role']
    );
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email or password';
}

echo json_encode($response);
?>