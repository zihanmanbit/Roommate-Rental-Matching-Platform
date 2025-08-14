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
  /* Background pattern */
  body {
    background: #f8f9fa url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"%3E%3Ccircle cx="30" cy="30" r="1" fill="%23dee2e6"/%3E%3C/svg%3E') repeat;
    font-family: 'Segoe UI', sans-serif;
  }

  /* Hero Section */
  .hero {
    position: relative;
    padding: 100px 20px;
    text-align: center;
    color: white;
    overflow: hidden;
    border-radius: 16px;
    background: linear-gradient(135deg,#0d6efd,#6610f2);
  }

  /* Decorative shapes */
  .hero::before, .hero::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    opacity: 0.3;
  }
  .hero::before {
    width: 300px; height: 300px;
    background: rgba(255,255,255,0.2);
    top: -80px; left: -80px;
  }
  .hero::after {
    width: 400px; height: 400px;
    background: rgba(255,255,255,0.15);
    bottom: -100px; right: -100px;
  }

  .hero h1 {
    font-weight: 800; font-size: 2.5rem;
    margin-bottom: 20px;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    animation: fadeUp 1s ease forwards;
  }
  .hero p {
    font-size: 1.2rem; color: #e2e2e2; margin-bottom: 30px;
    animation: fadeUp 1s ease 0.2s forwards;
  }
  .hero .btn {
    font-weight: 600; padding: 12px 30px;
    transition: all 0.3s ease;
  }
  .hero .btn-primary:hover { transform: translateY(-4px) scale(1.05); }
  .hero .btn-outline-light:hover { transform: translateY(-4px) scale(1.05); }

  @keyframes fadeUp { 0% { opacity:0; transform: translateY(20px); } 100% { opacity:1; transform: translateY(0); } }

  /* Feature Cards */
  .card.hover-lift {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 12px;
  }
  .card.hover-lift:hover {
    transform: translateY(-15px) rotate(-1deg);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
  }
  .card .rounded-circle {
    transition: transform 0.3s, background 0.3s;
  }
  .card:hover .rounded-circle {
    transform: scale(1.15) rotate(10deg);
    background: linear-gradient(135deg,#0d6efd,#6610f2);
  }
  .card-title { font-weight: 700; }

  /* Footer */
  footer {
    box-shadow: 0 -3px 15px rgba(0,0,0,0.05);
    font-weight: 500;
    background: #fff;
  }
</style>
</head>
<body>

<!-- Navbar -->
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
<div class="container hero mb-5">
  <?php if ($is_logged_in): ?>
    <h1>Welcome back, <?= htmlspecialchars($user_name) ?>!</h1>
    <p>You are logged in as <strong><?= htmlspecialchars($user_role) ?></strong>. Head over to your dashboard to continue.</p>
    <a href="dashboard.php" class="btn btn-light btn-lg shadow-sm mt-3">
      <i class="fa-solid fa-gauge-high me-1"></i> Go to Dashboard
    </a>
  <?php else: ?>
    <h1>Find Your Perfect Roommate or Rental</h1>
    <p>Join our platform to connect with trusted tenants and property owners in your city.</p>
    <a href="register.php" class="btn btn-primary btn-lg shadow-lg me-2 mt-3">
      <i class="fa-solid fa-user-plus me-1"></i> Get Started
    </a>
    <a href="login.php" class="btn btn-outline-light btn-lg shadow-lg mt-3">
      <i class="fa-solid fa-right-to-bracket me-1"></i> Login
    </a>
  <?php endif; ?>
</div>

<!-- Features Section -->
<div class="container my-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0 text-center hover-lift">
        <div class="card-body">
          <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;font-size:1.5rem;">
            <i class="fa-solid fa-building"></i>
          </div>
          <h5 class="card-title">Browse Properties</h5>
          <p class="card-text text-muted">Explore listings from property owners tailored to your needs and preferences.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0 text-center hover-lift">
        <div class="card-body">
          <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;font-size:1.5rem;">
            <i class="fa-solid fa-user-friends"></i>
          </div>
          <h5 class="card-title">Find Roommates</h5>
          <p class="card-text text-muted">Connect with potential roommates who match your lifestyle and budget.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0 text-center hover-lift">
        <div class="card-body">
          <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;font-size:1.5rem;">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <h5 class="card-title">Secure Platform</h5>
          <p class="card-text text-muted">Enjoy a safe and verified environment for finding rentals and roommates.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="py-3 mt-5 border-top text-center text-muted">
  &copy; <?= date('Y') ?> Roommate & Rental Platform. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
