<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the category_id parameter is set in the URL
    if (isset($_GET['category_id'])) {
        // include database connection
        include '../config.php';

        try {
            // Get the category ID from the URL query string
            $v_category_id = $_GET['category_id'];

            // Select query to retrieve a single category
            $query = "SELECT category_id, category_name FROM categories WHERE category_id = :v_category_id LIMIT 0,1";

            // Prepare the query for execution
            $stmt = $con->prepare($query);

            // Bind the parameter
            $stmt->bindParam(':v_category_id', $v_category_id);

            // Execute the query
            $stmt->execute();

            // Fetch the category data
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if a category was found
            if ($category) {
                http_response_code(200); // 200 OK
                echo json_encode(array('result' => 'success', 'data' => $category));
            } else {
                http_response_code(404); // 404 Not Found
                echo json_encode(array('result' => 'error', 'message' => 'Category not found.'));
            }
        } catch (PDOException $exception) {
            http_response_code(400);
            die('ERROR: ' . $exception->getMessage());
        }
    } else {
        // Return error response if data is missing
        http_response_code(400);
        echo json_encode(array('result' => 'error', 'message' => 'Missing category_id parameter.'));
    }
} else {
    // Return error response for other request methods
    http_response_code(405);
    echo json_encode(array('result' => 'error', 'message' => 'Method not allowed.'));
}
?>
