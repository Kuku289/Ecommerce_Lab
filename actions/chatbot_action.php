<?php
session_start();
header('Content-Type: application/json');

require_once '../chatbot/chatbot_handler.php';

$response = ['success' => false, 'response' => '', 'error' => ''];

// Check if message is provided
if (!isset($_POST['message']) || empty(trim($_POST['message']))) {
    $response['error'] = 'Please enter a message';
    echo json_encode($response);
    exit();
}

$message = trim($_POST['message']);
$customer_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Process message
$result = ChatbotHandler::processMessage($message, $customer_id);

echo json_encode($result);
?>