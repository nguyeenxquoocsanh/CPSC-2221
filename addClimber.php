<?php
include "db.php";

$climberId = $_POST['climberId'];
$name = $_POST['name'];
$sex = $_POST['sex'];
$dob = $_POST['dob'];

// Insert into Climbers (personal info)
$sql1 = "INSERT INTO Climbers (ClimberID, Name, Sex, DOB)
         VALUES ($climberId, '$name', '$sex', '$dob')";

// Insert into Members (membership)
$sql2 = "INSERT INTO Members (ClimberID, DateJoined)
         VALUES ($climberId, CURDATE())";

if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
    echo "Climber added successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>