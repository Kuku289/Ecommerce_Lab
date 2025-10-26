<?php
require_once '../settings/core.php';

// Check if user is logged in and is admin
if (!check_login()) {
    header('Location: ../login/login.php');
    exit();
}

if (!check_admin()) {
    header('Location: ../index.php');
    exit();
}

// Include necessary files
require_once(dirname(__FILE__) . '/../controllers/product_controller.php');
require_once(dirname(__FILE__) . '/../controllers/category_controller.php');
require_once(dirname(__FILE__) . '/../controllers/brand_controller.php');

// Fetch categories for dropdown
try {
    $categories = get_all_categories_ctr();
} catch (Exception $e) {
    $categories = [];
}

// Fetch brands for dropdown
try {
    $brands = get_all_brands_ctr();
} catch (Exception $e) {
    $brands = [];
}

$user_name = get_user_name();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - BotaniQs</title>
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
        .container-fluid {
            padding: 20px;
            margin-top: 80px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .card-header {
            background-color: var(--primary);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        .btn-primary {
            background-color: var(--primary);
            border: none;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .btn-success {
            background-color: var(--primary);
            border: none;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }
        .product-image {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        #imagePreview {
            margin-top: 10px;
        }
        #imagePreview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 5px;
            border: 2px solid #dee2e6;
        }
        .form-label {
            font-weight: 600;
            color: #2c3e50;
        }
        .required:after {
            content: " *";
            color: red;
        }
        .alert-custom {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-leaf"></i> BotaniQs Admin
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
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-list"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="brand.php">
                            <i class="fas fa-tag"></i> Brands
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="product.php">
                            <i class="fas fa-box"></i> Products
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-box"></i> Product Management
                </h2>
            </div>
        </div>

        <!-- Add/Edit Product Form -->
        <div class="row">
            <div class="col-md-12 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle"></i> <span id="formTitle">Add New Product</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="productForm" onsubmit="addProduct(event)">
                            <input type="hidden" id="product_id" name="product_id">
                            <input type="hidden" id="product_image" name="product_image">
                            
                            <!-- Category Selection -->
                            <div class="mb-3">
                                <label for="product_cat" class="form-label required">Category</label>
                                <select class="form-select" id="product_cat" name="product_cat" required>
                                    <option value="">Select Category</option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category['cat_id']); ?>">
                                                <?php echo htmlspecialchars($category['cat_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Brand Selection -->
                            <div class="mb-3">
                                <label for="product_brand" class="form-label required">Brand</label>
                                <select class="form-select" id="product_brand" name="product_brand" required>
                                    <option value="">Select Brand</option>
                                    <?php if (!empty($brands)): ?>
                                        <?php foreach ($brands as $brand): ?>
                                            <option value="<?php echo htmlspecialchars($brand['brand_id']); ?>">
                                                <?php echo htmlspecialchars($brand['brand_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Product Title -->
                            <div class="mb-3">
                                <label for="product_title" class="form-label required">Product Title</label>
                                <input type="text" class="form-control" id="product_title" name="product_title" 
                                       placeholder="Enter product title" required>
                            </div>

                            <!-- Product Price -->
                            <div class="mb-3">
                                <label for="product_price" class="form-label required">Price (GH₵)</label>
                                <input type="number" class="form-control" id="product_price" name="product_price" 
                                       placeholder="0.00" step="0.01" min="0.01" required>
                            </div>

                            <!-- Product Description -->
                            <div class="mb-3">
                                <label for="product_desc" class="form-label required">Description</label>
                                <textarea class="form-control" id="product_desc" name="product_desc" 
                                          rows="4" placeholder="Enter product description" required></textarea>
                            </div>

                            <!-- Product Keywords -->
                            <div class="mb-3">
                                <label for="product_keywords" class="form-label required">Keywords</label>
                                <input type="text" class="form-control" id="product_keywords" name="product_keywords" 
                                       placeholder="e.g., organic, seeds, wellness" required>
                                <small class="text-muted">Separate keywords with commas</small>
                            </div>

                            <!-- Product Image Upload -->
                            <div class="mb-3">
                                <label for="product_image_file" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="product_image_file" 
                                       accept="image/jpeg,image/jpg,image/png,image/gif">
                                <small class="text-muted">Max size: 5MB. Allowed: JPG, PNG, GIF</small>
                                
                                <!-- Upload Button -->
                                <button type="button" class="btn btn-secondary btn-sm mt-2" 
                                        onclick="uploadProductImage(document.getElementById('product_id').value || 0)">
                                    <i class="fas fa-upload"></i> Upload Image
                                </button>
                                
                                <!-- Image Preview -->
                                <div id="imagePreview"></div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="fas fa-save"></i> Add Product
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-redo"></i> Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products List -->
            <div class="col-md-12 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> All Products
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th>Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="spinner-border text-success" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/product.js"></script>
    <script>
        // Reset form function
        function resetForm() {
            document.getElementById('productForm').reset();
            document.getElementById('product_id').value = '';
            document.getElementById('product_image').value = '';
            document.getElementById('imagePreview').innerHTML = '';
            
            // Reset form to add mode
            document.getElementById('productForm').onsubmit = addProduct;
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Add Product';
            document.getElementById('formTitle').textContent = 'Add New Product';
        }

        // Show message with better styling
        function showMessage(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${alertClass} alert-dismissible fade show alert-custom`;
            alertDiv.innerHTML = `
                <i class="fas fa-${icon}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Override edit product to update form title
        function editProduct(productId) {
            document.getElementById('formTitle').textContent = 'Edit Product';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Update Product';
            
            fetch(`../actions/get_product_action.php?product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateEditForm(data.data);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while loading product details', 'error');
            });
        }
    </script>
</body>
</html>