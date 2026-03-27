<?php
$servername = "localhost";
$username = "root";
$password = "root"; 
$database = "Rock Climbing Customer Management";

$climberId = $_POST['climberId'] ?? null;
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT name, sex, dob FROM Climber WHERE climberId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $climberId);
$stmt->execute();
$result = $stmt->get_result();

// Link CSS and HTML
echo "<!DOCTYPE html><html><head><link rel='stylesheet' href='simple-style.css'></head><body>";
echo "<div class='container'>";
echo "<h1>Search Results</h1>";

if ($row = $result->fetch_assoc()) {
    $birthDate = new DateTime($row['dob']);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    $gender = ($row['sex'] == 'M') ? "Male" : "Female";

    echo "<table class='climber-table'>";
    echo "<thead><tr><th colspan='2'>Climber Profile</th></tr></thead>";
    echo "<tbody>";
    echo "<tr><td><b>Name</b></td><td>" . htmlspecialchars($row['name']) . "</td></tr>";
    echo "<tr><td><b>Gender</b></td><td>" . $gender . "</td></tr>";
    echo "<tr><td><b>Age</b></td><td>" . $age . " years old</td></tr>";
    echo "<tr><td><b>Birthdate</b></td><td>" . $row['dob'] . "</td></tr>";
    echo "</tbody></table>";
} else {
    echo "<div class='message error'>No climber found with ID: " . htmlspecialchars($climberId) . "</div>";
}

echo "<div class='link-section'><a href='searchClimber.html'>Search again</a></div>";
echo "</div></body></html>";

$stmt->close();
$conn->close();
?>
