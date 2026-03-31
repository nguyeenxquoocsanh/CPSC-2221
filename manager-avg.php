<?php
/*
Milestone 4

Haiden Murphy
Jauseff Dait
Nick Nguyen
Khushi

php script for finding the average rental time of equiptment, accessed by a button on the manager html page
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
    echo "Median Rental Calculation Successful <br><br>";
}

$query = "SELECT AVG(duration) AS avg_duration FROM Rents";
$avg = $conn->query($query);

if ($avg->num_rows > 0) {
    $row = $avg->fetch_assoc();
    echo "<h3>Average Rental Duration: " . $row['avg_duration'] . "</h3>";
} else {
    echo "No results found.";
}

$conn->close();
?>