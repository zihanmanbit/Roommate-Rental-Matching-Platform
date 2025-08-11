<?php
session_start();
require 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: browse_properties.php");
    exit();
}

$property_id = intval($_GET['id']);

// Fetch property and owner info
$sql = "SELECT p.*, u.name AS owner_name, u.email AS owner_email, u.user_id AS owner_id
        FROM properties p 
        JOIN users u ON p.owner_id = u.user_id 
        WHERE p.property_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: browse_properties.php");
    exit();
}

$property = $result->fetch_assoc();

// Fetch average rating
$rating_query = "SELECT AVG(rating) AS avg_rating FROM ratings WHERE reviewed_user_id = ?";
$rating_stmt = $conn->prepare($rating_query);
$rating_stmt->bind_param("i", $property['owner_id']);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();
$rating_data = $rating_result->fetch_assoc();
$avg_rating = round($rating_data['avg_rating'], 1);

// Fetch reviews
$reviews_query = "SELECT r.rating, r.comment, r.rated_at, u.name AS reviewer_name 
                  FROM ratings r 
                  JOIN users u ON r.reviewer_id = u.user_id 
                  WHERE r.reviewed_user_id = ? 
                  ORDER BY r.rated_at DESC";
$reviews_stmt = $conn->prepare($reviews_query);
$reviews_stmt->bind_param("i", $property['owner_id']);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Property Details - Roommate & Rental Platform</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body { background: #f8f9fa; }
    .container { max-width: 800px; margin: 40px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 25px rgba(0,0,0,0.05); }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="browse_properties.php"><i class="fa-solid fa-house-user me-2"></i>Roommate & Rental Platform</a>
    <div>
      <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2"><i class="fa-solid fa-dashboard me-1"></i> Dashboard</a>
      <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a>
    </div>
  </div>
</nav>

<div class="container">
  <h2 class="mb-4"><i class="fa-solid fa-building me-2"></i><?= htmlspecialchars($property['title']) ?></h2>

  <dl class="row">
    <dt class="col-sm-3">Description</dt>
    <dd class="col-sm-9"><?= nl2br(htmlspecialchars($property['description'])) ?></dd>

    <dt class="col-sm-3">Address</dt>
    <dd class="col-sm-9"><?= htmlspecialchars($property['address']) ?></dd>

    <dt class="col-sm-3">Location</dt>
    <dd class="col-sm-9"><?= htmlspecialchars($property['location']) ?></dd>

    <dt class="col-sm-3">Rent</dt>
    <dd class="col-sm-9">৳ <?= number_format($property['rent'], 2) ?></dd>

    <dt class="col-sm-3">Available From</dt>
    <dd class="col-sm-9"><?= htmlspecialchars($property['available_from']) ?></dd>

    <dt class="col-sm-3">Gender Preference</dt>
    <dd class="col-sm-9"><?= htmlspecialchars($property['gender_preference']) ?></dd>

    <dt class="col-sm-3">Amenities</dt>
    <dd class="col-sm-9"><?= htmlspecialchars($property['amenities']) ?></dd>

    <dt class="col-sm-3">Owner</dt>
    <dd class="col-sm-9"><?= htmlspecialchars($property['owner_name']) ?> (<?= htmlspecialchars($property['owner_email']) ?>)</dd>
  </dl>

  <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $property['owner_id']): ?>
    <div class="mt-5">
      <h4>Send Request to Owner</h4>
      <form action="send_request.php" method="POST">
        <input type="hidden" name="receiver_id" value="<?= $property['owner_id'] ?>">
        <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">

        <div class="mb-3">
          <label for="message" class="form-label">Message</label>
          <textarea name="message" id="message" rows="4" class="form-control" placeholder="Type your message here..." required></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
          <i class="fa-solid fa-paper-plane me-1"></i> Send Request
        </button>
      </form>
    </div>
  <?php endif; ?>

  <hr class="my-5">

  <h4 class="mb-3">Owner Rating</h4>
  <p><strong>Average Rating:</strong> <?= $avg_rating ?: "No ratings yet" ?>/5</p>

  <h5>Reviews:</h5>
  <?php if ($reviews_result->num_rows > 0): ?>
    <?php while ($review = $reviews_result->fetch_assoc()): ?>
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <h6><i class="fa-solid fa-star text-warning"></i> <?= $review['rating'] ?>/5</h6>
          <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
          <small class="text-muted">Reviewed by <?= htmlspecialchars($review['reviewer_name']) ?> on <?= $review['rated_at'] ?></small>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No reviews yet.</p>
  <?php endif; ?>

  <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $property['owner_id']): ?>
    <hr>
    <h4>Submit Your Rating</h4>
    <form action="submit_rating.php" method="POST" class="mb-5">
      <input type="hidden" name="property_id" value="<?= $property_id ?>">
      <input type="hidden" name="reviewed_user_id" value="<?= $property['owner_id'] ?>">

      <div class="mb-3">
        <label for="rating" class="form-label">Rating</label>
        <select name="rating" id="rating" class="form-select" required>
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
          <?php endfor; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="comment" class="form-label">Comment</label>
        <textarea name="comment" id="comment" rows="4" class="form-control" placeholder="Write your review here..."></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Submit Rating</button>
    </form>
  <?php endif; ?>

  <div class="mt-4 text-center">
    <a href="browse_properties.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to Properties</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
