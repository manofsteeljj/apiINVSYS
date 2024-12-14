<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:3000");  // Modify if necessary
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Database configuration
$host = 'localhost';
$dbname = 'invsys';
$username = 'root';
$password = '';

try {
    // Establish a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get the admin_id from the GET request
$admin_id = isset($_GET['admin_id']) ? $_GET['admin_id'] : null;

// Check if the admin_id is provided
if ($admin_id) {
    try {
        // Prepare the delete query
        $stmt = $pdo->prepare('DELETE FROM admin WHERE admin_id = :admin_id');
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Check if the delete operation was successful
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Admin deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Admin not found or already deleted.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No admin_id provided.']);
}
?>
