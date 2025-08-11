<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$errors = [];
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$property_id = intval($_GET['id']);

// Fetch existing property to edit
$stmt = $conn->prepare("SELECT * FROM properties WHERE property_id = ? AND owner_id = ?");
$stmt->bind_param("ii", $property_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: dashboard.php");
    exit();
}

$property = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $address = trim($_POST['address']);
    $location = trim($_POST['location']);
    $rent = floatval($_POST['rent']);
    $available_from = $_POST['available_from'];
    $gender_preference = $_POST['gender_preference'];
    $amenities = trim($_POST['amenities']);

    if (!$title) $errors[] = "Property title is required.";
    if (!$description) $errors[] = "Description is required.";
    if (!$address) $errors[] = "Address is required.";
    if (!$location) $errors[] = "Location is required.";
    if ($rent <= 0) $errors[] = "Rent must be a positive number.";
    if (!$available_from) $errors[] = "Available From date is required.";
    if (!in_array($gender_preference, ['Male', 'Female', 'Any'])) $errors[] = "Select a valid gender preference.";

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE properties SET title=?, description=?, address=?, location=?, rent=?, available_from=?, gender_preference=?, amenities=? WHERE property_id=? AND owner_id=?");
        $stmt->bind_param("ssssdsssii", $title, $description, $address, $location, $rent, $available_from, $gender_preference, $amenities, $property_id, $owner_id);

        if ($stmt->execute()) {
            $success = "Property updated successfully!";
            // Refresh property data
            $property = [
                'title' => $title,
                'description' => $description,
                'address' => $address,
                'location' => $location,
                'rent' => $rent,
                'available_from' => $available_from,
                'gender_preference' => $gender_preference,
                'amenities' => $amenities
            ];
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Property - Roommate & Rental Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  body { background: #f8f9fa; }
  .container { max-width: 700px; margin: 40px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.05);}
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
  <h2 class="mb-4"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Property</h2>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <div class="mb-3">
      <label for="title" class="form-label">Property Title</label>
      <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($property['title']) ?>" />
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($property['description']) ?></textarea>
    </div>
    <div class="mb-3">
      <label for="address" class="form-label">Address</label>
      <input type="text" class="form-control" id="address" name="address" required value="<?= htmlspecialchars($property['address']) ?>" />
    </div>
    <div class="mb-3">
      <label for="location" class="form-label">Location (e.g., Dhaka area)</label>
      <input type="text" class="form-control" id="location" name="location" required value="<?= htmlspecialchars($property['location']) ?>" />
    </div>
    <div class="mb-3">
      <label for="rent" class="form-label">Rent (BDT)</label>
      <input type="number" step="0.01" class="form-control" id="rent" name="rent" required value="<?= htmlspecialchars($property['rent']) ?>" />
    </div>
    <div class="mb-3">
      <label for="available_from" class="form-label">Available From</label>
      <input type="date" class="form-control" id="available_from" name="available_from" required value="<?= htmlspecialchars($property['available_from']) ?>" />
    </div>
    <div class="mb-3">
      <label for="gender_preference" class="form-label">Gender Preference</label>
      <select class="form-select" id="gender_preference" name="gender_preference" required>
        <?php
          $options = ['Male', 'Female', 'Any'];
          foreach ($options as $opt) {
              $selected = ($property['gender_preference'] === $opt) ? 'selected' : '';
              echo "<option value=\"$opt\" $selected>$opt</option>";
          }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label for="amenities" class="form-label">Amenities (comma separated)</label>
      <textarea class="form-control" id="amenities" name="amenities" rows="2"><?= htmlspecialchars($property['amenities']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-save me-1"></i> Save Changes</button>
  </form>

  <div class="mt-4 text-center">
    <a href="browse_properties.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to Properties</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
