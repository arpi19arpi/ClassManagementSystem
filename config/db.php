<?php
$host = 'localhost';
$port = 8889;        // MySQL port for MAMP
$user = 'root';
$pass = 'root';
$db   = 'ClassManagementSystem';

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

