<?php
require_once 'settings/core.php';
require_once 'settings/db_class.php';
require_once 'controllers/product_controller.php';
require_once 'controllers/category_controller.php';
require_once 'controllers/brand_controller.php';

$db = new db_connection();
$db->db_connect();

// Get categories for display and filters
$categories = get_all_categories_ctr();
$brands = get_all_brands_ctr();

// Get featured products (latest 6)
$all_products = view_all_products_ctr();
$featured_products = array_slice($all_products, 0, 6);

$is_logged_in = check_login();
$is_admin = $is_logged_in ? check_admin() : false;
$user_name = $is_logged_in ? get_user_name() : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BotaniQs - Authentic Wellness, Made Accessible</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4CAF50;
            --secondary: #FF9800;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        .page-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('W4.jpg') center/cover no-repeat;
            z-index: -1;
            opacity: 0.3;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            color: var(--primary) !important;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .hero-section {
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.9), rgba(255, 152, 0, 0.8));
            color: white;
            padding: 100px 0 80px;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        /* ⭐ NEW: Search Box Styles */
        .search-container {
            max-width: 700px;
            margin: 30px auto;
        }
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        .search-input {
            width: 100%;
            padding: 15px 60px 15px 20px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .search-btn {
            position: absolute;
            right: 5px;
            top: 5px;
            bottom: 5px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0 30px;
            font-weight: 600;
            cursor: pointer;
        }
        .search-btn:hover {
            background: #45a049;
        }
        .filter-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .filter-dropdown {
            background: rgba(255,255,255,0.9);
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
        }
        .value-card {
            text-align: center;
            padding: 2rem;
            transition: transform 0.3s;
        }
        .value-card:hover {
            transform: translateY(-10px);
        }
        .value-card i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        .category-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s;
            height: 100%;
        }
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .category-card img {
            width: 100%;
            max-width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        /* ⭐ NEW: Featured Products */
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        .product-image-container {
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .no-image {
            color: #ccc;
            font-size: 60px;
        }
        .product-body {
            padding: 20px;
        }
        .product-title {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            min-height: 40px;
        }
        .product-price {
            font-size: 20px;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 15px;
        }
        .wellness-tip {
            background: white;
            border-left: 4px solid var(--primary);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        footer {
            background: #2d3748;
            color: white;
            padding: 3rem 0;
        }
        section {
            padding: 60px 0;
        }
        .section-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #2d3748;
        }
        .stats-box {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
        }
        .stats-number {
            font-size: 3rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="page-background"></div>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-leaf"></i> BotaniQs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="view/all_product.php">All Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="#categories">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="#wellness">Wellness</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    
                    <?php if (!$is_logged_in): ?>
                        <li class="nav-item"><a class="nav-link" href="login/login.php">Login</a></li>
                        <li class="nav-item">
                            <a class="btn btn-success btn-sm ms-2" href="login/register.php">Register</a>
                        </li>
                    <?php elseif ($is_admin): ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-success btn-sm ms-2" href="admin/category.php">Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-info btn-sm ms-2" href="admin/brand.php">Brands</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-warning btn-sm ms-2" href="admin/product.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-danger btn-sm ms-2" href="login/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view/cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-danger btn-sm ms-2" href="login/logout.php">Logout</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <section id="home" class="hero-section">
        <div class="container">
            <h1>Authentic Wellness, Made Accessible</h1>
            <p>Premium organic seeds, essential oils, and herbs for Ghana's wellness journey</p>
            
            <!-- ⭐ NEW: Search Box -->
            <div class="search-container">
                <form action="view/product_search_result.php" method="GET">
                    <div class="search-box">
                        <input type="text" 
                               class="search-input" 
                               name="q" 
                               placeholder="Search for products (e.g., 'tea tree oil', 'organic seeds')..." 
                               required>
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>

                <!-- ⭐ NEW: Filter Buttons -->
                <div class="filter-buttons">
                    <div class="dropdown">
                        <button class="filter-dropdown dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-tags"></i> Categories
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a class="dropdown-item" href="view/all_product.php?category=<?php echo $category['cat_id']; ?>">
                                        <?php echo htmlspecialchars($category['cat_name']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="filter-dropdown dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-certificate"></i> Brands
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach ($brands as $brand): ?>
                                <li>
                                    <a class="dropdown-item" href="view/all_product.php?brand=<?php echo $brand['brand_id']; ?>">
                                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-center mt-4">
                <a href="view/all_product.php" class="btn btn-light btn-lg">Explore Products</a>
                <?php if (!$is_logged_in): ?>
                    <a href="login/register.php" class="btn btn-outline-light btn-lg">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5">Why Choose BotaniQs?</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="value-card">
                        <i class="fas fa-check-circle"></i>
                        <h4>100% Authentic</h4>
                        <p>FDA-approved, quality-verified products</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="value-card">
                        <i class="fas fa-tags"></i>
                        <h4>Affordable</h4>
                        <p>Direct sourcing reduces costs by 40%</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="value-card">
                        <i class="fas fa-leaf"></i>
                        <h4>Local Support</h4>
                        <p>Supporting Ghanaian farmers</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="value-card">
                        <i class="fas fa-book-open"></i>
                        <h4>Education</h4>
                        <p>Learn proper usage and benefits</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ⭐ NEW: Featured Products Section -->
    <?php if (!empty($featured_products)): ?>
    <section>
        <div class="container">
            <h2 class="section-title text-center">Featured Products</h2>
            <p class="text-center text-muted mb-5">Check out our latest additions</p>
            <div class="row g-4">
                <?php foreach ($featured_products as $product): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="product-card">
                            <div class="product-image-container">
                                <?php if (!empty($product['product_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['product_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                                         class="product-image"
                                         onerror="this.parentElement.innerHTML='<i class=\'fas fa-image no-image\'></i>'">
                                <?php else: ?>
                                    <i class="fas fa-image no-image"></i>
                                <?php endif; ?>
                            </div>
                            <div class="product-body">
                                <h5 class="product-title">
                                    <?php echo htmlspecialchars($product['product_title']); ?>
                                </h5>
                                <div class="product-price">
                                    GH₵<?php echo number_format($product['product_price'], 2); ?>
                                </div>
                                <a href="view/single_product.php?id=<?php echo $product['product_id']; ?>" 
                                   class="btn btn-success w-100">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="view/all_product.php" class="btn btn-lg btn-success">
                    <i class="fas fa-shopping-bag"></i> View All Products
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section id="categories" class="bg-light">
        <div class="container">
            <h2 class="section-title text-center">Shop by Category</h2>
            <p class="text-center text-muted mb-5">Curated collections for your wellness journey</p>
            
            <div class="row g-4">
                <?php if (count($categories) > 0): ?>
                    <?php foreach (array_slice($categories, 0, 6) as $category): ?>
                        <div class="col-md-4">
                            <div class="category-card">
                                <i class="fas fa-spa" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;"></i>
                                <h3><?php echo htmlspecialchars($category['cat_name']); ?></h3>
                                <p class="text-muted">Explore our premium collection</p>
                                <a href="view/all_product.php?category=<?php echo $category['cat_id']; ?>" class="btn btn-success">View Products</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-4">
                        <div class="category-card">
                            <img src="S2.jpg" alt="Organic Seeds">
                            <h3>Organic Seeds</h3>
                            <p class="text-muted">Chia, flax, pumpkin and more</p>
                            <a href="login/register.php" class="btn btn-success">Shop Now</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="category-card">
                            <img src="B2.jpg" alt="Essential Oils">
                            <h3>Essential Oils</h3>
                            <p class="text-muted">Pure therapeutic oils</p>
                            <a href="login/register.php" class="btn btn-success">Shop Now</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="category-card">
                            <img src="H1.jpg" alt="Medicinal Herbs">
                            <h3>Medicinal Herbs</h3>
                            <p class="text-muted">Traditional remedies</p>
                            <a href="login/register.php" class="btn btn-success">Shop Now</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="wellness">
        <div class="container">
            <h2 class="section-title text-center mb-5">Wellness Tips and Education</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="wellness-tip">
                        <h5><i class="fas fa-mug-hot text-success"></i> Boost Immunity Naturally</h5>
                        <p>Incorporate ginger, turmeric, and moringa to strengthen your immune system.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wellness-tip">
                        <h5><i class="fas fa-spa text-warning"></i> Stress Relief with Aromatherapy</h5>
                        <p>Use lavender or chamomile oils for relaxation and better sleep.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wellness-tip">
                        <h5><i class="fas fa-heart text-danger"></i> Heart-Healthy Seeds</h5>
                        <p>Add chia and flax seeds for omega-3 fatty acids that support heart health.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wellness-tip">
                        <h5><i class="fas fa-leaf text-success"></i> Support Local Wellness</h5>
                        <p>Choose locally-sourced shea butter and moringa from Ghanaian farmers.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="section-title">Our Mission</h2>
                    <p class="lead">BotaniQs bridges Ghana's wellness access gap by providing authentic, affordable, and well-documented natural products.</p>
                    <ul class="list-unstyled mt-4">
                        <li class="mb-2"><i class="fas fa-check-circle text-success"></i> Direct sourcing from verified suppliers</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success"></i> Supporting local Ghanaian producers</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success"></i> Educational resources for every product</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success"></i> Committed to sustainability</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stats-box">
                                <div class="stats-number">$50M+</div>
                                <p>Annual wellness gap</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-box">
                                <div class="stats-number">40%</div>
                                <p>Cost reduction</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stats-box">
                                <div class="stats-number">30%</div>
                                <p>Local products supporting farmers</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h4><i class="fas fa-leaf"></i> BotaniQs</h4>
                    <p>Authentic Wellness, Made Accessible</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="text-white-50">Home</a></li>
                        <li><a href="view/all_product.php" class="text-white-50">All Products</a></li>
                        <li><a href="#categories" class="text-white-50">Categories</a></li>
                        <li><a href="login/register.php" class="text-white-50">Register</a></li>
                        <li><a href="login/login.php" class="text-white-50">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <p><i class="fas fa-envelope"></i> botaniqs@gmail.com</p>
                    <p><i class="fas fa-phone"></i> +233 20 409 3497</p>
                    <p><i class="fas fa-phone"></i> +233 59 573 4449</p>
                </div>
            </div>
            <div class="text-center mt-4 pt-4 border-top border-secondary">
                <p>&copy; 2025 BotaniQs Enhanced. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>