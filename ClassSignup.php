<?php
include 'connect.php';

// Handle form submission
$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $climberId = $_POST['climber_id'] ?? '';
    $classId   = $_POST['class_id']   ?? '';

    if ($climberId == '' || $classId == '') {
        $message = "Please select a class and enter your Climber ID.";
        $success = false;
    } else {
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
    }
}

// Load classes for the selected month
$month = (int)($_GET['month'] ?? date('m'));
$year  = (int)($_GET['year']  ?? date('Y'));

$stmt = $conn->prepare(
    "SELECT Class_ID, Class_Name, Event_Date FROM Classes
     WHERE MONTH(Event_Date) = ? AND YEAR(Event_Date) = ?
     ORDER BY Event_Date ASC"
);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = [
        'id'         => $row['Class_ID'],
        'name'       => $row['Class_Name'],
        'event_date' => $row['Event_Date'],
        'day'        => (int) substr($row['Event_Date'], 8, 2)
    ];
}
$stmt->close();
$conn->close();

// Prev / next month links
$prevMonth = $month - 1;
$prevYear  = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $month + 1;
$nextYear  = $year;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Sign Up — Rock Climbing Gym</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #f2ede8; min-height: 100vh; color: #1a1a1a; }

        .topbar { background: #1a1a1a; color: white; padding: 14px 32px; display: flex; align-items: center; justify-content: space-between; }
        .topbar-title { font-family: 'Bebas Neue', sans-serif; font-size: 1.5rem; letter-spacing: 2px; }
        .topbar a { color: #aaa; text-decoration: none; font-size: 0.85rem; }
        .topbar a:hover { color: white; }

        .page { max-width: 980px; margin: 40px auto; padding: 0 20px 60px; display: grid; grid-template-columns: 1fr 1fr; gap: 28px; }
        @media (max-width: 700px) { .page { grid-template-columns: 1fr; } }

        .panel { background: white; border-radius: 12px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
        .panel-title { font-family: 'Bebas Neue', sans-serif; font-size: 1.4rem; letter-spacing: 1.5px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e8e2db; }

        /* Calendar */
        .cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .cal-header a { border: 1.5px solid #e8e2db; color: #555; width: 34px; height: 34px; border-radius: 50%; font-size: 1rem; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all .2s; }
        .cal-header a:hover { background: #1a1a1a; color: white; border-color: #1a1a1a; }
        #monthYear { font-weight: 600; font-size: 1rem; }

        .cal-days-row { display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; margin-bottom: 6px; }
        .cal-days-row div { font-size: 0.72rem; font-weight: 600; color: #aaa; padding-bottom: 6px; text-transform: uppercase; }

        #dates { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
        .date { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 0.85rem; cursor: pointer; transition: all .15s; position: relative; }
        .date:hover:not(.inactive) { background: #e8e2db; }
        .date.inactive { color: #ccc; pointer-events: none; }
        .date.today { font-weight: 700; color: #c0392b; }
        .date.selected { background: #1a1a1a !important; color: white; }
        .date.has-class::after { content: ''; position: absolute; bottom: 3px; width: 5px; height: 5px; background: #c0392b; border-radius: 50%; }
        .date.selected.has-class::after { background: white; }

        /* Class list */
        #class-list { display: flex; flex-direction: column; gap: 10px; min-height: 60px; }
        .class-card { border: 1.5px solid #e8e2db; border-radius: 8px; padding: 12px 16px; cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: space-between; }
        .class-card:hover { border-color: #c0392b; background: #fdf8f6; }
        .class-card.selected-class { border-color: #c0392b; background: #fdf0ee; }
        .class-card-name { font-weight: 600; font-size: 0.95rem; }
        .class-card-date { font-size: 0.78rem; color: #555; margin-top: 2px; }
        .class-card-check { width: 20px; height: 20px; border-radius: 50%; border: 2px solid #e8e2db; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .2s; }
        .class-card.selected-class .class-card-check { background: #c0392b; border-color: #c0392b; }
        .class-card.selected-class .class-card-check::after { content: '✓'; color: white; font-size: 0.7rem; font-weight: 700; }
        .no-classes { text-align: center; color: #aaa; font-size: 0.9rem; padding: 20px 0; }

        /* Signup form */
        .selected-summary { background: #e8e2db; border-radius: 8px; padding: 12px 16px; margin-bottom: 18px; font-size: 0.88rem; color: #555; min-height: 44px; }
        .selected-summary strong { color: #1a1a1a; }
        .form-group { margin-bottom: 18px; display: flex; flex-direction: column; }
        .form-group label { font-size: 0.8rem; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .form-group input { padding: 11px 14px; border: 1.5px solid #e8e2db; border-radius: 8px; font-size: 0.95rem; font-family: 'DM Sans', sans-serif; }
        .form-group input:focus { outline: none; border-color: #c0392b; }
        .submit-btn { width: 100%; padding: 14px; background: #c0392b; color: white; border: none; border-radius: 8px; font-family: 'Bebas Neue', sans-serif; font-size: 1.1rem; letter-spacing: 1.5px; cursor: pointer; }
        .submit-btn:hover { background: #a93226; }
        .submit-btn:disabled { background: #ccc; cursor: not-allowed; }

        /* Result message */
        .result-msg { border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; font-weight: 600; }
        .result-success { background: #d4edda; color: #155724; }
        .result-error   { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="topbar">
    <span class="topbar-title">⛰ Summit Gym</span>
    <a href="index.html">← Back to Main</a>
</div>

<div class="page">

    <!-- LEFT: Calendar + Class List -->
    <div>
        <div class="panel">
            <div class="panel-title">Pick a Date</div>
            <div class="cal-header">
                <a href="ClassSignup.php?month=<?= $prevMonth ?>&year=<?= $prevYear ?>">&#8249;</a>
                <span id="monthYear"></span>
                <a href="ClassSignup.php?month=<?= $nextMonth ?>&year=<?= $nextYear ?>">&#8250;</a>
            </div>
            <div class="cal-days-row">
                <div>Mon</div><div>Tue</div><div>Wed</div>
                <div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
            </div>
            <div id="dates"></div>
        </div>

        <div class="panel" style="margin-top: 24px;">
            <div class="panel-title" id="classes-heading">Available Classes</div>
            <div id="class-list">
                <div class="no-classes">Select a date to see available classes.</div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Signup Form -->
    <div class="panel" style="align-self: start; position: sticky; top: 24px;">
        <div class="panel-title">Sign Up</div>

        <?php if ($message != ''): ?>
            <div class="result-msg <?= $success ? 'result-success' : 'result-error' ?>"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" action="ClassSignup.php">
            <div class="selected-summary" id="selected-summary">No class selected yet.</div>

            <div class="form-group">
                <label for="climber_id">Your Climber ID</label>
                <input type="number" id="climber_id" name="climber_id" placeholder="e.g. 101" required>
            </div>

            <input type="hidden" id="class_id" name="class_id" value="">

            <button class="submit-btn" id="submitBtn" type="submit" disabled>Confirm Sign Up</button>
        </form>
    </div>

</div>

<script>
    const allClasses   = <?= json_encode($classes) ?>;
    const currentMonth = <?= $month ?>;
    const currentYear  = <?= $year ?>;
    const monthNames   = ['January','February','March','April','May','June','July','August','September','October','November','December'];

    let selectedDayEl = null;

    function buildCalendar() {
        const firstDay  = new Date(currentYear, currentMonth - 1, 1);
        const lastDay   = new Date(currentYear, currentMonth, 0);
        const totalDays = lastDay.getDate();
        const today     = new Date();
        const classDays = new Set(allClasses.map(c => c.day));

        document.getElementById('monthYear').textContent = monthNames[currentMonth - 1] + ' ' + currentYear;

        let firstDayIndex = firstDay.getDay() - 1;
        if (firstDayIndex === -1) firstDayIndex = 6;

        let html = '';

        const prevLast = new Date(currentYear, currentMonth - 1, 0).getDate();
        for (let i = firstDayIndex; i > 0; i--) {
            html += '<div class="date inactive">' + (prevLast - i + 1) + '</div>';
        }

        for (let d = 1; d <= totalDays; d++) {
            let cls = 'date';
            if (new Date(currentYear, currentMonth - 1, d).toDateString() === today.toDateString()) cls += ' today';
            if (classDays.has(d)) cls += ' has-class';
            html += '<div class="' + cls + '" onclick="selectDay(this, ' + d + ')">' + d + '</div>';
        }

        let lastDayIndex = lastDay.getDay() - 1;
        if (lastDayIndex === -1) lastDayIndex = 6;
        for (let i = 1; i <= (6 - lastDayIndex); i++) {
            html += '<div class="date inactive">' + i + '</div>';
        }

        document.getElementById('dates').innerHTML = html;
    }

    function selectDay(el, day) {
        if (selectedDayEl) selectedDayEl.classList.remove('selected');
        el.classList.add('selected');
        selectedDayEl = el;

        document.getElementById('class_id').value = '';
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('selected-summary').textContent = 'No class selected yet.';

        const dayClasses = allClasses.filter(c => c.day === day);
        document.getElementById('classes-heading').textContent = 'Classes on ' + monthNames[currentMonth - 1] + ' ' + day;

        if (dayClasses.length === 0) {
            document.getElementById('class-list').innerHTML = '<div class="no-classes">No classes on this day.</div>';
            return;
        }

        let html = '';
        for (let i = 0; i < dayClasses.length; i++) {
            const c = dayClasses[i];
            html += '<div class="class-card" onclick="selectClass(this, ' + c.id + ', \'' + c.name + '\', \'' + c.event_date + '\')">';
            html += '<div><div class="class-card-name">' + c.name + '</div>';
            html += '<div class="class-card-date">' + c.event_date + '</div></div>';
            html += '<div class="class-card-check"></div></div>';
        }
        document.getElementById('class-list').innerHTML = html;
    }

    function selectClass(el, id, name, date) {
        document.querySelectorAll('.class-card').forEach(function(c) { c.classList.remove('selected-class'); });
        el.classList.add('selected-class');
        document.getElementById('class_id').value = id;
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('selected-summary').innerHTML = '<strong>' + name + '</strong><br>' + date;
    }

    buildCalendar();
</script>
</body>
</html>
