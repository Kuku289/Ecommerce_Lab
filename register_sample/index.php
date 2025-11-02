<?php
require_once 'settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		.menu-tray {
			position: fixed;
			top: 16px;
			right: 16px;
			background: rgba(255,255,255,0.95);
			border: 1px solid #e6e6e6;
			border-radius: 8px;
			padding: 6px 10px;
			box-shadow: 0 4px 10px rgba(0,0,0,0.06);
			z-index: 1000;
		}
		.menu-tray a { margin-left: 8px; }
	</style>
</head>
<body>

	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		
		<?php if (!check_login()): ?>
			<!-- Not logged in - Show Register/Login -->
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		
		<?php elseif (check_admin()): ?>
			<!-- Logged in as Admin - Show Logout/Category -->
			<a href="admin/category.php" class="btn btn-sm btn-outline-success">Category</a>
			<a href="login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
		
		<?php else: ?>
			<!-- Logged in as Customer - Show Logout only -->
			<a href="login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
		
		<?php endif; ?>
	</div>

	<div class="container" style="padding-top:120px;">
		<div class="text-center">
			<?php if (!check_login()): ?>
				<h1>Welcome</h1>
				<p class="text-muted">Use the menu in the top-right to Register or Login.</p>
			
			<?php elseif (check_admin()): ?>
				<h1>Welcome, <?php echo get_user_name(); ?>!</h1>
				<p class="text-muted">You are logged in as an Administrator.</p>
				<p>Use the menu in the top-right to manage categories or logout.</p>
			
			<?php else: ?>
				<h1>Welcome, <?php echo get_user_name(); ?>!</h1>
				<p class="text-muted">You are logged in as a Customer.</p>
				<p>Use the menu in the top-right to logout.</p>
			
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>