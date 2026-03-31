<?php
$conn = new mysqli("localhost", "root", "root", "Rock Climbing Customer Management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>