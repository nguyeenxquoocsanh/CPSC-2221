<?php
include 'connect.php';

$climberId = intval($_POST['climberId'] ?? 0);

if ($climberId <= 0) {
    header('Location: SearchClimber.html');
    exit;
}

$stmt = $conn->prepare("SELECT climberId, name, sex, dob FROM Climber WHERE climberId = ?");
$stmt->bind_param("i", $climberId);
$stmt->execute();
$result = $stmt->get_result();
$row    = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Profile — Rock Climbing Gym</title>
    <link rel="stylesheet" href="unified.css">
    <style>
        body { background: #4a5f6f; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 440px; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align:center; margin-bottom:24px;">Member Profile</h1>

        <?php if ($row): ?>
            <?php
                $dob    = new DateTime($row['dob']);
                $age    = (new DateTime())->diff($dob)->y;
                $gender = $row['sex'] === 'M' ? 'Male' : 'Female';
            ?>
            <table class="climber-table">
                <thead>
                    <tr><th colspan="2">Climber #<?= htmlspecialchars($row['climberId']) ?></th></tr>
                </thead>
                <tbody>
                    <tr><td><strong>Name</strong></td>      <td><?= htmlspecialchars($row['name']) ?></td></tr>
                    <tr><td><strong>Gender</strong></td>    <td><?= $gender ?></td></tr>
                    <tr><td><strong>Age</strong></td>       <td><?= $age ?> years old</td></tr>
                    <tr><td><strong>Date of Birth</strong></td><td><?= htmlspecialchars($row['dob']) ?></td></tr>
                </tbody>
            </table>
        <?php else: ?>
            <div class="message error">
                No member found with ID: <strong><?= htmlspecialchars($climberId) ?></strong>
            </div>
        <?php endif; ?>

        <div class="link-section">
            <a href="SearchClimber.html">Search Again</a>
            <a href="index.html">Back to Main</a>
        </div>
    </div>
</body>
</html>