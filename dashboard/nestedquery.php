<?php
// Database connection parameters
$host = '127.0.0.1'; // Hostname (localhost)
$user = 'root';      // Database username
$password = '1234';      // Database password
$database = 'res_booking'; // Database name

// Establish connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query for nested query - Unused chairs
$sql = "
SELECT id AS chair_id, chair_no
FROM restaurant_chair
WHERE id NOT IN (
    SELECT chair_id
    FROM booking_chair
)";
$result = $conn->query($sql);

// Fetch and display results
if ($result->num_rows > 0) {
    echo "<h3 style='text-align: center;'>Unused Chairs</h3>";
    echo "<table border='1' style='width: 50%;'><tr><th>Chair ID</th><th>Chair Number</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["chair_id"] . "</td><td>" . $row["chair_no"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No unused chairs found.</p>";
}

// Close connection
$conn->close();
?>
