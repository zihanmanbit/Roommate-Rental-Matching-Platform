<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM preferences WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_preferences = $stmt->get_result()->fetch_assoc();

if (!$user_preferences):
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Preferences Missing - Roommate & Rental Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
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
<div class="container mt-5">
  <div class="alert alert-warning text-center" role="alert">
    <h4 class="alert-heading">Preferences Not Found</h4>
    <p>You have not set your roommate preferences yet.</p>
    <hr>
    <a href="tenant_preference.php" class="btn btn-primary">
      <i class="fa-solid fa-sliders me-1"></i> Set Your Preferences
    </a>
  </div>
</div>
</body>
</html>
<?php
exit();
endif;

$sql = "SELECT * FROM preferences WHERE user_id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$potential_roommates = $stmt->get_result();

$compatibility_scores = [];

while ($potential = $potential_roommates->fetch_assoc()) {
    $score = 0;

    if ($user_preferences['gender_preference'] == $potential['gender_preference']) $score += 18;
    if ($user_preferences['smoking'] == $potential['smoking']) $score += 15;
    if ($user_preferences['cleanliness_level'] == $potential['cleanliness_level']) $score += 12;
    if ($user_preferences['pets_allowed'] == $potential['pets_allowed']) $score += 10;
    if ($user_preferences['budget_min'] <= $potential['budget_max'] && $user_preferences['budget_max'] >= $potential['budget_min']) $score += 25;
    if ($user_preferences['sleep_schedule'] == $potential['sleep_schedule']) $score += 6;
    if (stripos($potential['preferred_location'], $user_preferences['preferred_location']) !== false) $score += 15;

    $score = min($score, 100);

    $compatibility_scores[$potential['user_id']] = [
        'score' => $score,
        'details' => $potential
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Roommate Matches - Roommate & Rental Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  body { background: #f8f9fa; }
  .container { max-width: 900px; margin: 40px auto; }
  .match-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
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
  <h2 class="mb-4"><i class="fa-solid fa-users me-2"></i>Roommate Matches</h2>

  <?php if (empty($compatibility_scores)): ?>
    <p>No potential roommates found.</p>
  <?php else: ?>
    <?php foreach ($compatibility_scores as $matched_user_id => $data):
      $roommate = $data['details'];
      $score = $data['score'];

      // Fetch user info
      $stmt = $conn->prepare("SELECT name, email, contact FROM users WHERE user_id = ?");
      $stmt->bind_param("i", $matched_user_id);
      $stmt->execute();
      $user = $stmt->get_result()->fetch_assoc();

      // Check for existing roommate request
      $stmt = $conn->prepare("SELECT * FROM roommate_requests WHERE 
        (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
      $stmt->bind_param("iiii", $user_id, $matched_user_id, $matched_user_id, $user_id);
      $stmt->execute();
      $request_result = $stmt->get_result();
      $request = $request_result->fetch_assoc();
      $request_exists = $request_result->num_rows > 0;
    ?>
    <div class="match-card">
      <h4><?= htmlspecialchars($user['name']) ?> <small class="text-muted">(User ID: <?= $matched_user_id ?>)</small></h4>
      <p><strong>Compatibility Score:</strong> <?= $score ?>/100</p>
      <p><strong>Budget Range:</strong> $<?= htmlspecialchars($roommate['budget_min']) ?> - $<?= htmlspecialchars($roommate['budget_max']) ?></p>
      <p><strong>Pets Allowed:</strong> <?= $roommate['pets_allowed'] ? 'Yes' : 'No' ?></p>
      <p><strong>Smoking:</strong> <?= $roommate['smoking'] ? 'Yes' : 'No' ?></p>
      <p><strong>Cleanliness Level:</strong> <?= htmlspecialchars($roommate['cleanliness_level']) ?></p>
      <p><strong>Sleep Schedule:</strong> <?= htmlspecialchars($roommate['sleep_schedule']) ?></p>
      <p><strong>Preferred Location:</strong> <?= htmlspecialchars($roommate['preferred_location']) ?></p>

      <?php if (!$request_exists): ?>
        <form action="tenant-tenant_send_request.php" method="POST" class="mt-3">
          <input type="hidden" name="receiver_id" value="<?= $matched_user_id ?>">
          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-paper-plane me-1"></i> Send Request
          </button>
        </form>
      <?php else: ?>
        <p class="mt-3">
          <strong>Status:</strong> <?= ucfirst(htmlspecialchars($request['status'])) ?><br>
          <?php if ($request['status'] === 'Accepted'): ?>
            <strong>Contact:</strong> <?= htmlspecialchars($user['email']) ?> / <?= htmlspecialchars($user['contact']) ?>
          <?php endif; ?>
        </p>
        <?php if ($request['status'] === 'Pending' && $user_id === $request['receiver_id']): ?>
          <form action="handle_request.php" method="POST" class="d-flex gap-2 mt-2">
            <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
            <button type="submit" name="accept_request" class="btn btn-success btn-sm">Accept</button>
            <button type="submit" name="decline_request" class="btn btn-danger btn-sm">Decline</button>
          </form>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-secondary">
      <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
