<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'];
    $reviewed_user_id = $_POST['reviewed_user_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);
    $reviewer_id = $_SESSION['user_id'];

    // Check user exists
    $check_user_query = "SELECT user_id FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($check_user_query);
    $stmt->bind_param("i", $reviewer_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $_SESSION['error'] = "Invalid user.";
        header("Location: property_details.php?id=$property_id");
        exit();
    }
    $stmt->close();

    // Insert rating
    $insert_sql = "INSERT INTO ratings (reviewer_id, reviewed_user_id, rating, comment, rated_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iiis", $reviewer_id, $reviewed_user_id, $rating, $comment);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Rating submitted successfully.";
    } else {
        $_SESSION['error'] = "Failed to submit rating.";
    }
    $stmt->close();

    header("Location: property_details.php?id=$property_id");
    exit();
}
?>
