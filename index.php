<?php
require_once 'settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Commerce Platform - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #D19C97 0%, #D19C97 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .menu-tray {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 10px 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .menu-tray:hover {
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }
        
        .menu-tray .btn {
            margin-left: 8px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .menu-tray .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .main-content {
            padding-top: 150px;
            color: white;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            margin: 20px auto;
            max-width: 600px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-card h1 {
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            font-size: 0.9rem;
            margin-top: 15px;
        }
        
        .admin-badge {
            background: rgba(40, 167, 69, 0.3);
            border: 1px solid rgba(40, 167, 69, 0.5);
        }
        
        .customer-badge {
            background: rgba(0, 123, 255, 0.3);
            border: 1px solid rgba(0, 123, 255, 0.5);
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body>
    <!-- Navigation Menu -->
    <div class="menu-tray">
        <span class="me-2"><i class="fas fa-bars"></i> Menu:</span>
        
        <?php if (!check_login()): ?>
            <!-- Not logged in -->
            <a href="login/register.php" class="btn btn-sm btn-primary">
                <i class="fas fa-user-plus"></i> Register
            </a>
            <a href="login/login.php" class="btn btn-sm btn-outline-light">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        
        <?php elseif (check_admin()): ?>
            <!-- Admin user -->
            <a href="admin/category.php" class="btn btn-sm btn-success">
                <i class="fas fa-tags"></i> Categories
            </a>
            <a href="login/logout.php" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        
        <?php else: ?>
            <!-- Regular customer -->
            <a href="login/logout.php" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="container main-content">
        <div class="text-center">
            <div class="welcome-card">
                <?php if (!check_login()): ?>
                    <h1><i class="fas fa-store"></i> Welcome to Our Platform</h1>
                    <p class="lead">Your gateway to amazing products and services</p>
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-shopping-cart"></i></div>
                            <h5>Shop</h5>
                            <p>Browse our catalog</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-user-shield"></i></div>
                            <h5>Secure</h5>
                            <p>Safe transactions</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-shipping-fast"></i></div>
                            <h5>Fast Delivery</h5>
                            <p>Quick shipping</p>
                        </div>
                    </div>
                    <p class="mt-4 text-light">
                        <i class="fas fa-arrow-up"></i> Use the menu above to get started
                    </p>
                
                <?php elseif (check_admin()): ?>
                    <h1><i class="fas fa-crown"></i> Welcome, <?php echo get_user_name(); ?>!</h1>
                    <div class="status-badge admin-badge">
                        <i class="fas fa-shield-alt"></i> Administrator
                    </div>
                    <p class="lead mt-3">Manage your platform with admin tools</p>
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-tags"></i></div>
                            <h5>Categories</h5>
                            <p>Manage product categories</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-users"></i></div>
                            <h5>Users</h5>
                            <p>View customer accounts</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-chart-bar"></i></div>
                            <h5>Analytics</h5>
                            <p>View platform statistics</p>
                        </div>
                    </div>
                
                <?php else: ?>
                    <h1><i class="fas fa-user"></i> Welcome, <?php echo get_user_name(); ?>!</h1>
                    <div class="status-badge customer-badge">
                        <i class="fas fa-user"></i> Customer
                    </div>
                    <p class="lead mt-3">Ready to explore our products?</p>
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-heart"></i></div>
                            <h5>Favorites</h5>
                            <p>Your saved items</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-shopping-bag"></i></div>
                            <h5>Orders</h5>
                            <p>Track your purchases</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-user-circle"></i></div>
                            <h5>Profile</h5>
                            <p>Update your info</p>
                        </div>
                    </div>
                
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>