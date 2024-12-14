<?php
// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Origin: *'); // Replace * with your frontend URL (e.g., 'http://localhost:3000') for security
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    exit(); // Exit after handling the OPTIONS request
}

// Allow CORS for all requests
header("Access-Control-Allow-Origin: *"); // Or specify your frontend domain like 'http://localhost:3000'
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Set the content type to JSON
header('Content-Type: application/json');

// Database connection settings
$servername = "localhost"; // Update with your database server name
$username = "root";        // Update with your database username
$password = "";            // Update with your database password
$dbname = "invsys";        // Update with your database name

// Establishing the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Query to fetch categories
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);

// Fetch and return data
if ($result->num_rows > 0) {
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    echo json_encode(["success" => true, "categories" => $categories]);
} else {
    echo json_encode(["success" => false, "message" => "No categories found."]);
}

// Close the database connection
$conn->close();
?>
