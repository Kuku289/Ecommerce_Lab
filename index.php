<?php
require_once 'settings/core.php';
require_once 'settings/db_class.php';

// Get categories from database
$db = new db_connection();
$db->db_connect();

$categories = [];
$categories_result = $db->db->query("SELECT * FROM categories LIMIT 6");
if ($categories_result) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
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
            --accent: #3F51B5;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Background Image */
        .page-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('W7.jpg') center/cover no-repeat;
            z-index: -1;
            opacity: 0.3;
        }

        /* Navigation */
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

        /* Hero Section */
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

        .hero-section p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
        }

        /* Value Props */
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

        /* Category Cards */
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

        .category-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .category-card img {
            width: 100%;
            max-width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        /* Wellness Tips */
        .wellness-tip {
            background: white;
            border-left: 4px solid var(--primary);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Buttons */
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background: #45a049;
            transform: scale(1.05);
        }

        .btn-outline-custom {
            border: 2px solid white;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-outline-custom:hover {
            background: white;
            color: var(--primary);
        }

        /* Footer */
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
    <!-- Background Image -->
    <div class="page-background"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-leaf"></i> BotaniQs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#categories">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="#wellness">Wellness</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    
                    <?php if (!check_login()): ?>
                        <li class="nav-item"><a class="nav-link" href="login/login.php">Login</a></li>
                        <li class="nav-item">
                            <a class="btn btn-success btn-sm ms-2" href="login/register.php">Register</a>
                        </li>
                    <?php elseif (check_admin()): ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?php echo get_user_name(); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-success btn-sm ms-2" href="admin/categories.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-danger btn-sm ms-2" href="login/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?php echo get_user_name(); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-danger btn-sm ms-2" href="login/logout.php">Logout</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <h1>Authentic Wellness, Made Accessible</h1>
            <p>Premium organic seeds, essential oils, and herbs for Ghana's wellness journey</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="#categories" class="btn btn-light btn-primary-custom">Explore Products</a>
                <?php if (!check_login()): ?>
                    <a href="login/register.php" class="btn btn-outline-custom">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Value Propositions -->
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
                        <p>Learn proper usage & benefits</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories">
        <div class="container">
            <h2 class="section-title text-center">Shop by Category</h2>
            <p class="text-center text-muted mb-5">Curated collections for your wellness journey</p>
            
            <div class="row g-4">
                <?php if (count($categories) > 0): ?>
                    <?php foreach ($categories as $category): ?>
                        <div class="col-md-4">
                            <div class="category-card">
                                <i class="fas fa-spa category-icon"></i>
                                <h3><?php echo htmlspecialchars($category['cat_name']); ?></h3>
                                <p class="text-muted">Explore our premium collection</p>
                                <a href="#" class="btn btn-success">View Products</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default Categories -->
                    <div class="col-md-4">
                        <div class="category-card">
                            <img src="S2.jpg" alt="Organic Seeds">
                            <h3>Organic Seeds</h3>
                            <p class="text-muted">Chia, flax, pumpkin & more</p>
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

    <!-- Wellness Tips -->
    <section id="wellness" class="bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5">Wellness Tips & Education</h2>
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

    <!-- About/Impact Section -->
    <section id="about">
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

    <!-- Footer -->
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
                        <li><a href="#categories" class="text-white-50">Categories</a></li>
                        <li><a href="login/register.php" class="text-white-50">Register</a></li>
                        <li><a href="login/login.php" class="text-white-50">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <p><i class="fas fa-envelope"></i> botaniqs@gmail.com</p>
                    <p><i class="fas fa-phone"></i> +233 20