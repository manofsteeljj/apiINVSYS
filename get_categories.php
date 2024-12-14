<?php
// Enable CORS headers to allow cross-origin requests
header("Access-Control-Allow-Origin: *"); // Allows any origin to access
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow specific methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers

// Database connection configuration
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "invsys"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch all categories
$sql = "SELECT * FROM categories"; // Replace 'categories' with your actual categories table name
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $categories = [];
    // Fetch the categories
    while($row = $result->fetch_assoc()) {
        $categories[] = $row; // Add each category to the array
    }
    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No categories found'
    ]);
}

// Close the connection
$conn->close();
?>
