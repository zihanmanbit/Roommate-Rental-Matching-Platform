<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit;
}

$sender_id = $_SESSION['user_id'];
$property_id = $_POST['property_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if (!$property_id || $message === '') {
    $_SESSION['error'] = "Invalid request parameters or empty message.";
    header("Location: browse_properties.php");
    exit;
}

// Get owner_id (receiver) from the property
$stmt = $conn->prepare("SELECT owner_id FROM properties WHERE property_id = ?");
$stmt->bind_param("i", $property_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    $_SESSION['error'] = "Invalid property selected.";
    header("Location: browse_properties.php");
    exit;
}

$property = $res->fetch_assoc();
$receiver_id = $property['owner_id'];
$stmt->close();

// Check if request already exists
$check = $conn->prepare("SELECT request_id FROM requests WHERE sender_id = ? AND receiver_id = ? AND property_id = ?");
$check->bind_param("iii", $sender_id, $receiver_id, $property_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $_SESSION['error'] = "You have already sent a request for this property.";
    $check->close();
    header("Location: browse_properties.php");
    exit;
}
$check->close();

// Insert the new request with message
$insert = $conn->prepare("INSERT INTO requests (sender_id, receiver_id, property_id, message, status, sent_at) VALUES (?, ?, ?, ?, 'Pending', NOW())");
$insert->bind_param("iiis", $sender_id, $receiver_id, $property_id, $message);

if ($insert->execute()) {
    $_SESSION['success'] = "Request sent successfully.";
} else {
    $_SESSION['error'] = "Failed to send request.";
}
$insert->close();

header("Location: browse_properties.php");
exit;
