<?php
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'invsys';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error,
    ]);
    exit();
}

// Validate input
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No product ID provided.',
    ]);
    exit();
}

$productId = intval($_GET['id']);

// Prepare the SQL statement
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare statement: ' . $conn->error,
    ]);
    exit();
}

$stmt->bind_param('i', $productId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the product exists
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'product' => [
            'product_name' => $product['product_name'],
            'category_id' => $product['category_id'],
            'product_stock' => $product['product_stock'],
            'buying_price' => $product['buying_price'],
            'selling_price' => $product['selling_price'],
        ],
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Product not found.',
    ]);
}

// Close the connection
$stmt->close();
$conn->close();
?>
