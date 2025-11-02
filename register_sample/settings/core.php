<?php
require_once 'settings/core.php';

echo "<h2>Session Management Test</h2>";

// Test 1: Check if user is logged in
echo "<h3>Test 1: Login Status</h3>";
if (check_login()) {
    echo "✓ User is logged in<br>";
    echo "User ID: " . get_user_id() . "<br>";
    echo "User Name: " . get_user_name() . "<br>";
    echo "User Role: " . get_user_role() . "<br>";
} else {
    echo "✗ User is not logged in<br>";
}

// Test 2: Check admin privileges
echo "<h3>Test 2: Admin Status</h3>";
if (check_admin()) {
    echo "✓ User has admin privileges<br>";
} else {
    echo "✗ User does not have admin privileges<br>";
}

// Display session data for debugging
echo "<h3>Session Data</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Links for testing
echo "<h3>Test Links</h3>";
echo "<a href='login/login.php'>Go to Login</a><br>";
echo "<a href='login/logout.php'>Logout</a><br>";
?>