<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is working!<br>";

// Test if file exists
if (file_exists('../settings/core.php')) {
    echo "core.php file exists!<br>";
} else {
    echo "core.php file NOT found!<br>";
}

// Try to include it
try {
    require_once '../settings/core.php';
    echo "core.php loaded successfully!<br>";
} catch (Error $e) {
    echo "Error loading core.php: " . $e->getMessage() . "<br>";
}
?>