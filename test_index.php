<?php
require_once 'controllers/user_controller.php';
$email = "admin.test@gmail.com";
$exists = check_email_exists_ctr($email);
echo "Email '$email' exists: " . ($exists ? "YES" : "NO");
?>