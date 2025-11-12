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

// Redirect if cart is empty
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BotaniQs</title>
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
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .checkout-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 15px;
        }
        .checkout-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .order-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .order-item:last-child {
            border-bottom: none;
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
            <div class="ms-auto">
                <span class="navbar-text text-white">
                    Welcome, <?php echo htmlspecialchars($user_name); ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="checkout-container">
        <h2 class="mb-4"><i class="fas fa-lock"></i> Checkout</h2>
        
        <!-- Order Summary -->
        <div class="checkout-card">
            <h4 class="mb-4">Order Summary</h4>
            
            <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1"><?php echo htmlspecialchars($item['product_title']); ?></h6>
                            <small class="text-muted">
                                <?php echo htmlspecialchars($item['cat_name']); ?> | 
                                <?php echo htmlspecialchars($item['brand_name']); ?>
                            </small>
                        </div>
                        <div class="col-md-2 text-center">
                            <span>Qty: <?php echo $item['qty']; ?></span>
                        </div>
                        <div class="col-md-2 text-center">
                            <span>GH₵<?php echo number_format($item['product_price'], 2); ?></span>
                        </div>
                        <div class="col-md-2 text-end">
                            <strong>GH₵<?php echo number_format($item['subtotal'], 2); ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="mt-4 pt-3 border-top">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Total Amount:</h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <h4 class="text-success">GH₵<?php echo number_format($cart_total, 2); ?></h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Section -->
        <div class="checkout-card">
            <h4 class="mb-3"><i class="fas fa-credit-card"></i> Payment</h4>
            <p class="text-muted">This is a simulated payment for demonstration purposes.</p>
            
            <div class="d-grid gap-2">
                <button id="simulate-payment-btn" class="btn btn-success btn-lg">
                    <i class="fas fa-lock"></i> Simulate Payment
                </button>
                <a href="cart.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Cart
                </a>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-credit-card"></i> Simulated Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-money-bill-wave text-success" style="font-size: 60px;"></i>
                    <h4 class="mt-3">Total Amount: GH₵<?php echo number_format($cart_total, 2); ?></h4>
                    <p class="text-muted mt-3">
                        This is a simulated payment process.<br>
                        Click "Yes, I've Paid" to complete your order.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancel-payment-btn">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirm-payment-btn">
                        <i class="fas fa-check"></i> Yes, I've Paid
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    <h3 class="mt-4">Payment Successful!</h3>
                    <p class="text-muted">Your order has been placed successfully.</p>
                    <div class="alert alert-info mt-4">
                        <strong>Invoice No:</strong> <span id="success-invoice"></span><br>
                        <strong>Amount Paid:</strong> <span id="success-amount"></span>
                    </div>
                    <p class="text-muted">Redirecting to home page...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/checkout.js"></script>
</body>
</html>