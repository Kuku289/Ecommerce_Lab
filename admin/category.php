<?php
require_once '../settings/core.php';

// Check if user is logged in and is admin
if (!check_login()) {
    header("Location: ../login/login.php");
    exit();
}

if (!check_admin()) {
    header("Location: ../login/login.php");
    exit();
}

// Get user info
require_once '../controllers/category_controller.php';
$user_id = get_user_id();
$user_name = get_user_name();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management - BotaniQs Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4CAF50;
            --secondary: #FF9800;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .btn-custom {
            background-color: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .btn-custom:hover {
            background-color: #45a049;
            border-color: #45a049;
        }

        .highlight {
            color: var(--primary);
        }

        .table th {
            background-color: #f8f9fa;
        }

        .category-card {
            transition: transform 0.3s;
            border-left: 4px solid var(--primary);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .info-card {
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(255, 152, 0, 0.1));
            border-left: 4px solid var(--secondary);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><i class="fas fa-tags"></i> Category Management</h2>
                        <p class="text-muted mb-0">Welcome, <?php echo htmlspecialchars($user_name); ?></p>
                    </div>
                    <div>
                        <a href="../index.php" class="btn btn-secondary me-2">
                            <i class="fas fa-home"></i> Home
                        </a>
                        <a href="brand.php" class="btn btn-info me-2">
                            <i class="fas fa-copyright"></i> Brands
                        </a>
                        <a href="product.php" class="btn btn-warning me-2">
                            <i class="fas fa-box"></i> Products
                        </a>
                        <a href="../login/logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Category Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card category-card">
                    <div class="card-header">
                        <h5><i class="fas fa-plus"></i> Add New Category</h5>
                    </div>
                    <div class="card-body">
                        <form id="addCategoryForm">
                            <div class="mb-3">
                                <label for="categoryName" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="categoryName" name="category_name" 
                                       required minlength="2" maxlength="100" 
                                       placeholder="e.g., Seeds, Oils, Herbs, Natural Butters">
                                <small class="text-muted">Category name must be unique (2-100 characters)</small>
                            </div>
                            <button type="submit" class="btn btn-custom">
                                <i class="fas fa-plus"></i> Add Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-info-circle text-primary"></i> About Categories</h5>
                        <p class="card-text">
                            Categories are the main organizational structure for your wellness products. They represent broad groups of items.
                        </p>
                        <ul class="mb-3">
                            <li><strong>Seeds:</strong> Chia, Flax, Pumpkin, Sunflower</li>
                            <li><strong>Essential Oils:</strong> Lavender, Tea Tree, Peppermint, Eucalyptus</li>
                            <li><strong>Herbs:</strong> Moringa, Turmeric, Ginger, Basil</li>
                            <li><strong>Natural Butters:</strong> Shea, Cocoa, Mango Butter</li>
                        </ul>
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-lightbulb"></i> <strong>Workflow Tip:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Create categories first (you're here!)</li>
                                <li>Add brands within each category</li>
                                <li>Add products under brands</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Your Categories</h5>
                            <span class="badge bg-primary" id="categoryCount">Loading...</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="categoriesTable">
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                <p class="mt-2 text-muted">Loading categories...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" id="editCatId" name="cat_id">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="editCategoryName" name="category_name" 
                                   required minlength="2" maxlength="100">
                            <small class="text-muted">Only the category name can be edited</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-custom" onclick="updateCategory()">
                        <i class="fas fa-save"></i> Update Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/category.js"></script>
</body>
</html>