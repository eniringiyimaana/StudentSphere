<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['image_id'])) {
    $image_id = intval($_POST['image_id']);
    
    // Get image filename
    $stmt = $conn->prepare("SELECT image_path FROM post_images WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();
    
    if ($image) {
        // Delete from database
        $delete_stmt = $conn->prepare("DELETE FROM post_images WHERE id = ?");
        $delete_stmt->bind_param("i", $image_id);
        
        if ($delete_stmt->execute()) {
            // Delete physical file
            $file_path = "../uploads/posts/gallery/" . $image['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Image not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}