<?php
include 'connect.php';

$climberId = $_POST['climber_id'];
$classId   = $_POST['class_id'];

$stmt = $conn->prepare("INSERT INTO Teaches (classId, climberId, role) VALUES (?, ?, 'Student')");
$stmt->bind_param("ii", $classId, $climberId);

if ($stmt->execute()) {
    $message = "Successfully signed up for the class!";
    $success = true;
} else {
    $message = "Signup failed: " . $stmt->error;
    $success = false;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Result — Rock Climbing Gym</title>
    <link rel="stylesheet" href="unified.css">
</head>
<body>
    <div class="container">
        <h1>Sign Up Result</h1>

        <?php if ($success): ?>
            <div class="message success"><?= $message ?></div>
        <?php else: ?>
            <div class="message error"><?= $message ?></div>
        <?php endif; ?>

        <div class="link-section">
            <a href="ClassSignup.html">Sign Up for Another Class</a>
            <a href="index.html">Back to Main</a>
        </div>
    </div>
</body>
</html>
