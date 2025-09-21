<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get the raw DELETE data
    $json_data = file_get_contents('php://input');

    // Decode JSON data into an associative array
    $data = json_decode($json_data, true);

    // Check if JSON decoding was successful and the category_id is present
    if ($data !== null && isset($data['category_id'])) {
        // include database connection
        include '../config.php';

        try {
            // Delete query for the categories table
            $query = "DELETE FROM categories WHERE category_id = :v_category_id";

            // Prepare the query for execution
            $stmt = $con->prepare($query);

            // posted value
            $v_category_id = $data['category_id'];

            // bind the parameter
            $stmt->bindParam(':v_category_id', $v_category_id);

            // Execute the query
            if ($stmt->execute()) {
                http_response_code(200); // 200 OK
                echo json_encode(array('result' => 'success', 'message' => 'Category deleted successfully.'));
            } else {
                http_response_code(500);
                echo json_encode(array('result' => 'fail', 'message' => 'Failed to delete category.'));
            }
        } catch (PDOException $exception) {
            http_response_code(400);
            die('ERROR: ' . $exception->getMessage());
        }
    } else {
        // Return error response if JSON decoding failed or data is missing
        http_response_code(400);
        echo json_encode(array('result' => 'error', 'message' => 'Invalid JSON data or missing category_id parameter.'));
    }
} else {
    // Return error response for other request methods
    http_response_code(405);
    echo json_encode(array('result' => 'error', 'message' => 'Method not allowed.'));
}
?>
