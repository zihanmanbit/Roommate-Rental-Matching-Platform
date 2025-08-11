<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Fetch requests sent to this owner, join property and sender info
$sql = "SELECT r.request_id, r.status, r.sent_at, r.message, r.reply, p.title AS property_title, u.name AS sender_name
        FROM requests r
        JOIN properties p ON r.property_id = p.property_id
        JOIN users u ON r.sender_id = u.user_id
        WHERE p.owner_id = ?
        ORDER BY r.sent_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>View Requests - Roommate & Rental Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  body { background: #f8f9fa; }
  .container { max-width: 1000px; margin: 40px auto; }
  table {
    background: white;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
  }
  td, th {
    vertical-align: middle !important;
  }
  textarea {
    resize: vertical;
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
  <h2 class="mb-4"><i class="fa-solid fa-envelope-open-text me-2"></i>Incoming Requests</h2>

  <?php if ($result->num_rows > 0): ?>
    <table class="table table-hover">
      <thead class="table-primary">
        <tr>
          <th>Property</th>
          <th>Sender</th>
          <th>Message</th>
          <th>Sent At</th>
          <th>Status</th>
          <th>Reply</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($req = $result->fetch_assoc()): ?>
          <?php
            $status = strtolower($req['status']);
            $badgeClass = 'secondary';
            if ($status === 'pending') $badgeClass = 'warning';
            elseif ($status === 'accepted') $badgeClass = 'success';
            elseif ($status === 'rejected') $badgeClass = 'danger';
          ?>
          <tr>
            <td><?= htmlspecialchars($req['property_title']) ?></td>
            <td><?= htmlspecialchars($req['sender_name']) ?></td>
            <td>
              <?= !empty($req['message']) ? nl2br(htmlspecialchars($req['message'])) : '<em>No message provided</em>' ?>
            </td>
            <td><?= htmlspecialchars($req['sent_at']) ?></td>
            <td>
              <span class="badge bg-<?= $badgeClass ?> text-capitalize"><?= $status ?></span>
            </td>
            <td>
              <?php if ($status === 'pending'): ?>
                <form action="handle_request.php" method="POST">
                  <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                  <textarea name="reply" rows="2" class="form-control mb-2" placeholder="Write your reply..." required></textarea>
                  <button type="submit" name="send_reply" class="btn btn-sm btn-primary">Send Reply</button>
                </form>
              <?php else: ?>
                <?= $req['reply'] ? nl2br(htmlspecialchars($req['reply'])) : '<em>No reply</em>' ?>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($status === 'pending'): ?>
                <a href="handle_request.php?id=<?= $req['request_id'] ?>&action=accept" class="btn btn-success btn-sm me-1" title="Accept"><i class="fa-solid fa-check"></i></a>
                <a href="handle_request.php?id=<?= $req['request_id'] ?>&action=reject" class="btn btn-danger btn-sm" title="Reject"><i class="fa-solid fa-xmark"></i></a>
              <?php else: ?>
                <span class="text-muted">No actions</span>
              <?php endif; ?>
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
