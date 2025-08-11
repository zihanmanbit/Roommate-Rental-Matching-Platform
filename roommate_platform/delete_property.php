<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$property_id = intval($_GET['id']);
$owner_id = $_SESSION['user_id'];

// Delete property only if owned by user
$stmt = $conn->prepare("DELETE FROM properties WHERE property_id = ? AND owner_id = ?");
$stmt->bind_param("ii", $property_id, $owner_id);
$stmt->execute();

header("Location: dashboard.php?msg=property_deleted");
exit();
