<?php
$servername = "localhost";
$username = "root";
$password = "root"; // Check if your XAMPP/MAMP password is "" or "root"
$database = "Rock Climbing Customer Management";

$climberId = $_POST['climberId'] ?? null;

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Prepare the query to get all columns
$sql = "SELECT name, sex, dob FROM Climber WHERE climberId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $climberId);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Search Results</h2>";

if ($row = $result->fetch_assoc()) {
    // 2. Calculate Age from DOB
    $birthDate = new DateTime($row['dob']);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y; // Gets the difference in years

    // 3. Translate 'M'/'F' to full words
    $gender = ($row['sex'] == 'M') ? "Male" : "Female";

    // 4. Display the "Profile"
    echo "<div style='border: 1px solid #ccc; padding: 15px; width: 300px;'>";
    echo "<strong>Name:</strong> " . htmlspecialchars($row['name']) . "<br>";
    echo "<strong>Gender:</strong> " . $gender . "<br>";
    echo "<strong>Age:</strong> " . $age . " years old<br>";
    echo "<strong>Birthdate:</strong> " . $row['dob'];
    echo "</div>";
} else {
    echo "No climber found with ID: " . htmlspecialchars($climberId);
}

$stmt->close();
$conn->close();

echo "<br><a href='searchClimber.html'>Search again</a>";
?>