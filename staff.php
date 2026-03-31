<?php
include 'auth_check.php';

if ($_SESSION['role'] !== 'staff') {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard — Rock Climbing Gym</title>
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
            background: #2980b9;
            color: white;
            font-size: 0.72rem;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-left: 10px;
        }
    </style>
</head>
<body>

    <div class="topbar-user">
        <div>
            Welcome, <span><?= htmlspecialchars($_SESSION['climber_name']) ?></span>
            <span class="role-badge">Staff</span>
        </div>
        <a href="index.html">← Main Page</a>
    </div>

    <div class="course-header">
        <h3>CPSC 2221 — Jauseff · Khushi · Haiden · Nick</h3>
    </div>

    <div class="hero-section">
        <div class="hero-content">
            <h1>STAFF PORTAL</h1>
        </div>
    </div>

    <div class="content-section">
        <div class="button-container">
            <a href="ClassSignup.html"><div class="btn">Class Signup</div></a>
            <a href="SearchClimber.html"><div class="btn">Search Climber</div></a>
        </div>
    </div>

</body>
</html>
