<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // include database connection
    include '../config.php';

    try {
        // Select query to retrieve all categories
        $query = "SELECT category_id, category_name FROM categories ORDER BY category_name";

        // Prepare the query for execution
        $stmt = $con->prepare($query);

        // Execute the query
        $stmt->execute();

        // Fetch all categories as an associative array
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if any categories were found
        if ($categories) {
            http_response_code(200); // 200 OK
            echo json_encode(array('result' => 'success', 'data' => $categories));
        } else {
            http_response_code(404); // 404 Not Found
            echo json_encode(array('result' => 'error', 'message' => 'No categories found.'));
        }
    } catch (PDOException $exception) {
        http_response_code(400);
        die('ERROR: ' . $exception->getMessage());
    }
} else {
    // Return error response for other request methods
    http_response_code(405);
    echo json_encode(array('result' => 'error', 'message' => 'Method not allowed.'));
}
?>
