<?php
// Database configuration
$host = "localhost";        // Usually 'localhost'
$db_user = "root";          // Your DB username
$db_pass = "";              // Your DB password
$db_name = "studentsphere"; // Your database name

// Create connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set character set to UTF-8
$conn->set_charset("utf8");

// Now you can use $conn for all queries



?>
