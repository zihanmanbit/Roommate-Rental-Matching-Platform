<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept_request'])) {
        $request_id = $_POST['request_id'];
        
        // Update request status to Accepted
        $stmt = $conn->prepare("UPDATE roommate_requests SET status = 'Accepted' WHERE request_id = ? AND receiver_id = ?");
        $stmt->bind_param("ii", $request_id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Request accepted! You can now communicate with the other tenant.";
        } else {
            $_SESSION['error'] = "Failed to accept request.";
        }
    }

    if (isset($_POST['decline_request'])) {
        $request_id = $_POST['request_id'];
        
        // Update request status to Declined
        $stmt = $conn->prepare("UPDATE roommate_requests SET status = 'Declined' WHERE request_id = ? AND receiver_id = ?");
        $stmt->bind_param("ii", $request_id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Request declined.";
        } else {
            $_SESSION['error'] = "Failed to decline request.";
        }
    }
}

header("Location: tenant-tenant_view_requests.php");
exit;
