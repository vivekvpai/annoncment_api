<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if the request method is PUT
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Get the raw PUT data
    $json_data = file_get_contents('php://input');

    // Decode JSON data into an associative array
    $data = json_decode($json_data, true);

    // Check if JSON decoding was successful and required data is present
    if ($data !== null && isset($data['category_id']) && isset($data['category_name'])) {
        // include database connection
        include '../config.php';

        try {
            // Update query for the categories table
            $query = "UPDATE categories SET category_name=:v_category_name WHERE category_id=:v_category_id";

            // Prepare the query for execution
            $stmt = $con->prepare($query);

            // posted values
            $v_category_id = $data['category_id'];
            $v_category_name = $data['category_name'];

            // bind the parameters
            $stmt->bindParam(':v_category_id', $v_category_id);
            $stmt->bindParam(':v_category_name', $v_category_name);

            // Execute the query
            if ($stmt->execute()) {
                http_response_code(200); // 200 OK
                echo json_encode(array('result' => 'success', 'message' => 'Category updated successfully.'));
            } else {
                http_response_code(500);
                echo json_encode(array('result' => 'fail', 'message' => 'Failed to update category.'));
            }
        } catch (PDOException $exception) {
            http_response_code(400);
            die('ERROR: ' . $exception->getMessage());
        }
    } else {
        // Return error response if JSON decoding failed or data is missing
        http_response_code(400);
        echo json_encode(array('result' => 'error', 'message' => 'Invalid JSON data or missing parameters.'));
    }
} else {
    // Return error response for other request methods
    http_response_code(405);
    echo json_encode(array('result' => 'error', 'message' => 'Method not allowed.'));
}
?>
