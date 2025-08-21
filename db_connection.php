<?php
// Define database credentials
$host = 'localhost'; // or your database host
$username = 'root'; // your database username (default is 'root' for localhost)
$password = ''; // your database password (default is empty for localhost)
$db_name = 'academic_toolkit'; // your database name

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    // Optional debug message to confirm connection
    // echo "✅ Database connection successful.";
}
?>
