<?php
session_start();
include 'config.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

// Check if slide ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_slides.php?error=Slide ID not provided");
    exit();
}

$id = intval($_GET['id']);

// Delete the slide
$stmt = $conn->prepare("DELETE FROM slides WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: manage_slides.php?success=Slide deleted successfully");
} else {
    header("Location: manage_slides.php?error=Error deleting slide: " . $conn->error);
}

$stmt->close();
exit();
?>