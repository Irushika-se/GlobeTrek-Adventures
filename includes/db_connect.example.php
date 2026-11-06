<?php
// Database connection settings
$host = "localhost";
$user = "your_database_username";
$pass = "your_database_password";
$db   = "globetrek";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed. Check your configuration.");
}
?>