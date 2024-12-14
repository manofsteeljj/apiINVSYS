<?php
header('Content-Type: application/json');

// Database connection settings
$servername = "localhost"; // Update with your database server name
$username = "root";        // Update with your database username
$password = "";            // Update with your database password
$dbname = "invsys"; // Update with your database name

// Establishing the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Get product ID from request
$product_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($product_id) {
    // Query to delete product
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Product deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete product."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid product ID."]);
}

// Close the database connection
$conn->close();
?>
