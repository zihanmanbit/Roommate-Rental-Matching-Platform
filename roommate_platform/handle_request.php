<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// --- Handle POST: Sending reply ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['reply'])) {
    $request_id = intval($_POST['request_id']);
    $reply = trim($_POST['reply']);

    // Update the request with reply and mark as 'replied'
    $stmt = $conn->prepare("UPDATE requests SET status = 'Replied', reply = ? WHERE request_id = ? AND receiver_id = ?");
    $stmt->bind_param("sii", $reply, $request_id, $owner_id);

    if ($stmt->execute()) {
        header("Location: view_requests.php?msg=reply_sent");
        exit();
    } else {
        echo "Error updating request: " . $stmt->error;
        exit();
    }
}

// --- Handle GET: Accept or Reject ---
if (isset($_GET['id'], $_GET['action'])) {
    $request_id = intval($_GET['id']);
    $action = $_GET['action'];

    if (!in_array($action, ['accept', 'reject'])) {
        header("Location: view_requests.php");
        exit();
    }

    // Verify that the request belongs to this owner
    $sql = "SELECT r.request_id FROM requests r 
            JOIN properties p ON r.property_id = p.property_id 
            WHERE r.request_id = ? AND p.owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $request_id, $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        header("Location: view_requests.php");
        exit();
    }

    $status = ($action === 'accept') ? 'Accepted' : 'Rejected';

    $update = $conn->prepare("UPDATE requests SET status = ? WHERE request_id = ?");
    $update->bind_param("si", $status, $request_id);
    $update->execute();

    header("Location: view_requests.php?msg=request_updated");
    exit();
}

// If neither POST nor valid GET, redirect back
header("Location: view_requests.php");
exit();
