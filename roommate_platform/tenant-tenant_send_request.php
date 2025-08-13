<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php?error=unauthorized");
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? null;

// Validate receiver
if (!$receiver_id) {
    header("Location: roommate_connection.php?error=missing_receiver");
    exit();
}

// Check if a request has already been sent
$stmt = $conn->prepare("SELECT request_id FROM roommate_requests WHERE sender_id = ? AND receiver_id = ?");
$stmt->bind_param("ii", $sender_id, $receiver_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    header("Location: roommate_connection.php?error=already_sent");
    exit();
}
$stmt->close();

// Insert new request
$stmt = $conn->prepare("INSERT INTO roommate_requests (sender_id, receiver_id, status, sent_at) VALUES (?, ?, 'Pending', NOW())");
$stmt->bind_param("ii", $sender_id, $receiver_id);

if ($stmt->execute()) {
    header("Location: roommate_connection.php?success=request_sent");
} else {
    header("Location: roommate_connection.php?error=failed");
}

$stmt->close();
exit();
