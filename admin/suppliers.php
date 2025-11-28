<?php
session_start();
require_once(dirname(__FILE__) . '/../settings/core.php');

// Check if user is logged in and is admin
if (!check_login()) {
    header('Location: ../login/login.php');
    exit();
}

if (!check_admin()) {
    header('Location: ../index.php');
    exit();
}

require_once(dirname(__FILE__) . '/../controllers/supplier_controller.php');

$suppliers = get_all_suppliers_ctr();
$user_name = get_user_name();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management - BotaniQs Admin</title>
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
            background: linear-gradient(135deg, var(--primary), #66BB6A);
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .admin-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 15px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .supplier-card {
            transition: transform 0.3s;
        }
        .supplier-card:hover {
            transform: translateY(-5px);
        }
        .badge-verified {
            background: #4CAF50;
        }
        .badge-pending {
            background: #FF9800;
        }
        .badge-rejected {
            background: #f44336;
        }
        .certification-badge {
            display: inline-block;
            padding: 5px 10px;
            margin: 3px;
            border-radius: 15px;
            font-size: 12px;
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
        .supplier-logo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
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
                        <a class="nav-link" href="category.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="brand.php">Brands</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="suppliers.php">Suppliers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product.php">Products</a>
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

    <div class="admin-container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-truck"></i> Supplier Management</h2>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="fas fa-plus"></i> Add New Supplier
            </button>
        </div>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success"><?php echo count($suppliers); ?></h3>
                        <p class="mb-0">Total Suppliers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success">
                            <?php echo count(array_filter($suppliers, fn($s) => $s['verification_status'] == 'Verified')); ?>
                        </h3>
                        <p class="mb-0">Verified</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning">
                            <?php echo count(array_filter($suppliers, fn($s) => $s['verification_status'] == 'Pending')); ?>
                        </h3>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-danger">
                            <?php echo count(array_filter($suppliers, fn($s) => $s['verification_status'] == 'Rejected')); ?>
                        </h3>
                        <p class="mb-0">Rejected</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suppliers List -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">All Suppliers</h5>
                
                <?php if (empty($suppliers)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No suppliers found. Add your first supplier!
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Logo</th>
                                    <th>Supplier Name</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Certifications</th>
                                    <th>Products</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($supplier['supplier_logo'])): ?>
                                                <img src="../<?php echo htmlspecialchars($supplier['supplier_logo']); ?>" 
                                                     alt="Logo" class="supplier-logo">
                                            <?php else: ?>
                                                <div class="supplier-logo bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-building text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($supplier['supplier_name']); ?></strong><br>
                                            <small class="text-muted">#<?php echo $supplier['supplier_id']; ?></small>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($supplier['supplier_email']); ?><br>
                                                <?php if ($supplier['supplier_phone']): ?>
                                                    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($supplier['supplier_phone']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo strtolower($supplier['verification_status']); ?>">
                                                <?php echo $supplier['verification_status']; ?>
                                            </span>
                                            <?php if ($supplier['verification_status'] == 'Verified'): ?>
                                                <br><small class="text-muted">by <?php echo htmlspecialchars($supplier['verified_by_name']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($supplier['fda_approved']): ?>
                                                <span class="certification-badge badge-fda">
                                                    <i class="fas fa-check"></i> FDA
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($supplier['organic_certified']): ?>
                                                <span class="certification-badge badge-organic">
                                                    <i class="fas fa-leaf"></i> Organic
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($supplier['fair_trade_certified']): ?>
                                                <span class="certification-badge badge-fairtrade">
                                                    <i class="fas fa-handshake"></i> Fair Trade
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($supplier['local_farmer']): ?>
                                                <span class="certification-badge badge-local">
                                                    <i class="fas fa-map-marker-alt"></i> Local
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $supplier['product_count']; ?> Products</span>
                                        </td>
                                        <td>
                                            <small><?php echo date('M d, Y', strtotime($supplier['registration_date'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="../view/supplier_profile.php?id=<?php echo $supplier['supplier_id']; ?>" 
                                                   class="btn btn-sm btn-info" title="View Profile">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-success verify-supplier-btn" 
                                                        data-supplier-id="<?php echo $supplier['supplier_id']; ?>"
                                                        title="Verify">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-supplier-btn" 
                                                        data-supplier-id="<?php echo $supplier['supplier_id']; ?>"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="add-supplier-form" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Supplier Name *</label>
                                <input type="text" class="form-control" name="supplier_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="supplier_email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="supplier_phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Logo</label>
                                <input type="file" class="form-control" name="supplier_logo" accept="image/*">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="supplier_address" rows="2"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="supplier_description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Add Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Verify Supplier Modal -->
    <div class="modal fade" id="verifyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-check-circle"></i> Verify Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="verify-supplier-form">
                    <input type="hidden" name="supplier_id" id="verify-supplier-id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Verification Status</label>
                            <select class="form-select" name="verification_status" required>
                                <option value="Verified">Verified</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Certifications</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fda_approved" value="1">
                                <label class="form-check-label">FDA Approved</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="organic_certified" value="1">
                                <label class="form-check-label">Organic Certified</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fair_trade_certified" value="1">
                                <label class="form-check-label">Fair Trade Certified</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="local_farmer" value="1">
                                <label class="form-check-label">Local Farmer</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/supplier.js"></script>
</body>
</html>