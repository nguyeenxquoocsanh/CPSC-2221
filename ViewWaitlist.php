<?php
include 'auth_check.php';

if ($_SESSION['role'] !== 'manager') {
    header('Location: index.html');
    exit;
}

include 'connect.php';

$sql = "
    SELECT
        w.QueueNum,
        w.classId,
        cl.Class_Name,
        wi.climberId,
        c.name AS climber_name
    FROM Waitlist_IsOn wi
    JOIN Waitlist w   ON wi.QueueNum = w.QueueNum AND wi.classId = w.classId
    JOIN Classes cl   ON w.classId   = cl.Class_ID
    JOIN Climber c    ON wi.climberId = c.climberId
    ORDER BY w.classId, w.QueueNum
";

$result = $conn->query($sql);
$rows   = $result->fetch_all(MYSQLI_ASSOC);
$total  = count($rows);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Waitlist — Manager View</title>
    <link rel="stylesheet" href="unified.css">
    <style>
        body {
            background: #4a5f6f;
            min-height: 100vh;
            padding: 0 0 60px 0;
        }

        .topbar {
            background: #2c3e50;
            padding: 12px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .topbar-title {
            color: white;
            font-weight: bold;
            font-size: 1rem;
            letter-spacing: 1px;
        }
        .topbar a {
            color: #aaa;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color .2s;
        }
        .topbar a:hover { color: white; }
        .topbar .logout { color: #f08080; margin-left: 20px; }

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

        .page {
            max-width: 900px;
            margin: 36px auto;
            padding: 0 20px;
        }

        .page-title {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 6px;
            letter-spacing: 1px;
        }
        .page-subtitle {
            color: #cdd;
            font-size: 0.85rem;
            margin-bottom: 24px;
        }

        .summary-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .summary-count {
            color: white;
            font-size: 0.88rem;
        }
        .summary-count strong { color: #aef; }

        .results-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: #2c3e50;
            color: white;
        }
        thead th {
            padding: 13px 16px;
            text-align: left;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background .15s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8fbff; }
        tbody td {
            padding: 13px 16px;
            font-size: 0.9rem;
            color: #333;
        }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #aaa;
        }
        .empty-state .icon {
            font-size: 2.5rem;
            margin-bottom: 12px;
        }
        .empty-state p { font-size: 0.95rem; }

        .back-link {
            display: inline-block;
            margin-top: 24px;
            color: #adf;
            text-decoration: none;
            font-size: 0.88rem;
            transition: color .2s;
        }
        .back-link:hover { color: white; }
    </style>
</head>
<body>

    <div class="topbar">
        <div>
            <span class="topbar-title">⛰ Summit Gym</span>
            <span class="role-badge">Manager</span>
        </div>
        <div>
            <a href="manager.php">← Dashboard</a>
            <a href="index.html" class="logout">← Main Page</a>
        </div>
    </div>

    <div class="page">

        <div class="page-title">Class Waitlist</div>
        <div class="page-subtitle">Climbers waiting for a spot in a class, ordered by class and queue position.</div>

        <div class="summary-bar">
            <span class="summary-count">
                Showing <strong><?= $total ?></strong> <?= $total === 1 ? 'entry' : 'entries' ?>
            </span>
        </div>

        <div class="results-card">
            <?php if ($total > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Queue #</th>
                        <th>Class ID</th>
                        <th>Class Name</th>
                        <th>Climber ID</th>
                        <th>Climber Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['QueueNum']) ?></td>
                        <td><?= htmlspecialchars($row['classId']) ?></td>
                        <td><?= htmlspecialchars($row['Class_Name']) ?></td>
                        <td><?= htmlspecialchars($row['climberId']) ?></td>
                        <td><?= htmlspecialchars($row['climber_name']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <div class="icon">📋</div>
                <p>No climbers are currently on the waitlist.</p>
            </div>
            <?php endif; ?>
        </div>

        <a href="manager.php" class="back-link">← Back to Manager Dashboard</a>

    </div>

</body>
</html>
<?php $conn->close(); ?>
