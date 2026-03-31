<?php
/*
Milestone 4

Haiden Murphy
Jauseff Dait
Nick Nguyen
Khushi

php script for finding gym members who use equiptment for the longer than the average time , 
accessed by a button on the manager html page
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
    echo "Above Median Calculation Successful <br><br>";
}

$query = "
    SELECT climberId, AVG(duration) AS avg_duration
    FROM Rents
    GROUP BY climberId
    HAVING AVG(duration) > (
    SELECT AVG(duration) FROM Rents
    )";

$above_avg = $conn->query($query);

if ($above_avg->num_rows > 0) {
    echo "<h3>Climbers with Above Average Rental Duration:</h3>";
    echo "<table border='1'>
    <tr>
        <th>Climber ID</th>
        <th>Average Duration (Hours) </th>
    </tr>";
            
while ($row = $above_avg->fetch_assoc()) {
    echo "
    <tr>
        <td>{$row['climberId']}</td>
        <td>{$row['avg_duration']}</td>
    </tr>";
}
    echo "</table>";
} else {
    echo "No climbers above average.";
}

$conn->close();
?>