<?php
session_start();
include 'connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $climberId = $_POST['climberId'];
    $password  = $_POST['password'];

    // Hardcoded accounts — no database table needed
    $accounts = [
        101 => ['password' => 'manager123', 'role' => 'manager'],
        103 => ['password' => 'staff123',   'role' => 'staff'],
    ];

    $id = (int) $climberId;

    if (!isset($accounts[$id]) || $password !== $accounts[$id]['password']) {
        $error = 'Invalid Climber ID or password.';
    } else {
        // Get the climber's name from the database
        $stmt = $conn->prepare("SELECT name FROM Climber WHERE climberId = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        $_SESSION['climber_id']   = $id;
        $_SESSION['climber_name'] = $row['name'];
        $_SESSION['role']         = $accounts[$id]['role'];

        if ($accounts[$id]['role'] == 'manager') {
            header('Location: manager.php');
        } else {
            header('Location: staff.php');
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rock Climbing Customer Management System | Log In</title>
  <link rel="stylesheet" href="unified.css">
</head>
<body>
  <div class="container">
    <h1>Log In</h1>
    <?php if ($error != ''): ?>
    <p style="color:#c0392b; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="climberId">Climber ID:</label>
        <input type="text" id="climberId" name="climberId" placeholder="Enter your Climber ID" required>
      </div>
      <div class="form-group">
        <label for="pw">Password:</label>
        <input type="password" id="pw" name="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn">Log In</button>
    </form>
    <p style="margin-top:16px; font-size:0.9rem;">
      <a href="index.html">← Back to Main Page</a>
    </p>
  </div>
</body>
</html>
