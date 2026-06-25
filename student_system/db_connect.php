<?php
// Database configuration credentials
$host     = 'localhost';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password
$dbname   = 'student_management';

// Initialize MySQLi connection using the object-oriented approach
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection established successfully
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 to ensure proper handling of special characters
$conn->set_charset("utf8mb4");
?>