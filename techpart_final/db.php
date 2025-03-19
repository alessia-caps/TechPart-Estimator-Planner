<?php
$servername = "localhost";
$username = "root"; // Default user for XAMPP
$password = ""; // Default is empty
$database = "user_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
