<?php
header('Content-Type: application/json');
include 'config.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (!empty($query) && strlen($query) >= 2) {
    $search_term = "%$query%";
    
    // Search events
    $stmt = $conn->prepare("SELECT title, 'event' as type FROM events WHERE title LIKE ? LIMIT 3");
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Search posts
    $stmt = $conn->prepare("SELECT title, 'post' as type FROM posts WHERE title LIKE ? LIMIT 3");
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Search clubs
    $stmt = $conn->prepare("SELECT name as title, 'club' as type FROM clubs WHERE name LIKE ? LIMIT 3");
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $clubs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $results = array_merge($events, $posts, $clubs);
}

echo json_encode($results);
?>