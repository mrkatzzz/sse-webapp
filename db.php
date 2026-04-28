<?php
$host = 'localhost';
$db   = 'lab_assignment';
$user = 'root';
$pass = ''; // Default XAMPP password is empty

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>