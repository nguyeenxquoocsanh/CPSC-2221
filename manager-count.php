<?php
/*
Milestone 4

Haiden Murphy
Jauseff Dait
Nick Nguyen
Khushi

php script for counting members of the gym, accessed by a button on the manager html page
*/

$servername = "localhost";
$username = "root"; 
$password = "root"; 
$database = "Rock Climbing Customer Management";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  
}
else {
    echo "Member Count Successful <br><br>";
}

$query = "SELECT COUNT(*) AS total_members FROM Members";
$count = $conn->query($query);

if ($count->num_rows > 0) {
    $row = $count->fetch_assoc();
    echo "<h3>Total Members: " . $row['total_members'] . "</h3>";
} else {
    echo "No results found.";
}

$conn->close();
?>