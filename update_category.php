<?php
// Allow cross-origin requests (CORS) for all origins
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"));
    
    // Check if category ID and category name are set
    if (isset($data->id) && isset($data->category)) {
        $categoryId = $data->id;
        $categoryName = $data->category;

        // SQL query to update the category
        $query = "UPDATE categories SET category = ? WHERE category_id = ?";
        
        if ($stmt = $conn->prepare($query)) {
            // Bind parameters
            $stmt->bind_param('si', $categoryName, $categoryId);
            
            // Execute the query
            if ($stmt->execute()) {
                // Return success response
                echo json_encode(['success' => true, 'message' => 'Category updated successfully.']);
            } else {
                // Return error response if update fails
                echo json_encode(['success' => false, 'message' => 'Failed to update category.']);
            }
            
            // Close the statement
            $stmt->close();
        } else {
            // Return error response if statement preparation fails
            echo json_encode(['success' => false, 'message' => 'Database query preparation failed.']);
        }
    } else {
        // Return error response if required data is missing
        echo json_encode(['success' => false, 'message' => 'Category ID or category name not provided.']);
    }
} else {
    // Return error response if the request method is not POST
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
