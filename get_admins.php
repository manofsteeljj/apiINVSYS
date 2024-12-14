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
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

try {
    // Prepare and execute the query to fetch all admin users
    $stmt = $pdo->prepare('SELECT * FROM admin');
    $stmt->execute();

    // Fetch all results
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if any admins were found
    if ($admins) {
        echo json_encode(['success' => true, 'admins' => $admins]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No admins found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
