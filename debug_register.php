<?php
require_once 'controllers/user_controller.php';

$name = "Debug User";
$email = "debug" . time() . "@test.com";
$password = "Test123";
$phone = "1234567890";
$role = 2;

echo "Testing controller directly:<br>";
echo "Email: $email<br><br>";

$result = register_user_ctr($name, $email, $password, $phone, $role);
echo "Controller returned: " . ($result ? "SUCCESS (ID: $result)" : "FALSE") . "<br>";

// Test user class directly
require_once 'classes/user_class.php';
$user = new User();
$direct = $user->createUser($name, $email . "2", $password, $phone, $role);
echo "User class returned: " . ($direct ? "SUCCESS (ID: $direct)" : "FALSE") . "<br>";
?>