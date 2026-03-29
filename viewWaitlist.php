<?php
include "db.php";

$sql = "SELECT * FROM Waitlist_IsOn ORDER BY ClassID, QueueNumber";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Waitlist</title>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
            margin: 30px auto;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: lightgray;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Class Waitlist</h2>

<table>
    <tr>
        <th>Queue Number</th>
        <th>Class ID</th>
        <th>Climber ID</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['QueueNumber']}</td>
                    <td>{$row['ClassID']}</td>
                    <td>{$row['ClimberID']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No data found</td></tr>";
    }
    ?>

</table>

</body>
</html>

<?php
$conn->close();
?>