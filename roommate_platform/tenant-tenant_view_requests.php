<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch roommate requests sent to the logged-in tenant, along with sender's name
$stmt = $conn->prepare("
    SELECT r.*, u.name AS sender_name 
    FROM roommate_requests r 
    JOIN users u ON r.sender_id = u.user_id 
    WHERE r.receiver_id = ? 
    ORDER BY r.sent_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Roommate Requests - Roommate & Rental Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  body { background: #f8f9fa; }
  .container { max-width: 800px; margin: 40px auto; }
  .request-card {
    background: white;
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
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

<div class="container">
  <h2 class="mb-4"><i class="fa-solid fa-envelope-open-text me-2"></i>Your Roommate Requests</h2>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($request = $result->fetch_assoc()): ?>
      <div class="request-card">
        <h5>Request from: <?= htmlspecialchars($request['sender_name']) ?> (User ID: <?= $request['sender_id'] ?>)</h5>
        <p><strong>Sent At:</strong> <?= htmlspecialchars($request['sent_at']) ?></p>
        <p><strong>Status:</strong> 
          <?php
            $status = $request['status'];
            $badgeClass = 'secondary';
            if ($status === 'Pending') $badgeClass = 'warning';
            elseif ($status === 'Accepted') $badgeClass = 'success';
            elseif ($status === 'Declined') $badgeClass = 'danger';
          ?>
          <span class="badge bg-<?= strtolower($badgeClass) ?>"><?= htmlspecialchars($status) ?></span>
        </p>

        <?php if ($status === 'Pending'): ?>
          <form action="tenant-tenant_handle_request.php" method="POST" class="d-inline">
            <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
            <button type="submit" name="accept_request" class="btn btn-success btn-sm me-1"><i class="fa-solid fa-check"></i> Accept</button>
          </form>
          <form action="tenant-tenant_handle_request.php" method="POST" class="d-inline">
            <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
            <button type="submit" name="decline_request" class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i> Decline</button>
          </form>
        <?php elseif ($status === 'Accepted'): ?>
          <p class="mt-2 text-success"><strong>Request Accepted!</strong> You can now see each other's contact information.</p>
          <?php
            $sender_id = $request['sender_id'];
            $stmtUser = $conn->prepare("SELECT contact, email FROM users WHERE user_id = ?");
            $stmtUser->bind_param("i", $sender_id);
            $stmtUser->execute();
            $user_info = $stmtUser->get_result()->fetch_assoc();
          ?>
          <p><strong>Contact:</strong> <?= htmlspecialchars($user_info['contact']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($user_info['email']) ?></p>
        <?php elseif ($status === 'Declined'): ?>
          <p class="text-danger">Request Declined</p>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No requests found.</p>
  <?php endif; ?>

  <div class="mt-3 text-center">
    <a href="dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
