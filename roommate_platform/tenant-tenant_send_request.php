<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? null;
$property_id = $_POST['property_id'] ?? null;

// Validate receiver
if (!$receiver_id) {
    echo json_encode(['success' => false, 'message' => 'Missing receiver ID']);
    exit();
}

// Determine query based on whether property_id is set
if ($property_id) {
    $stmt = $conn->prepare("SELECT request_id FROM roommate_requests WHERE sender_id = ? AND receiver_id = ? AND property_id = ?");
    $stmt->bind_param("iii", $sender_id, $receiver_id, $property_id);
} else {
    $stmt = $conn->prepare("SELECT request_id FROM roommate_requests WHERE sender_id = ? AND receiver_id = ? AND property_id IS NULL");
    $stmt->bind_param("ii", $sender_id, $receiver_id);
}

$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already sent a request to this user.']);
    $stmt->close();
    exit();
}
$stmt->close();

// Insert new request
if ($property_id) {
    $stmt = $conn->prepare("INSERT INTO roommate_requests (sender_id, receiver_id, property_id, status, sent_at) VALUES (?, ?, ?, 'Pending', NOW())");
    $stmt->bind_param("iii", $sender_id, $receiver_id, $property_id);
} else {
    $stmt = $conn->prepare("INSERT INTO roommate_requests (sender_id, receiver_id, status, sent_at) VALUES (?, ?, 'Pending', NOW())");
    $stmt->bind_param("ii", $sender_id, $receiver_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Request sent successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send request.']);
}

$stmt->close();
