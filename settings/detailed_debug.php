<?php
require_once 'controllers/user_controller.php';

// Test data
$name = "Test User";
$email = "test@example.com";
$password = "Test123";
$phone_number = "123456789";
$role = 2;
$country = "Ghana";
$city = "Accra";

echo "Testing each step...<br><br>";

// Test 1: Basic validation
echo "1. Testing basic validation:<br>";
if (empty($name) || empty($email) || empty($password) || empty($phone_number)) {
    echo "❌ Required fields missing<br>";
} else {
    echo "✅ Required fields present<br>";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Invalid email format<br>";
} else {
    echo "✅ Email format valid<br>";
}

// Test 2: Check email exists
echo "<br>2. Testing email existence:<br>";
$email_exists = check_email_exists_ctr($email);
echo "Email exists: " . ($email_exists ? "YES" : "NO") . "<br>";

// Test 3: Direct user class test
echo "<br>3. Testing user class directly:<br>";
require_once 'classes/user_class.php';
$user = new User();
$user_id = $user->createUser($name, $email, $password, $phone_number, $role, $country, $city);
echo "Direct user creation result: " . ($user_id ? "SUCCESS (ID: $user_id)" : "FAILED") . "<br>";

// Test 4: Controller test
echo "<br>4. Testing controller:<br>";
$result = register_user_ctr($name, $email, $password, $phone_number, $role, $country, $city);
echo "Controller result: " . ($result ? "SUCCESS (ID: $result)" : "FAILED") . "<br>";
?>