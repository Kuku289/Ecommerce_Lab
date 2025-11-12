<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

require_once('../controllers/cart_controller.php');

$customer_id = $_SESSION['user_id'];
$cart_items = get_cart_items_ctr($customer_id);
$cart_total = get_cart_total_ctr($customer_id);
$user_name = $_SESSION['user_name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - BotaniQs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4CAF50;
            --secondary: #FF9800;
        }
        body {
            background-color: #f8f9fa;
            padding-top: 76px;
        }
        .navbar {
            background: rgba(76, 175, 80, 0.95);
            backdrop-filter: blur(10px);
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .cart-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 15px;
        }
        .cart-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-input {
            width: 80px;
            text-align: center;
        }
        .cart-summary {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 90px;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
        }
        .empty-cart i {
            font-size: 100px;
            color: #ccc;
            margin-bottom: 20px;
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
                            <i class="fas fa-shopping-bag"></i> Shop
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../login/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Shopping Cart</h2>
        
        <?php if (empty($cart_items)): ?>
            <!-- Empty Cart -->
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p class="text-muted">Add some products to get started!</p>
                <a href="all_product.php" class="btn btn-success btn-lg mt-3">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="row align-items-center">
                                <!-- Product Image -->
                                <div class="col-md-2">
                                    <?php if (!empty($item['product_image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($item['product_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                             class="product-image">
                                    <?php else: ?>
                                        <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Product Info -->
                                <div class="col-md-4">
                                    <h5><?php echo htmlspecialchars($item['product_title']); ?></h5>
                                    <p class="text-muted mb-0">
                                        <small>
                                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($item['cat_name']); ?> | 
                                            <i class="fas fa-certificate"></i> <?php echo htmlspecialchars($item['brand_name']); ?>
                                        </small>
                                    </p>
                                </div>
                                
                                <!-- Price -->
                                <div class="col-md-2 text-center">
                                    <p class="mb-0"><strong>GH₵<?php echo number_format($item['product_price'], 2); ?></strong></p>
                                    <small class="text-muted">per unit</small>
                                </div>
                                
                                <!-- Quantity -->
                                <div class="col-md-2 text-center">
                                    <input type="number" 
                                           class="form-control quantity-input" 
                                           value="<?php echo $item['qty']; ?>" 
                                           min="1" 
                                           data-product-id="<?php echo $item['p_id']; ?>">
                                </div>
                                
                                <!-- Subtotal & Remove -->
                                <div class="col-md-2 text-end">
                                    <p class="mb-2"><strong>GH₵<?php echo number_format($item['subtotal'], 2); ?></strong></p>
                                    <button class="btn btn-sm btn-danger remove-btn" 
                                            data-product-id="<?php echo $item['p_id']; ?>">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <a href="all_product.php" class="btn btn-outline-success">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                        <button id="empty-cart-btn" class="btn btn-outline-danger">
                            <i class="fas fa-trash"></i> Empty Cart
                        </button>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4 class="mb-4">Order Summary</h4>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Items:</span>
                            <strong><?php echo count($cart_items); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <strong>GH₵<?php echo number_format($cart_total, 2); ?></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total:</h5>
                            <h5 class="text-success">GH₵<?php echo number_format($cart_total, 2); ?></h5>
                        </div>
                        <a href="checkout.php" class="btn btn-success w-100 btn-lg">
                            <i class="fas fa-lock"></i> Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/cart.js"></script>
</body>
</html>