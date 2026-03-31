<?php
include 'connect.php';

$search = trim($_GET['search'] ?? '');

// Guard: redirect back if search is empty
if ($search === '') {
    header('Location: SearchClimber.html');
    exit;
}

$like = "%" . $search . "%";

// Table: Climber, columns: climberId, name, sex, dob (matches ClimbingGymRevised.sql)
$sql  = "SELECT climberId, name, sex, dob FROM Climber WHERE name LIKE ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results — Rock Climbing Gym</title>
    <link rel="stylesheet" href="unified.css">
    <style>
        body { background: #4a5f6f; display: flex; align-items: flex-start; justify-content: center; min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 600px; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align:center; margin-bottom:6px;">Search Results</h1>
        <p style="text-align:center; color:#eee; font-size:0.88rem; margin-bottom:24px;">
            Results for: <strong style="color:white;"><?= htmlspecialchars($search) ?></strong>
        </p>

        <?php if ($result->num_rows > 0): ?>
            <table class="climber-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Date of Birth</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['climberId']) ?></td>
                        <td><?= htmlspecialchars($row['name'])      ?></td>
                        <td><?= $row['sex'] === 'M' ? 'Male' : 'Female' ?></td>
                        <td><?= htmlspecialchars($row['dob'])       ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="message error">
                No members found matching "<?= htmlspecialchars($search) ?>".
            </div>
        <?php endif; ?>

        <div class="link-section">
            <a href="SearchClimber.html">Search Again</a>
            <a href="index.html">Back to Main</a>
        </div>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>