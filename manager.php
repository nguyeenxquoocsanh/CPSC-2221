<?php
include 'auth_check.php';

if ($_SESSION['role'] !== 'manager') {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard — Rock Climbing Gym</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .topbar-user {
            background: #2c3e50;
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.88rem;
            color: #ddd;
        }
        .topbar-user span { color: white; font-weight: bold; }
        .topbar-user a {
            color: #f08080;
            text-decoration: none;
            font-weight: bold;
            transition: color .2s;
        }
        .topbar-user a:hover { color: white; }
        .role-badge {
            background: #c0392b;
            color: white;
            font-size: 0.72rem;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-left: 10px;
        }
        .manager-note {
            background: #fdf0ee;
            border-left: 4px solid #c0392b;
            padding: 10px 20px;
            margin: 10px 30px;
            font-size: 0.85rem;
            color: #555;
            border-radius: 0 6px 6px 0;
        }
    </style>
</head>
<body>

    <div class="topbar-user">
        <div>
            Welcome, <span><?= htmlspecialchars($_SESSION['climber_name']) ?></span>
            <span class="role-badge">Manager</span>
        </div>
        <a href="index.html">← Main Page</a>
    </div>

    <div class="course-header">
        <h3>CPSC 2221 — Jauseff · Khushi · Haiden · Nick</h3>
    </div>

    <div class="nav">
        <button onclick="window.location.href='manager.php'">Main</button>
        <button onclick="window.location.href='info.html'">Info</button>
    </div>

    <div class="hero-section">
        <div class="hero-content">
            <h1>MANAGER PORTAL</h1>
        </div>
    </div>

    <div class="manager-note">
        ⚠ You are logged in as <strong>Manager</strong>. You have full access including member management.
    </div>

    <div class="content-section">

        <div class="button-container">
            <!-- Manager-only -->
            <a href="AddClimber.html"><div class="btn">Add Member</div></a>
            <a href="ViewSignups.php"><div class="btn">View Signups</div></a>
            <a href="ViewWaitlist.php"><div class="btn">View Waitlist</div></a>
            <!-- Shared with staff -->
            <a href="ClassSignup.html"><div class="btn">Class Signup</div></a>
            <a href="SearchClimber.html"><div class="btn">Member Lookup</div></a>
            <a href="info.html"><div class="btn">Info</div></a>
        </div>

        <div class="search-box">
            <form action="search.php" method="GET">
                <input type="text" name="search" placeholder="Quick Member Lookup" required>
                <button type="submit">Search</button>
            </form>
        </div>

    </div>

</body>
</html>