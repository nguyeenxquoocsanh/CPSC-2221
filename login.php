<?php
include "db.php";

$climberId = $_POST['climberId'];

$sql = "SELECT * FROM Members WHERE ClimberID = $climberId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    header("Location: classsignup.html"); // redirect
    exit();
} else {
    echo "Invalid Climber ID";
}

$conn->close();
?>