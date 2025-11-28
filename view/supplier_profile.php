<?php
session_start();

require_once('../controllers/supplier_controller.php');

// Get supplier ID
if (!isset($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$supplier_id = intval($_GET['id']);
$supplier = get_supplier_ctr($supplier_id);

if (!$supplier) {
    header('Location: ../index.php');
    exit();
}

$certifications = get_supplier_certifications_ctr($supplier_id);
$products = get_supplier_products_ctr($supplier_id);

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($supplier['supplier_name']); ?> - BotaniQs</title>
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
        .supplier-header {
            background: linear-gradient(135deg, var(--primary), #66BB6A);
            color: white;
            padding: 60px 0 40px;
        }
        .supplier-logo-lg {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 15px;
            border: 5px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .certification-badge {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
        }
        .badge-fda {
            background: #2196F3;
            color: white;
        }
        .badge-organic {
            background: #4CAF50;
            color: white;
        }
        .badge-fairtrade {
            background: #FF9800;
            color: white;
        }
        .badge-local {
            background: #9C27B0;
            color: white;
        }
        .badge-verified {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
        }
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .section-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_product.php">All Products</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Supplier Header -->
    <div class="supplier-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <?php if (!empty($supplier['supplier_logo'])): ?>
                        <img src="../<?php echo htmlspecialchars($supplier['supplier_logo']); ?>" 
                             alt="Logo" class="supplier-logo-lg">
                    <?php else: ?>
                        <div class="supplier-logo-lg bg-white d-flex align-items-center justify-content-center">
                            <i class="fas fa-building text-muted" style="font-size: 60px;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-10">
                    <h1><?php echo htmlspecialchars($supplier['supplier_name']); ?></h1>
                    <p class="lead mb-3"><?php echo htmlspecialchars($supplier['supplier_description'] ?? 'Trusted supplier of quality wellness products'); ?></p>
                    
                    <?php if ($supplier['verification_status'] == 'Verified'): ?>
                        <span class="badge-verified">
                            <i class="fas fa-check-circle"></i> Verified Supplier
                        </span>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <?php if ($supplier['fda_approved']): ?>
                            <span class="certification-badge badge-fda">
                                <i class="fas fa-check"></i> FDA Approved
                            </span>
                        <?php endif; ?>
                        <?php if ($supplier['organic_certified']): ?>
                            <span class="certification-badge badge-organic">
                                <i class="fas fa-leaf"></i> Organic Certified
                            </span>
                        <?php endif; ?>
                        <?php if ($supplier['fair_trade_certified']): ?>
                            <span class="certification-badge badge-fairtrade">
                                <i class="fas fa-handshake"></i> Fair Trade
                            </span>
                        <?php endif; ?>
                        <?php if ($supplier['local_farmer']): ?>
                            <span class="certification-badge badge-local">
                                <i class="fas fa-map-marker-alt"></i> Local Farmer
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <!-- Supplier Information -->
        <div class="row">
            <div class="col-md-8">
                <!-- About -->
                <div class="section-card">
                    <h3><i class="fas fa-info-circle"></i> About</h3>
                    <hr>
                    <p><?php echo nl2br(htmlspecialchars($supplier['supplier_description'] ?? 'No description available.')); ?></p>
                </div>

                <!-- Products -->
                <div class="section-card">
                    <h3><i class="fas fa-box"></i> Products (<?php echo count($products); ?>)</h3>
                    <hr>
                    
                    <?php if (empty($products)): ?>
                        <p class="text-muted">No products from this supplier yet.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-6">
                                    <div class="product-card">
                                        <?php if (!empty($product['product_image'])): ?>
                                            <img src="../<?php echo htmlspecialchars($product['product_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                                                 class="product-image">
                                        <?php else: ?>
                                            <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                                <i class="fas fa-image text-muted" style="font-size: 60px;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="p-3">
                                            <h6><?php echo htmlspecialchars($product['product_title']); ?></h6>
                                            <p class="text-success mb-2">
                                                <strong>GH₵<?php echo number_format($product['product_price'], 2); ?></strong>
                                            </p>
                                            <a href="single_product.php?id=<?php echo $product['product_id']; ?>" 
                                               class="btn btn-sm btn-success w-100">
                                                <i class="fas fa-eye"></i> View Product
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Contact Information -->
                <div class="section-card">
                    <h5><i class="fas fa-address-card"></i> Contact Information</h5>
                    <hr>
                    <p>
                        <i class="fas fa-envelope text-primary"></i> 
                        <strong>Email:</strong><br>
                        <?php echo htmlspecialchars($supplier['supplier_email']); ?>
                    </p>
                    <?php if ($supplier['supplier_phone']): ?>
                        <p>
                            <i class="fas fa-phone text-primary"></i> 
                            <strong>Phone:</strong><br>
                            <?php echo htmlspecialchars($supplier['supplier_phone']); ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($supplier['supplier_address']): ?>
                        <p>
                            <i class="fas fa-map-marker-alt text-primary"></i> 
                            <strong>Address:</strong><br>
                            <?php echo nl2br(htmlspecialchars($supplier['supplier_address'])); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Certifications -->
                <?php if (!empty($certifications)): ?>
                    <div class="section-card">
                        <h5><i class="fas fa-certificate"></i> Certifications</h5>
                        <hr>
                        <?php foreach ($certifications as $cert): ?>
                            <div class="mb-3 p-2 border-start border-4 border-success">
                                <strong><?php echo htmlspecialchars($cert['cert_name']); ?></strong><br>
                                <small class="text-muted">
                                    Type: <?php echo htmlspecialchars($cert['cert_type']); ?><br>
                                    <?php if ($cert['cert_number']): ?>
                                        Number: <?php echo htmlspecialchars($cert['cert_number']); ?><br>
                                    <?php endif; ?>
                                    <?php if ($cert['expiry_date']): ?>
                                        Expires: <?php echo date('M d, Y', strtotime($cert['expiry_date'])); ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Statistics -->
                <div class="section-card">
                    <h5><i class="fas fa-chart-bar"></i> Statistics</h5>
                    <hr>
                    <p>
                        <strong>Total Products:</strong> <?php echo $supplier['product_count']; ?><br>
                        <strong>Member Since:</strong> <?php echo date('M Y', strtotime($supplier['registration_date'])); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>