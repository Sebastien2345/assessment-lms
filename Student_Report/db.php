<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pup_lms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Set UTF-8 encoding
$conn->set_charset("utf8mb4");
?>
