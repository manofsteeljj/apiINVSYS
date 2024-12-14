<?php
// Enable CORS for all origins or specify your frontend URL
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Database connection parameters
$host = 'localhost';      // Database host
$dbname = 'invsys'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

// Establish connection to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Check if 'id' parameter is provided in the URL query string
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $categoryId = $_GET['id'];

    // Prepare and execute the SQL query to get the category by ID
    $sql = "SELECT * FROM categories WHERE category_id = :categoryId LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the category exists
        if ($category) {
            echo json_encode([
                'success' => true,
                'category' => $category
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Category not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching category: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing category ID']);
}
?>
