<?php
include 'auth_check.php';

if ($_SESSION['role'] !== 'manager') {
    header('Location: index.html');
    exit;
}

include 'connect.php';

// Fetch all classes for the filter dropdown
$classOptions = $conn->query("SELECT Class_ID, Class_Name, Event_Date FROM Classes ORDER BY Event_Date ASC");

// Get filter values from GET params
$filterClass = $_GET['class_id']    ?? '';
$filterDate  = $_GET['event_date']  ?? '';
$filterRole  = $_GET['role']        ?? '';

// Build the JOIN query dynamically based on filters
// Joins: Teaches + Climber + Classes (3-table join)
$sql = "
    SELECT 
        c.climberId,
        c.name        AS climber_name,
        c.sex,
        cl.Class_ID,
        cl.Class_Name,
        cl.Event_Date,
        t.role
    FROM Teaches t
    JOIN Climber c  ON t.climberId = c.climberId
    JOIN Classes cl ON t.classId   = cl.Class_ID
    WHERE 1=1
";

$params = [];
$types  = '';

if ($filterClass !== '') {
    $sql     .= " AND cl.Class_ID = ?";
    $params[] = $filterClass;
    $types   .= 'i';
}

if ($filterDate !== '') {
    $sql     .= " AND cl.Event_Date = ?";
    $params[] = $filterDate;
    $types   .= 's';
}

if ($filterRole !== '') {
    $sql     .= " AND t.role = ?";
    $params[] = $filterRole;
    $types   .= 's';
}

$sql .= " ORDER BY cl.Event_Date ASC, c.name ASC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$rows   = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get total count for summary
$total = count($rows);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Signups — Manager View</title>
    <link rel="stylesheet" href="unified.css">
    <style>
        body {
            background: #4a5f6f;
            min-height: 100vh;
            padding: 0 0 60px 0;
        }

        /* Top bar */
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

        /* Page wrapper */
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
        .page-subtitle code {
            background: rgba(255,255,255,0.1);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            color: #aef;
        }

        /* Filter card */
        .filter-card {
            background: white;
            border-radius: 10px;
            padding: 22px 26px;
            margin-bottom: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        .filter-card h2 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            margin-bottom: 16px;
        }
        .filter-row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 160px;
        }
        .filter-group label {
            font-size: 0.78rem;
            font-weight: bold;
            color: #555;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .filter-group select,
        .filter-group input {
            padding: 9px 12px;
            border: 1.5px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            color: #333;
            background: white;
            transition: border-color .2s;
        }
        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #3498db;
        }
        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        .btn-filter {
            padding: 9px 22px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background .2s;
            white-space: nowrap;
        }
        .btn-filter:hover { background: #1a252f; }
        .btn-reset {
            padding: 9px 16px;
            background: white;
            color: #888;
            border: 1.5px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all .2s;
            white-space: nowrap;
            text-decoration: none;
            display: inline-block;
        }
        .btn-reset:hover { border-color: #aaa; color: #333; }

        /* Summary bar */
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

        /* Results table */
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

        /* Role badge inline */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-student  { background: #d4edda; color: #155724; }
        .badge-instructor { background: #cce5ff; color: #004085; }
        .badge-other    { background: #f0f0f0; color: #555; }

        /* Sex display */
        .sex-label { color: #666; font-size: 0.85rem; }

        /* Empty state */
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

        /* Back link */
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

        <div class="page-title">Class Signup Report</div>
        <div class="page-subtitle">
            Join query: <code>Teaches ⋈ Climber ⋈ Classes</code> — showing all enrolled climbers with class details
        </div>

        <!-- Filter Form -->
        <div class="filter-card">
            <h2>Filter Results</h2>
            <form method="GET" action="ViewSignups.php">
                <div class="filter-row">

                    <div class="filter-group">
                        <label for="class_id">Class</label>
                        <select name="class_id" id="class_id">
                            <option value="">All Classes</option>
                            <?php
                            // Reset pointer in case already iterated
                            $classOptions->data_seek(0);
                            while ($cls = $classOptions->fetch_assoc()):
                                $selected = ($filterClass == $cls['Class_ID']) ? 'selected' : '';
                            ?>
                            <option value="<?= $cls['Class_ID'] ?>" <?= $selected ?>>
                                <?= htmlspecialchars($cls['Class_Name']) ?> (<?= $cls['Event_Date'] ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="event_date">Date</label>
                        <input type="date" name="event_date" id="event_date"
                               value="<?= htmlspecialchars($filterDate) ?>">
                    </div>

                    <div class="filter-group">
                        <label for="role">Role</label>
                        <select name="role" id="role">
                            <option value="">All Roles</option>
                            <option value="Student"          <?= $filterRole === 'Student'          ? 'selected' : '' ?>>Student</option>
                            <option value="Lead Instructor"  <?= $filterRole === 'Lead Instructor'  ? 'selected' : '' ?>>Lead Instructor</option>
                            <option value="Assistant Instructor" <?= $filterRole === 'Assistant Instructor' ? 'selected' : '' ?>>Assistant Instructor</option>
                            <option value="Climbing Coach"   <?= $filterRole === 'Climbing Coach'   ? 'selected' : '' ?>>Climbing Coach</option>
                            <option value="Safety Officer"   <?= $filterRole === 'Safety Officer'   ? 'selected' : '' ?>>Safety Officer</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">Apply</button>
                        <a href="ViewSignups.php" class="btn-reset">Reset</a>
                    </div>

                </div>
            </form>
        </div>

        <!-- Summary -->
        <div class="summary-bar">
            <span class="summary-count">
                Showing <strong><?= $total ?></strong> <?= $total === 1 ? 'record' : 'records' ?>
                <?php if ($filterClass || $filterDate || $filterRole): ?>
                    (filtered)
                <?php endif; ?>
            </span>
        </div>

        <!-- Results Table -->
        <div class="results-card">
            <?php if ($total > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Climber ID</th>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Class</th>
                        <th>Date</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row):
                        $gender = $row['sex'] === 'M' ? 'Male' : 'Female';
                        $role   = htmlspecialchars($row['role']);
                        $badgeClass = match(true) {
                            $row['role'] === 'Student'                                   => 'badge-student',
                            in_array($row['role'], ['Lead Instructor', 'Assistant Instructor', 'Climbing Coach']) => 'badge-instructor',
                            default                                                      => 'badge-other'
                        };
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['climberId']) ?></td>
                        <td><?= htmlspecialchars($row['climber_name']) ?></td>
                        <td class="sex-label"><?= $gender ?></td>
                        <td><?= htmlspecialchars($row['Class_Name']) ?></td>
                        <td><?= htmlspecialchars($row['Event_Date']) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= $role ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <div class="icon">📋</div>
                <p>No signups found<?= ($filterClass || $filterDate || $filterRole) ? ' for the selected filters.' : ' yet.' ?></p>
            </div>
            <?php endif; ?>
        </div>

        <a href="manager.php" class="back-link">← Back to Manager Dashboard</a>

    </div>

</body>
</html>
<?php $conn->close(); ?>