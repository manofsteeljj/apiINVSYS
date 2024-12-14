<?php
header("Content-Type: application/json");

// Allow cross-origin requests from your frontend
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Database credentials
$host = 'localhost';
$dbname = 'invsys';
$username = 'root';
$password = '';

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Get input data from the request
$data = json_decode(file_get_contents("php://input"));

// Debugging: Log received data
error_log("Received data: " . print_r($data, true));

// Validate input fields
if (
    isset($data->product_id) && !empty($data->product_id) &&
    isset($data->product_name) && !empty($data->product_name) &&
    isset($data->category_id) && !empty($data->category_id) &&
    isset($data->product_stock) &&
    isset($data->buying_price) &&
    isset($data->selling_price)
) {
    $product_id = $data->product_id;
    $product_name = $data->product_name;
    $category_id = $data->category_id;
    $product_stock = $data->product_stock;
    $buying_price = $data->buying_price;
    $selling_price = $data->selling_price;

    // Debug: Parameters
    error_log("Parameters: product_id = $product_id, product_name = $product_name, category_id = $category_id, product_stock = $product_stock, buying_price = $buying_price, selling_price = $selling_price");

    // Check if the product exists
    $checkSql = "SELECT COUNT(*) FROM products WHERE product_id = :product_id";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $checkStmt->execute();
    $productExists = $checkStmt->fetchColumn();

    if ($productExists == 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    // Update product details
    $sql = "UPDATE products SET 
                product_name = :product_name, 
                category_id = :category_id, 
                product_stock = :product_stock, 
                buying_price = :buying_price, 
                selling_price = :selling_price 
            WHERE product_id = :product_id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':product_stock', $product_stock, PDO::PARAM_INT);
        $stmt->bindParam(':buying_price', $buying_price);
        $stmt->bindParam(':selling_price', $selling_price);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update product', 'error' => $stmt->errorInfo()]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error updating product: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
}
?>
