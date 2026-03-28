<?php
include "db.php";

$climberId = $_POST['climberId'];
$classId = $_POST['classId'];

// Step 1: Find next queue number
$result = $conn->query("SELECT MAX(QueueNumber) AS maxQ 
                        FROM Waitlist_IsOn 
                        WHERE ClassID = $classId");

$row = $result->fetch_assoc();
$queueNumber = $row['maxQ'] + 1;

if ($queueNumber == NULL) {
    $queueNumber = 1;
}

// Step 2: Insert into Waitlist
$sql = "INSERT INTO Waitlist_IsOn (QueueNumber, ClassID, ClimberID)
        VALUES ($queueNumber, $classId, $climberId)";

if ($conn->query($sql) === TRUE) {
    echo "Successfully added to waitlist! Your position: " . $queueNumber;
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>