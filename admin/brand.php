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

// Get user's categories for the dropdown
require_once '../controllers/category_controller.php';
$user_id = get_user_id();
$categories = get_categories_by_user_ctr($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            background-color: #4CAF50;
            border-color: #4CAF50;
            color: #fff;
        }

        .btn-custom:hover {
            background-color: #45a049;
            border-color: #45a049;
        }

        .highlight {
            color: #4CAF50;
        }

        .table th {
            background-color: #f8f9fa;
        }

        .category-badge {
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-copyright"></i> Brand Management</h2>
                    <div>
                        <a href="../index.php" class="btn btn-secondary me-2">
                            <i class="fas fa-home"></i> Home
                        </a>
                        <a href="category.php" class="btn btn-info me-2">
                            <i class="fas fa-tags"></i> Categories
                        </a>
                        <a href="../login/logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Brand Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-plus"></i> Add New Brand</h5>
                    </div>
                    <div class="card-body">
                        <form id="addBrandForm">
                            <div class="mb-3">
                                <label for="categorySelect" class="form-label">Category *</label>
                                <select class="form-select" id="categorySelect" name="cat_id" required>
                                    <option value="">-- Select Category --</option>
                                    <?php if ($categories && count($categories) > 0): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['cat_id']; ?>">
                                                <?php echo htmlspecialchars($category['cat_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Select the category this brand belongs to</small>
                            </div>
                            <div class="mb-3">
                                <label for="brandName" class="form-label">Brand Name *</label>
                                <input type="text" class="form-control" id="brandName" name="brand_name" required 
                                       minlength="2" maxlength="100" placeholder="e.g., Nike, Adidas, Puma">
                                <small class="text-muted">Brand + Category combination must be unique</small>
                            </div>
                            <button type="submit" class="btn btn-custom">
                                <i class="fas fa-plus"></i> Add Brand
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-info-circle"></i> About Brands</h5>
                        <p class="card-text">
                            Brands represent specific labels within your categories. For example:
                        </p>
                        <ul>
                            <li><strong>Seeds Category:</strong> Chia Seeds, Flax Seeds, Pumpkin Seeds</li>
                            <li><strong>Oils Category:</strong> Lavender Oil, Tea Tree Oil, Coconut Oil</li>
                            <li><strong>Herbs Category:</strong> Moringa, Turmeric, Ginger</li>
                        </ul>
                        <p class="text-muted">
                            <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Each brand must belong to a category, and the brand + category combination must be unique.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brands List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Your Brands</h5>
                    </div>
                    <div class="card-body">
                        <div id="brandsTable">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin"></i> Loading brands...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editBrandForm">
                        <input type="hidden" id="editBrandId" name="brand_id">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" id="editCategoryName" class="form-control" readonly>
                            <small class="text-muted">Category cannot be changed when editing</small>
                        </div>
                        <div class="mb-3">
                            <label for="editBrandName" class="form-label">Brand Name *</label>
                            <input type="text" class="form-control" id="editBrandName" name="brand_name" required
                                   minlength="2" maxlength="100">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-custom" onclick="updateBrand()">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/brand.js"></script>
</body>
</html>