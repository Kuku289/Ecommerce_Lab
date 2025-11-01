<?php
// Enable error reporting to see the exact error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Step 1: Starting test...<br>";

// Test 1: Check if core.php exists and works
echo "Step 2: Loading core.php...<br>";
require_once '../settings/core.php';
echo "Step 3: core.php loaded successfully!<br>";

// Test 2: Check login
echo "Step 4: Checking login...<br>";
if (check_login()) {
    echo "Step 5: User is logged in!<br>";
} else {
    echo "Step 5: User is NOT logged in!<br>";
    exit();
}

// Test 3: Check admin
echo "Step 6: Checking admin status...<br>";
if (check_admin()) {
    echo "Step 7: User is admin!<br>";
} else {
    echo "Step 7: User is NOT admin!<br>";
    exit();
}

// Test 4: Load database class
echo "Step 8: Loading database class...<br>";
require_once '../settings/db_class.php';
echo "Step 9: Database class loaded!<br>";

// Test 5: Test database connection
echo "Step 10: Testing database connection...<br>";
$db = new db_connection();
if ($db->db_connect()) {
    echo "Step 11: Database connected successfully!<br>";
} else {
    echo "Step 11: Database connection FAILED!<br>";
}

// Test 6: Try loading product controller
echo "Step 12: Loading product controller...<br>";
try {
    require_once '../controllers/product_controller.php';
    echo "Step 13: Product controller loaded!<br>";
} catch (Exception $e) {
    echo "Step 13: Product controller FAILED: " . $e->getMessage() . "<br>";
}

// Test 7: Try loading category controller
echo "Step 14: Loading category controller...<br>";
try {
    require_once '../controllers/category_controller.php';
    echo "Step 15: Category controller loaded!<br>";
} catch (Exception $e) {
    echo "Step 15: Category controller FAILED: " . $e->getMessage() . "<br>";
}

// Test 8: Try loading brand controller
echo "Step 16: Loading brand controller...<br>";
try {
    require_once '../controllers/brand_controller.php';
    echo "Step 17: Brand controller loaded!<br>";
} catch (Exception $e) {
    echo "Step 17: Brand controller FAILED: " . $e->getMessage() . "<br>";
}

// Test 9: Try getting categories
echo "Step 18: Trying to get categories...<br>";
try {
    $categories = get_all_categories_ctr();
    echo "Step 19: Categories retrieved: " . count($categories) . " categories found!<br>";
} catch (Exception $e) {
    echo "Step 19: Get categories FAILED: " . $e->getMessage() . "<br>";
}

// Test 10: Try getting brands
echo "Step 20: Trying to get brands...<br>";
try {
    $brands = get_all_brands_ctr();
    echo "Step 21: Brands retrieved: " . count($brands) . " brands found!<br>";
} catch (Exception $e) {
    echo "Step 21: Get brands FAILED: " . $e->getMessage() . "<br>";
}

echo "<br><br><strong>All tests completed! If you see this, we can identify the problem.</strong>";
?>
```

**Save this file as `admin/product_test.php` and access it in your browser:**
```
http://169.239.251.102:442/admin/product_test.php