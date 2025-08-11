<?php
session_start();
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $age = trim($_POST['age']);
    $gender = $_POST['gender'];
    $contact = trim($_POST['contact']);

    // Basic validation
    if (!$name) $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    if ($role !== 'tenant' && $role !== 'owner') $errors[] = "Please select a valid role.";
    if (!$age || !is_numeric($age) || $age < 1) $errors[] = "Valid age is required.";
    if (!in_array($gender, ['Male', 'Female', 'Other'])) $errors[] = "Please select a valid gender.";
    if (!$contact || !preg_match('/^\d{10,15}$/', $contact)) $errors[] = "Valid contact number is required.";

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already registered.";
        }
        $stmt->close();
    }

    // Insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, age, gender, contact) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiss", $name, $email, $hashed_password, $role, $age, $gender, $contact);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Registration failed: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Register - Roommate & Rental Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  body { background: #f8f9fa; }
  .form-container {
    max-width: 420px;
    margin: 40px auto;
    padding: 25px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0,0,0,0.05);
  }
</style>
</head>
<body>
<div class="form-container shadow-sm">
  <h2 class="mb-4 text-center"><i class="fa-solid fa-user-plus me-2"></i>Register</h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <div class="mb-3">
      <label for="name" class="form-label">Full Name</label>
      <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email address</label>
      <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
    </div>
    <div class="mb-3">
      <label for="age" class="form-label">Age</label>
      <input type="number" class="form-control" id="age" name="age" required value="<?= htmlspecialchars($_POST['age'] ?? '') ?>" />
    </div>
    <div class="mb-3">
      <label for="gender" class="form-label">Gender</label>
      <select class="form-select" id="gender" name="gender" required>
        <option value="" disabled <?= !isset($_POST['gender']) ? 'selected' : '' ?>>Select Gender</option>
        <option value="Male" <?= ($_POST['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= ($_POST['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
        <option value="Other" <?= ($_POST['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
      </select>
    </div>
    <div class="mb-3">
      <label for="contact" class="form-label">Contact Number</label>
      <input type="text" class="form-control" id="contact" name="contact" required value="<?= htmlspecialchars($_POST['contact'] ?? '') ?>" />
    </div>
    <div class="mb-3">
      <label for="role" class="form-label">Role</label>
      <select class="form-select" id="role" name="role" required>
        <option value="" disabled <?= !isset($_POST['role']) ? 'selected' : '' ?>>Select Role</option>
        <option value="tenant" <?= (($_POST['role'] ?? '') === 'tenant') ? 'selected' : '' ?>>Tenant</option>
        <option value="owner" <?= (($_POST['role'] ?? '') === 'owner') ? 'selected' : '' ?>>Owner</option>
      </select>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" required minlength="6" />
    </div>
    <div class="mb-3">
      <label for="confirm_password" class="form-label">Confirm Password</label>
      <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6" />
    </div>
    <button type="submit" class="btn btn-primary w-100">
      <i class="fa-solid fa-user-plus me-1"></i> Register
    </button>
  </form>

  <div class="mt-3 text-center">
    Already have an account? <a href="login.php">Login here</a>.
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
