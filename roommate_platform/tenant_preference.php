<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch existing preferences if any
$stmt = $conn->prepare("SELECT * FROM preferences WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$preference = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $budget_min = $_POST['budget_min'];
    $budget_max = $_POST['budget_max'];
    $pets_allowed = isset($_POST['pets_allowed']) ? 1 : 0;
    $smoking = isset($_POST['smoking']) ? 1 : 0;
    $cleanliness_level = $_POST['cleanliness_level'];
    $sleep_schedule = $_POST['sleep_schedule'];
    $preferred_location = $_POST['preferred_location'];
    $gender_preference = $_POST['gender_preference'];

    if ($preference) {
        $stmt = $conn->prepare("UPDATE preferences SET budget_min=?, budget_max=?, pets_allowed=?, smoking=?, cleanliness_level=?, sleep_schedule=?, preferred_location=?, gender_preference=? WHERE user_id=?");
        $stmt->bind_param("ddiissssi", $budget_min, $budget_max, $pets_allowed, $smoking, $cleanliness_level, $sleep_schedule, $preferred_location, $gender_preference, $user_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO preferences (user_id, budget_min, budget_max, pets_allowed, smoking, cleanliness_level, sleep_schedule, preferred_location, gender_preference) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iddiissss", $user_id, $budget_min, $budget_max, $pets_allowed, $smoking, $cleanliness_level, $sleep_schedule, $preferred_location, $gender_preference);
    }

    if ($stmt->execute()) {
        header("Location: tenant_preference.php?success=1");
        exit();
    } else {
        $error = "Failed to save preferences.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tenant Preferences</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body { background-color: #f8f9fa; }
    .container { max-width: 700px; margin-top: 50px; }
    .form-section {
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 20px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">
      <i class="fa-solid fa-house-user me-2"></i>Roommate & Rental Platform
    </a>
    <div>
      <a href="logout.php" class="btn btn-outline-light btn-sm">
        <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
      </a>
    </div>
  </div>
</nav>

<div class="container">
  <h2 class="text-center mb-4"><i class="fa-solid fa-sliders me-2"></i>Set Your Preferences</h2>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success text-center">Preferences saved successfully!</div>
  <?php elseif (isset($error)): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="post" class="form-section">
    <div class="row mb-3">
      <div class="col">
        <label class="form-label">Budget Min</label>
        <input type="number" name="budget_min" class="form-control" value="<?= $preference['budget_min'] ?? '' ?>" required>
      </div>
      <div class="col">
        <label class="form-label">Budget Max</label>
        <input type="number" name="budget_max" class="form-control" value="<?= $preference['budget_max'] ?? '' ?>" required>
      </div>
    </div>

    <div class="form-check mb-3">
      <input type="checkbox" name="pets_allowed" class="form-check-input" id="pets_allowed" <?= ($preference && $preference['pets_allowed']) ? 'checked' : '' ?>>
      <label class="form-check-label" for="pets_allowed">Pets Allowed</label>
    </div>

    <div class="form-check mb-3">
      <input type="checkbox" name="smoking" class="form-check-input" id="smoking" <?= ($preference && $preference['smoking']) ? 'checked' : '' ?>>
      <label class="form-check-label" for="smoking">Smoking</label>
    </div>

    <div class="mb-3">
      <label class="form-label">Cleanliness Level (1–5)</label>
      <select name="cleanliness_level" class="form-select" required>
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <option value="<?= $i ?>" <?= ($preference && $preference['cleanliness_level'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Sleep Schedule</label>
      <select name="sleep_schedule" class="form-select" required>
        <option value="Early Bird" <?= ($preference && $preference['sleep_schedule'] === 'Early Bird') ? 'selected' : '' ?>>Early Bird</option>
        <option value="Night Owl" <?= ($preference && $preference['sleep_schedule'] === 'Night Owl') ? 'selected' : '' ?>>Night Owl</option>
        <option value="Flexible" <?= ($preference && $preference['sleep_schedule'] === 'Flexible') ? 'selected' : '' ?>>Flexible</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Preferred Location</label>
      <input type="text" name="preferred_location" class="form-control" value="<?= $preference['preferred_location'] ?? '' ?>" required>
    </div>

    <div class="mb-4">
      <label class="form-label">Gender Preference</label>
      <select name="gender_preference" class="form-select" required>
        <option value="Any" <?= ($preference && $preference['gender_preference'] === 'Any') ? 'selected' : '' ?>>Any</option>
        <option value="Male" <?= ($preference && $preference['gender_preference'] === 'Male') ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= ($preference && $preference['gender_preference'] === 'Female') ? 'selected' : '' ?>>Female</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-save me-1"></i> Save Preferences</button>
  </form>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-secondary">
      <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
