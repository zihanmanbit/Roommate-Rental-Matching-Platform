<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Use 'name' or 'user_name' consistently (adjust as needed)
$user_name = $_SESSION['user_name'] ?? $_SESSION['name'] ?? 'User';
$user_role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard - Roommate & Rental Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  body { background: #f8f9fa; }
  .dashboard-container {
    max-width: 900px;
    margin: 40px auto;
  }
  .btn-group-lg > .btn {
    padding: 1.25rem 2rem;
    font-size: 1.25rem;
  }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php"><i class="fa-solid fa-house-user me-2"></i>Roommate & Rental Platform</a>
    <div>
      <a href="logout.php" class="btn btn-outline-light btn-sm">
        <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
      </a>
    </div>
  </div>
</nav>

<div class="dashboard-container text-center">
  <h1 class="mb-4">Welcome, <?= htmlspecialchars($user_name) ?>!</h1>
  <h4 class="mb-5 text-muted">You are logged in as <strong><?= htmlspecialchars(ucfirst($user_role)) ?></strong></h4>

  <div class="d-flex flex-wrap justify-content-center gap-4">
    <?php if ($user_role === 'owner'): ?>
      <a href="add_property.php" class="btn btn-success btn-lg btn-group-lg" role="button">
        <i class="fa-solid fa-plus me-2"></i> Add Property
      </a>
      <a href="view_requests.php" class="btn btn-info btn-lg btn-group-lg" role="button">
        <i class="fa-solid fa-envelope-open-text me-2"></i> View Requests
      </a>
      <a href="browse_properties.php" class="btn btn-primary btn-lg btn-group-lg" role="button">
        <i class="fa-solid fa-building me-2"></i> Browse Properties
      </a>
    <?php elseif ($user_role === 'tenant'): ?>
      <a href="browse_properties.php" class="btn btn-primary btn-lg btn-group-lg" role="button">
        <i class="fa-solid fa-building me-2"></i> Browse Properties
      </a>
      <a href="tenant_preference.php" class="btn btn-warning btn-lg btn-group-lg" role="button">
        <i class="fa-solid fa-sliders me-2"></i> Set Preferences
      </a>
      <a href="roommate_connection.php" class="btn btn-success btn-lg btn-group-lg" role="button">
        <i class="fa-solid fa-user-friends me-2"></i> Find Roommates
      </a>
      <a href="tenant-tenant_view_requests.php" class="btn btn-info btn-lg btn-group-lg" role="button">
        <i class="fa-solid fa-envelope-open-text me-2"></i> View Roommate Requests
      </a>
      <a href="my_requests.php" class="btn btn-secondary btn-lg btn-group-lg" role="button">
        <i class="fa-solid fa-paper-plane me-2"></i> My Sent Requests
      </a>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
