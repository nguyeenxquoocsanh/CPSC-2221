<?php
include 'connect.php';

$id   = $_POST['climber_id'] ?? '';
$name = $_POST['name'] ?? '';
$sex  = $_POST['sex'] ?? '';
$dob  = $_POST['dob'] ?? '';

// Use prepared statement to prevent SQL injection
$sql  = "INSERT INTO Climber (climberId, name, sex, dob) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param("isss", $id, $name, $sex, $dob);

if ($stmt->execute()) {
    echo "Climber added successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>