<?php
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


header('Content-Type: application/json');

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "invsys"; // Update with your database name

// Establishing the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Query to fetch products with category names (including zero-stock products)
$sql = "SELECT p.product_id, p.product, p.product_stock, p.buying_price, p.selling_price, p.created, p.category_id
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id";

// Execute the query
$result = $conn->query($sql);

// Fetch and return data
if ($result->num_rows > 0) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Check if product is valid (stock > 0 or price > 0) - if you want to exclude invalid products, modify this check
        if ($row['product_stock'] > 0 || $row['buying_price'] > 0 || $row['selling_price'] > 0) {
            $products[] = $row;
        }
    }
    echo json_encode(["success" => true, "products" => $products]);
} else {
    echo json_encode(["success" => false, "message" => "No products found."]);
}

// Close the database connection
$conn->close();
?>
