<?php
session_start();
require_once(dirname(__FILE__) . '/../controllers/product_controller.php');

// Fetch all products
$products = get_all_products_ctr();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #27ae60;
        }
    </style>
</head>
<body>
    <!-- Navigation (same as index.php) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-store"></i> E-Commerce Platform
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">
            <i class="fas fa-box"></i> Our Products
        </h2>

        <div class="row">
            <?php if ($products && count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card product-card">
                            <?php if ($product['product_image']): ?>
                                <img src="../<?php echo htmlspecialchars($product['product_image']); ?>" 
                                     class="card-img-top product-image" 
                                     alt="<?php echo htmlspecialchars($product['product_title']); ?>">
                            <?php else: ?>
                                <div class="card-img-top product-image bg-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-white"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <span class="badge bg-primary mb-2">
                                    <?php echo htmlspecialchars($product['cat_name']); ?>
                                </span>
                                <span class="badge bg-secondary mb-2">
                                    <?php echo htmlspecialchars($product['brand_name']); ?>
                                </span>
                                
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($product['product_title']); ?>
                                </h5>
                                
                                <p class="card-text">
                                    <?php echo htmlspecialchars(substr($product['product_desc'], 0, 100)) . '...'; ?>
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price">
                                        $<?php echo number_format($product['product_price'], 2); ?>
                                    </span>
                                    <button class="btn btn-primary">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No products available at the moment.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>