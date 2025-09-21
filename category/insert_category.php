<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $json_data = file_get_contents('php://input');

    // Decode JSON data into an associative array
    $data = json_decode($json_data, true);

    // Check if JSON decoding was successful and the required data is present
    if ($data !== null && isset($data['category_name'])) {
        // include database connection
        include '../config.php';

        try {
            // Insert query for the categories table
            $query = "INSERT INTO categories (category_name) VALUES (:v_category_name)";

            // Prepare the query for execution
            $stmt = $con->prepare($query);

            // posted value
            $v_category_name = $data['category_name'];

            // bind the parameter
            $stmt->bindParam(':v_category_name', $v_category_name);

            // Execute the query
            if ($stmt->execute()) {
                http_response_code(201); // 201 Created
                echo json_encode(array('result' => 'success', 'message' => 'Category created successfully.'));
            } else {
                http_response_code(500);
                echo json_encode(array('result' => 'fail', 'message' => 'Failed to create category.'));
            }
        } catch (PDOException $exception) {
            http_response_code(400);
            die('ERROR: ' . $exception->getMessage());
        }
    } else {
        // Return error response if JSON decoding failed or data is missing
        http_response_code(400);
        echo json_encode(array('result' => 'error', 'message' => 'Invalid JSON data or missing category_name parameter.'));
    }
} else {
    // Return error response for other request methods
    http_response_code(405);
    echo json_encode(array('result' => 'error', 'message' => 'Method not allowed.'));
}
?>
