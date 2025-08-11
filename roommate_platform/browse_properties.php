<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch filter inputs
$location = $_GET['location'] ?? '';
$max_rent = $_GET['max_rent'] ?? '';
$gender_pref = $_GET['gender_preference'] ?? '';

$sql = "SELECT p.property_id, p.title, p.description, p.rent, p.location, p.gender_preference, p.available_from, p.owner_id, u.name AS owner_name
        FROM properties p
        JOIN users u ON p.owner_id = u.user_id
        WHERE p.status = 'Available'";

$params = [];
$types = "";

if ($location !== '') {
    $sql .= " AND p.location LIKE ?";
    $params[] = "%$location%";
    $types .= "s";
}
if ($max_rent !== '' && is_numeric($max_rent)) {
    $sql .= " AND p.rent <= ?";
    $params[] = $max_rent;
    $types .= "d";
}
if ($gender_pref !== '' && in_array($gender_pref, ['Male', 'Female', 'Any'])) {
    $sql .= " AND p.gender_preference = ?";
    $params[] = $gender_pref;
    $types .= "s";
}

$sql .= " ORDER BY p.available_from ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Browse Properties - Roommate & Rental Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  body { background: #f8f9fa; }
  .container { max-width: 900px; margin: 40px auto; }
  .property-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
    padding: 20px;
    margin-bottom: 20px;
    transition: box-shadow 0.3s ease;
  }
  .property-card:hover {
    box-shadow: 0 0 25px rgba(0,0,0,0.15);
  }
  .btn-details {
    white-space: nowrap;
  }
  form.filter-form {
    margin-bottom: 25px;
  }
  form.filter-form input, form.filter-form select {
    max-width: 200px;
    margin-right: 15px;
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
  <h2 class="mb-4"><i class="fa-solid fa-building me-2"></i>Available Properties</h2>

  <!-- Filter Form -->
  <form method="GET" action="browse_properties.php" class="filter-form d-flex flex-wrap align-items-center gap-2">
    <input type="text" name="location" class="form-control" placeholder="Location" value="<?= htmlspecialchars($location) ?>">
    <input type="number" step="0.01" name="max_rent" class="form-control" placeholder="Max Rent" value="<?= htmlspecialchars($max_rent) ?>">
    <select name="gender_preference" class="form-select">
      <option value="">Gender Preference</option>
      <option value="Male" <?= ($gender_pref === 'Male') ? 'selected' : '' ?>>Male</option>
      <option value="Female" <?= ($gender_pref === 'Female') ? 'selected' : '' ?>>Female</option>
      <option value="Any" <?= ($gender_pref === 'Any') ? 'selected' : '' ?>>Any</option>
    </select>
    <button type="submit" class="btn btn-primary">Filter</button>
  </form>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($property = $result->fetch_assoc()): ?>
      <div class="property-card">
        <h4><?= htmlspecialchars($property['title']) ?></h4>
        <p><strong>Location:</strong> <?= htmlspecialchars($property['location']) ?></p>
        <p><strong>Rent:</strong> BDT <?= htmlspecialchars(number_format($property['rent'], 2)) ?></p>
        <p><strong>Available From:</strong> <?= htmlspecialchars($property['available_from']) ?></p>
        <p><strong>Gender Preference:</strong> <?= htmlspecialchars($property['gender_preference']) ?></p>
        <p><strong>Owner:</strong> <?= htmlspecialchars($property['owner_name']) ?></p>
        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($property['description'])) ?></p>

        <?php
            $avg_rating_query = "SELECT AVG(rating) AS avg_rating FROM ratings WHERE reviewed_user_id = ?";
            $rating_stmt = $conn->prepare($avg_rating_query);
            $rating_stmt->bind_param("i", $property['owner_id']);
            $rating_stmt->execute();
            $rating_result = $rating_stmt->get_result();
            $rating_data = $rating_result->fetch_assoc();
            $avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : null;
        ?>
        <p><strong>Average Rating:</strong> <?= $avg_rating ?? "No ratings yet" ?>/5</p>

        <div class="d-flex flex-wrap gap-2 mt-3">
          <a href="property_details.php?id=<?= $property['property_id'] ?>" class="btn btn-primary btn-sm btn-details">
            <i class="fa-solid fa-info-circle me-1"></i> View Details
          </a>

          <?php if ($user_id == $property['owner_id']): ?>
            <a href="edit_property.php?id=<?= $property['property_id'] ?>" class="btn btn-warning btn-sm">
              <i class="fa-solid fa-pen-to-square me-1"></i> Edit
            </a>
            <a href="delete_property.php?id=<?= $property['property_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this property?');">
              <i class="fa-solid fa-trash me-1"></i> Delete
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No properties found.</p>
  <?php endif; ?>

  <div class="mt-3 text-center">
    <a href="dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
