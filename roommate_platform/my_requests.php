<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// For tenants: show requests they sent (with reply)
// For owners: show requests sent to their properties

if ($role === 'tenant') {
    $sql = "SELECT r.request_id, r.status, r.sent_at, r.message, r.reply, p.title AS property_title, u.name AS owner_name
            FROM requests r
            JOIN properties p ON r.property_id = p.property_id
            JOIN users u ON r.receiver_id = u.user_id
            WHERE r.sender_id = ?
            ORDER BY r.sent_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
} else if ($role === 'owner') {
    $sql = "SELECT r.request_id, r.status, r.sent_at, r.message, p.title AS property_title, u.name AS sender_name
            FROM requests r
            JOIN properties p ON r.property_id = p.property_id
            JOIN users u ON r.sender_id = u.user_id
            WHERE p.owner_id = ?
            ORDER BY r.sent_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
} else {
    header("Location: login.php");
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>My Requests - Roommate & Rental Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  body { background: #f8f9fa; }
  .container { max-width: 900px; margin: 40px auto; }
  table {
    background: white;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
  }
  td, th {
    vertical-align: middle !important;
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
  <h2 class="mb-4"><i class="fa-solid fa-list-check me-2"></i>Requests to Owners</h2>

  <?php if ($result->num_rows > 0): ?>
    <table class="table table-hover">
      <thead class="table-primary">
        <tr>
          <th>Property</th>
          <th><?= $role === 'tenant' ? 'Owner' : 'Sender' ?></th>
          <th>Message</th>
          <?php if ($role === 'tenant'): ?>
            <th>Owner Reply</th>
          <?php endif; ?>
          <th>Sent At</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($req = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($req['property_title']) ?></td>
            <td><?= htmlspecialchars($role === 'tenant' ? $req['owner_name'] : $req['sender_name']) ?></td>
            <td><?= nl2br(htmlspecialchars($req['message'])) ?></td>
            <?php if ($role === 'tenant'): ?>
              <td><?= $req['reply'] ? nl2br(htmlspecialchars($req['reply'])) : '<em class="text-muted">No reply</em>' ?></td>
            <?php endif; ?>
            <td><?= htmlspecialchars($req['sent_at']) ?></td>
            <td>
              <?php
                $status = strtolower($req['status']);
                $badgeClass = 'secondary';
                if ($status === 'pending') $badgeClass = 'warning';
                elseif ($status === 'accepted') $badgeClass = 'success';
                elseif ($status === 'rejected') $badgeClass = 'danger';
              ?>
              <span class="badge bg-<?= $badgeClass ?> text-capitalize"><?= $status ?></span>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
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
