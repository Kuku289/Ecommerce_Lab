<?php
// Start session
session_start();

// Include necessary files
require_once('../controllers/product_controller.php');
include('../includes/chatbot_widget.php');

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: all_product.php');
    exit();
}

// Get product details
$product = view_single_product_ctr($product_id);

if (!$product) {
    $_SESSION['error'] = 'Product not found';
    header('Location: all_product.php');
    exit();
}

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - BotaniQs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4CAF50;
            --secondary: #FF9800;
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: rgba(76, 175, 80, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .product-container {
            margin-top: 80px;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .product-image-main {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            background: #f8f9fa;
        }
        .no-image-large {
            width: 100%;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
        }
        .no-image-large i {
            font-size: 120px;
            color: #ccc;
        }
        .product-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .product-id {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .product-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .meta-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .meta-category {
            background: #e3f2fd;
            color: #1976d2;
        }
        .meta-brand {
            background: #fff3e0;
            color: #f57c00;
        }
        .product-price {
            font-size: 48px;
            font-weight: bold;
            color: var(--primary);
            margin: 20px 0;
        }
        .product-description {
            font-size: 16px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 20px;
        }
        .product-keywords {
            margin-top: 20px;
        }
        .keyword-tag {
            display: inline-block;
            background: #f0f0f0;
            padding: 5px 12px;
            border-radius: 15px;
            margin: 5px;
            font-size: 13px;
            color: #666;
        }
        .action-buttons {
            margin-top: 30px;
        }
        .btn-add-cart {
            background: var(--primary);
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-add-cart:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }
        .breadcrumb {
            background: transparent;
            padding: 20px 0;
        }
        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }
        .info-section h5 {
            color: var(--primary);
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-leaf"></i> BotaniQs
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_product.php">
                            <i class="fas fa-shopping-bag"></i> All Products
                        </a>
                    </li>
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                <i class="fas fa-shopping-cart"></i> Cart
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/register.php">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/login.php">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="all_product.php">All Products</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['product_title']); ?></li>
            </ol>
        </nav>

        <div class="product-container">
            <div class="row">
                <!-- Product Image -->
                <div class="col-md-6">
                    <?php if (!empty($product['product_image'])): ?>
                        <img src="<?php echo htmlspecialchars($product['product_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                             class="product-image-main"
                             onerror="this.parentElement.innerHTML='<div class=\'no-image-large\'><i class=\'fas fa-image\'></i></div>'">
                    <?php else: ?>
                        <div class="no-image-large">
                            <i class="fas fa-image"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Product Details -->
                <div class="col-md-6">
                    <div class="product-id">
                        <i class="fas fa-barcode"></i> Product ID: #<?php echo $product['product_id']; ?>
                    </div>
                    
                    <h1 class="product-title">
                        <?php echo htmlspecialchars($product['product_title']); ?>
                    </h1>

                    <div class="product-meta">
                        <span class="meta-badge meta-category">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['cat_name']); ?>
                        </span>
                        <span class="meta-badge meta-brand">
                            <i class="fas fa-certificate"></i> <?php echo htmlspecialchars($product['brand_name']); ?>
                        </span>
                    </div>

                    <div class="product-price">
                        GH₵<?php echo number_format($product['product_price'], 2); ?>
                    </div>

                    <div class="product-description">
                        <h5><i class="fas fa-info-circle"></i> Product Description</h5>
                        <p><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                    </div>

                    <?php if (!empty($product['product_keywords'])): ?>
                        <div class="product-keywords">
                            <h6><i class="fas fa-tags"></i> Keywords</h6>
                            <?php 
                            $keywords = explode(',', $product['product_keywords']);
                            foreach ($keywords as $keyword): 
                            ?>
                                <span class="keyword-tag"><?php echo trim(htmlspecialchars($keyword)); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="action-buttons">
                        <?php if ($is_logged_in): ?>
                            <button class="btn-add-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        <?php else: ?>
                            <a href="../login/login.php" class="btn-add-cart" style="text-decoration: none;">
                                <i class="fas fa-sign-in-alt"></i> Login to Purchase
                            </a>
                        <?php endif; ?>
                        
                        <a href="all_product.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>

                    <!-- Additional Info -->
                    <div class="info-section">
                        <h5><i class="fas fa-shield-alt"></i> Why Choose BotaniQs?</h5>
                        <ul>
                            <li>100% Natural & Organic Products</li>
                            <li>Quality Guaranteed</li>
                            <li>Fast & Secure Delivery</li>
                            <li>Customer Support 24/7</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(productId) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            fetch('../actions/add_to_cart_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ Product added to cart successfully!');
                    // Optionally redirect to cart
                    // window.location.href = 'cart.php';
                } else {
                    alert('✗ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding to cart');
            });
        }
    </script>
</body>
</html>