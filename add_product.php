
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "invsys";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Fetch POST data
$data = json_decode(file_get_contents("php://input"), true);

$product = $data['product'] ?? '';
$category = $data['category'] ?? ''; // This should be the category_id, not category name
$product_stock = $data['product_stock'] ?? 0;
$buying_price = $data['buying_price'] ?? 0.0;
$selling_price = $data['selling_price'] ?? 0.0;

// Check if category is valid (either send category_id or look it up)
if (!$product || !$category || $product_stock <= 0 || $buying_price <= 0 || $selling_price <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid input data."]);
    exit;
}

// If you're passing category name, you need to look up the category_id
if (!is_numeric($category)) {
    $category_query = "SELECT category_id FROM categories WHERE category_name = ?";
    $stmt = $conn->prepare($category_query);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $stmt->bind_result($category_id);
    if (!$stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Category not found."]);
        $stmt->close();
        exit;
    }
    $stmt->close();
} else {
    // If category is already the category_id
    $category_id = (int)$category;
}

// Insert into database
$sql = "INSERT INTO products (product, category_id, product_stock, buying_price, selling_price) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siidd", $product, $category_id, $product_stock, $buying_price, $selling_price);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "product_id" => $conn->insert_id]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to insert product."]);
}

$stmt->close();
$conn->close();
?>