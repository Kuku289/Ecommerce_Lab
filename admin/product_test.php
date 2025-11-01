<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Product Page Diagnostic</h2>";
echo "<style>body { font-family: Arial; padding: 20px; } .success { color: green; } .error { color: red; }</style>";

// Test 1: Check files exist
echo "<h3>1. File Check</h3>";
$files = [
    '../settings/core.php',
    '../settings/db_class.php',
    '../classes/product_class.php',
    '../controllers/product_controller.php',
    '../controllers/category_controller.php',
    '../controllers/brand_controller.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<span class='success'>✅ $file exists</span><br>";
    } else {
        echo "<span class='error'>❌ $file MISSING</span><br>";
    }
}

// Test 2: Check if we can include core
echo "<h3>2. Core Functions Check</h3>";
try {
    require_once '../settings/core.php';
    echo "<span class='success'>✅ Core included successfully</span><br>";
    
    if (function_exists('check_login')) {
        echo "<span class='success'>✅ check_login() exists</span><br>";
    } else {
        echo "<span class='error'>❌ check_login() missing</span><br>";
    }
    
    if (function_exists('check_admin')) {
        echo "<span class='success'>✅ check_admin() exists</span><br>";
    } else {
        echo "<span class='error'>❌ check_admin() missing</span><br>";
    }
    
    if (function_exists('get_user_id')) {
        echo "<span class='success'>✅ get_user_id() exists</span><br>";
    } else {
        echo "<span class='error'>❌ get_user_id() missing</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error: " . $e->getMessage() . "</span><br>";
}

// Test 3: Check database
echo "<h3>3. Database Check</h3>";
try {
    require_once '../settings/db_class.php';
    $db = new db_connection();
    if ($db->db_connect()) {
        echo "<span class='success'>✅ Database connected</span><br>";
        
        // Check if products table exists
        $result = $db->db->query("SHOW TABLES LIKE 'products'");
        if ($result && $result->num_rows > 0) {
            echo "<span class='success'>✅ Products table exists</span><br>";
            
            // Show structure
            $structure = $db->db->query("DESCRIBE products");
            echo "<br><strong>Table Structure:</strong><br>";
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
            while ($row = $structure->fetch_assoc()) {
                echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Key']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<span class='error'>❌ Products table does NOT exist!</span><br>";
            echo "<strong>Run this SQL:</strong><br>";
            echo "<pre>
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_cat INT NOT NULL,
    product_brand INT NOT NULL,
    product_title VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    product_desc TEXT NOT NULL,
    product_image VARCHAR(255) NOT NULL,
    product_keywords VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_cat) REFERENCES categories(cat_id),
    FOREIGN KEY (product_brand) REFERENCES brands(brand_id)
);
            </pre>";
        }
    } else {
        echo "<span class='error'>❌ Database connection failed</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Database error: " . $e->getMessage() . "</span><br>";
}

// Test 4: Check controllers
echo "<h3>4. Controller Functions Check</h3>";
if (file_exists('../controllers/product_controller.php')) {
    require_once '../controllers/product_controller.php';
    echo "<span class='success'>✅ Product controller included</span><br>";
    
    $functions = [
        'get_all_products_ctr',
        'add_product_ctr',
        'update_product_ctr',
        'delete_product_ctr',
        'get_product_ctr'
    ];
    
    foreach ($functions as $func) {
        if (function_exists($func)) {
            echo "<span class='success'>✅ $func() exists</span><br>";
        } else {
            echo "<span class='error'>❌ $func() MISSING</span><br>";
        }
    }
    
    // Try to fetch products
    echo "<br><strong>Testing get_all_products_ctr():</strong><br>";
    try {
        $products = get_all_products_ctr();
        if (is_array($products)) {
            echo "<span class='success'>✅ Function works! Found " . count($products) . " products</span><br>";
        } else {
            echo "<span class='error'>❌ Function returned non-array: " . gettype($products) . "</span><br>";
        }
    } catch (Exception $e) {
        echo "<span class='error'>❌ Error: " . $e->getMessage() . "</span><br>";
    }
} else {
    echo "<span class='error'>❌ Product controller file missing</span><br>";
}

// Test 5: Check category and brand controllers
echo "<h3>5. Category & Brand Controllers Check</h3>";
if (file_exists('../controllers/category_controller.php')) {
    require_once '../controllers/category_controller.php';
    echo "<span class='success'>✅ Category controller exists</span><br>";
    
    if (function_exists('get_all_categories_ctr')) {
        echo "<span class='success'>✅ get_all_categories_ctr() exists</span><br>";
    } else {
        echo "<span class='error'>❌ get_all_categories_ctr() missing</span><br>";
    }
} else {
    echo "<span class='error'>❌ Category controller missing</span><br>";
}

if (file_exists('../controllers/brand_controller.php')) {
    require_once '../controllers/brand_controller.php';
    echo "<span class='success'>✅ Brand controller exists</span><br>";
    
    if (function_exists('get_all_brands_ctr')) {
        echo "<span class='success'>✅ get_all_brands_ctr() exists</span><br>";
    } else {
        echo "<span class='error'>❌ get_all_brands_ctr() missing</span><br>";
    }
} else {
    echo "<span class='error'>❌ Brand controller missing</span><br>";
}

echo "<hr>";
echo "<h3>Summary</h3>";
echo "<p>Check the errors above to see what's causing the blank page.</p>";
echo "<p><a href='product.php'>Try loading product.php again</a></p>";
?>