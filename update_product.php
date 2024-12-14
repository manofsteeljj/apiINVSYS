<?php
// Handle the OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    exit();
}

// Allow CORS for all requests
header("Access-Control-Allow-Origin: *");
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

// Get data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Validate the input
if (empty($data['id']) || empty($data['product']) || empty($data['category_id']) || empty($data['product_stock']) || empty($data['buying_price']) || empty($data['selling_price'])) {
    echo json_encode(["success" => false, "message" => "Invalid input data. Please provide all required fields."]);
    exit();
}

$id = $data['id'];
$product = $data['product'];
$category_id = $data['category_id'];
$product_stock = $data['product_stock'];
$buying_price = $data['buying_price'];
$selling_price = $data['selling_price'];

// Validate that the category_id exists
$category_check_sql = "SELECT 1 FROM categories WHERE category_id = ?";
$category_stmt = $conn->prepare($category_check_sql);
$category_stmt->bind_param("i", $category_id);
$category_stmt->execute();
$category_stmt->store_result();

if ($category_stmt->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Invalid category ID."]);
    exit();
}

$category_stmt->close();

// Check if the product exists
$product_check_sql = "SELECT 1 FROM products WHERE product_id = ?";
$product_stmt = $conn->prepare($product_check_sql);
$product_stmt->bind_param("i", $id);
$product_stmt->execute();
$product_stmt->store_result();

if ($product_stmt->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Product not found."]);
    exit();
}

$product_stmt->close();

// Query to update product
$sql = "UPDATE products SET 
        product = ?, 
        category_id = ?, 
        product_stock = ?, 
        buying_price = ?, 
        selling_price = ? 
        WHERE product_id = ?";

$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("siddii", $product, $category_id, $product_stock, $buying_price, $selling_price, $id);

// Execute the query
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Product updated successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update product."]);
}

$stmt->close();
$conn->close();
?>
