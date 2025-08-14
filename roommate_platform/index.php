<?php
session_start();
require 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? ($_SESSION['user_name'] ?? 'User') : '';
$user_role = $is_logged_in ? ucfirst($_SESSION['role']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Home - Roommate & Rental Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  body { background: #f8f9fa; }
  .hero {
    padding: 80px 20px;
    text-align: center;
  }
  .hero h1 {
    font-weight: bold;
    margin-bottom: 20px;
  }
  .hero p {
    font-size: 1.1rem;
    margin-bottom: 30px;
    color: #6c757d;
  }
</style>
</head>
<body>

<!-- Navbar (same style as dashboard) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <i class="fa-solid fa-house-user me-2"></i>Roommate & Rental Platform
    </a>
    <div>
      <?php if ($is_logged_in): ?>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">
          <i class="fa-solid fa-gauge-high me-1"></i> Dashboard
        </a>
        <a href="logout.php" class="btn btn-outline-light btn-sm">
          <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
        </a>
      <?php else: ?>
        <a href="login.php" class="btn btn-outline-light btn-sm me-2">
          <i class="fa-solid fa-right-to-bracket me-1"></i> Login
        </a>
        <a href="register.php" class="btn btn-light btn-sm">
          <i class="fa-solid fa-user-plus me-1"></i> Register
        </a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<div class="container hero">
  <?php if ($is_logged_in): ?>
    <h1>Welcome back, <?= htmlspecialchars($user_name) ?>!</h1>
    <p>You are logged in as <strong><?= htmlspecialchars($user_role) ?></strong>.  
    Head over to your dashboard to continue.</p>
    <a href="dashboard.php" class="btn btn-primary btn-lg">
      <i class="fa-solid fa-gauge-high me-1"></i> Go to Dashboard
    </a>
  <?php else: ?>
    <h1>Find Your Perfect Roommate or Rental</h1>
    <p>Join our platform to connect with trusted tenants and property owners in your city.</p>
    <a href="register.php" class="btn btn-primary btn-lg me-2">
      <i class="fa-solid fa-user-plus me-1"></i> Get Started
    </a>
    <a href="login.php" class="btn btn-outline-primary btn-lg">
      <i class="fa-solid fa-right-to-bracket me-1"></i> Login
    </a>
  <?php endif; ?>
</div>

<!-- Features Section -->
<div class="container my-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body text-center">
          <i class="fa-solid fa-building fa-2x text-primary mb-3"></i>
          <h5 class="card-title">Browse Properties</h5>
          <p class="card-text text-muted">Explore listings from property owners tailored to your needs and preferences.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body text-center">
          <i class="fa-solid fa-user-friends fa-2x text-primary mb-3"></i>
          <h5 class="card-title">Find Roommates</h5>
          <p class="card-text text-muted">Connect with potential roommates who match your lifestyle and budget.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body text-center">
          <i class="fa-solid fa-shield-halved fa-2x text-primary mb-3"></i>
          <h5 class="card-title">Secure Platform</h5>
          <p class="card-text text-muted">Enjoy a safe and verified environment for finding rentals and roommates.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-light py-3 mt-5 border-top">
  <div class="container text-center text-muted">
    &copy; <?= date('Y') ?> Roommate & Rental Platform. All rights reserved.
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
