<?php
require_once 'settings/db_class.php';

$db = new db_connection();
$db->db_connect();

$name = "Direct Test User";
$email = "directtest" . time() . "@example.com";
$password = password_hash("Test123", PASSWORD_DEFAULT);
$phone = "1234567890";
$role = 1; // Test with admin role
$country = "Ghana";
$city = "Accra";

echo "Attempting direct insertion...<br>";
echo "Email: $email<br>";
echo "Role: $role (type: " . gettype($role) . ")<br><br>";

$sql = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, user_role, customer_country, customer_city) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $db->db->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $db->db->error);
}

$stmt->bind_param("ssssiss", $name, $email, $password, $phone, $role, $country, $city);

if ($stmt->execute()) {
    $new_id = mysqli_insert_id($db->db);
    echo "SUCCESS!<br>";
    echo "New ID: $new_id<br><br>";
    
    // Verify what was inserted
    $check = $db->db->query("SELECT customer_id, customer_name, user_role FROM customer WHERE customer_email = '$email'");
    $row = $check->fetch_assoc();
    echo "Verification:<br>";
    echo "ID: " . $row['customer_id'] . "<br>";
    echo "Name: " . $row['customer_name'] . "<br>";
    echo "Role: " . $row['user_role'] . "<br>";
} else {
    echo "FAILED: " . $stmt->error;
}
?>