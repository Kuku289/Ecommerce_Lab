<?php
// Start session
session_start();

// Include necessary files
require_once('../controllers/product_controller.php');
require_once('../controllers/category_controller.php');
require_once('../controllers/brand_controller.php');

// Get search query
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter_category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$filter_brand = isset($_GET['brand']) ? intval($_GET['brand']) : 0;

// Get all categories and brands for filters
$categories = get_all_categories_ctr();
$brands = get_all_brands_ctr();

// Perform search with filters
if (!empty($search_query)) {
    // Use composite search for better results
    $filters = [
        'query' => $search_query,
        'category' => $filter_category,
        'brand' => $filter_brand
    ];
    $products = composite_search_ctr($filters);
} else {
    // If no search query, redirect to all products
    header('Location: all_product.php');
    exit();
}

// Pagination settings
$products_per_page = 10;
$total_products = count($products);
$total_pages = ceil($total_products / $products_per_page);
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $products_per_page;

// Get products for current page
$paginated_products = array_slice($products, $offset, $products_per_page);

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results: <?php echo htmlspecialchars($search_query); ?> - BotaniQs</title>
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
        .search-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0 30px 0;
            margin-top: 56px;
            text-align: center;
        }
        .search-query {
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 10px;
        }
        .breadcrumb {
            background: white;
            padding: 15px 0;
            margin-bottom: 0;
        }
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            position: sticky;
            top: 76px;
        }
        .search-box {
            margin-bottom: 20px;
        }
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        .product-image-container {
            width: 100%;
            height: 250px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .no-image {
            color: #999;
            font-size: 48px;
        }
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
        .product-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .product-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            min-height: 50px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .product-meta {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 15px;
            margin-top: auto;
        }
        .btn-add-cart, .btn-view {
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-add-cart {
            background: var(--primary);
            color: white;
        }
        .btn-add-cart:hover {
            background: #45a049;
            color: white;
        }
        .btn-view {
            background: #3498db;
            color: white;
        }
        .btn-view:hover {
            background: #2980b9;
            color: white;
        }
        .pagination {
            margin-top: 30px;
        }
        .filter-btn {
            margin-bottom: 10px;
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
        }
        .no-results i {
            font-size: 80px;
            color: #ccc;
            margin-bottom: 20px;
        }
        mark {
            background-color: #fff59d;
            padding: 2px 4px;
            border-radius: 3px;
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

    <!-- Search Header -->
    <div class="search-header">
        <div class="container">
            <h1><i class="fas fa-search"></i> Search Results</h1>
            <div class="search-query">
                Showing results for: <strong><?php echo htmlspecialchars($search_query); ?></strong>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="all_product.php">All Products</a></li>
                    <li class="breadcrumb-item active">Search Results</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
                <div class="filter-section">
                    <h5><i class="fas fa-filter"></i> Refine Search</h5>
                    <hr>
                    
                    <!-- Search Box -->
                    <div class="search-box">
                        <form action="product_search_result.php" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="q" 
                                       placeholder="Search products..." 
                                       value="<?php echo htmlspecialchars($search_query); ?>">
                                <button class="btn btn-success" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Clear Filters -->
                    <a href="product_search_result.php?q=<?php echo urlencode($search_query); ?>" 
                       class="btn btn-outline-secondary w-100 filter-btn">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                    
                    <!-- Categories -->
                    <h6 class="mt-3"><i class="fas fa-tags"></i> Categories</h6>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <a href="product_search_result.php?q=<?php echo urlencode($search_query); ?>&category=<?php echo $category['cat_id']; ?>" 
                               class="btn btn-outline-primary w-100 filter-btn <?php echo $filter_category == $category['cat_id'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($category['cat_name']); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Brands -->
                    <h6 class="mt-3"><i class="fas fa-certificate"></i> Brands</h6>
                    <?php if (!empty($brands)): ?>
                        <?php foreach ($brands as $brand): ?>
                            <a href="product_search_result.php?q=<?php echo urlencode($search_query); ?>&brand=<?php echo $brand['brand_id']; ?>" 
                               class="btn btn-outline-secondary w-100 filter-btn <?php echo $filter_brand == $brand['brand_id'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($brand['brand_name']); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Search Results -->
            <div class="col-lg-9">
                <!-- Results Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>
                        <?php echo $total_products; ?> Result<?php echo $total_products != 1 ? 's' : ''; ?> Found
                    </h3>
                    <div>
                        <?php if ($filter_category > 0 || $filter_brand > 0): ?>
                            <span class="badge bg-info">Filtered</span>
                        <?php endif; ?>
                        <?php if ($total_pages > 1): ?>
                            <span class="badge bg-success">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row">
                    <?php if (!empty($paginated_products)): ?>
                        <?php foreach ($paginated_products as $product): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="product-card">
                                    <!-- Product Image -->
                                    <div class="product-image-container">
                                        <?php if (!empty($product['product_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['product_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                                                 class="product-image"
                                                 onerror="this.parentElement.innerHTML='<i class=\'fas fa-image no-image\'></i>'">
                                        <?php else: ?>
                                            <i class="fas fa-image no-image"></i>
                                        <?php endif; ?>
                                        <span class="product-badge">#<?php echo $product['product_id']; ?></span>
                                    </div>

                                    <!-- Product Details -->
                                    <div class="product-body">
                                        <div class="product-meta">
                                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['cat_name']); ?> | 
                                            <i class="fas fa-certificate"></i> <?php echo htmlspecialchars($product['brand_name']); ?>
                                        </div>
                                        <h5 class="product-title">
                                            <?php 
                                            // Highlight search term in title
                                            $title = htmlspecialchars($product['product_title']);
                                            $highlighted = str_ireplace($search_query, '<mark>' . $search_query . '</mark>', $title);
                                            echo $highlighted;
                                            ?>
                                        </h5>
                                        <div class="product-price">
                                            GH₵<?php echo number_format($product['product_price'], 2); ?>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <a href="single_product.php?id=<?php echo $product['product_id']; ?>" 
                                               class="btn-view">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                            <?php if ($is_logged_in): ?>
                                                <button class="btn-add-cart" 
                                                        onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                </button>
                                            <?php else: ?>
                                                <a href="../login/login.php" class="btn-add-cart">
                                                    <i class="fas fa-sign-in-alt"></i> Login to Purchase
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="no-results">
                                <i class="fas fa-search"></i>
                                <h3>No results found for "<?php echo htmlspecialchars($search_query); ?>"</h3>
                                <p class="text-muted">Try different keywords or check your spelling</p>
                                <a href="all_product.php" class="btn btn-success mt-3">
                                    <i class="fas fa-shopping-bag"></i> Browse All Products
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Search results pagination">
                        <ul class="pagination justify-content-center">
                            <!-- Previous Button -->
                            <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?q=<?php echo urlencode($search_query); ?>&page=<?php echo $current_page - 1; ?><?php echo $filter_category > 0 ? '&category=' . $filter_category : ''; ?><?php echo $filter_brand > 0 ? '&brand=' . $filter_brand : ''; ?>">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                            
                            <!-- Page Numbers -->
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?q=<?php echo urlencode($search_query); ?>&page=<?php echo $i; ?><?php echo $filter_category > 0 ? '&category=' . $filter_category : ''; ?><?php echo $filter_brand > 0 ? '&brand=' . $filter_brand : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Next Button -->
                            <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?q=<?php echo urlencode($search_query); ?>&page=<?php echo $current_page + 1; ?><?php echo $filter_category > 0 ? '&category=' . $filter_category : ''; ?><?php echo $filter_brand > 0 ? '&brand=' . $filter_brand : ''; ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
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
                    alert('✓ Product added to cart!');
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