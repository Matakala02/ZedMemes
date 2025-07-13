<?php
$host = 'localhost';           // XAMPP default host
$user = 'root';                // XAMPP default user
$password = '';                // XAMPP default password (empty by default)
$dbname = 'zedmemes_db';       // The database you created in phpMyAdmin

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
