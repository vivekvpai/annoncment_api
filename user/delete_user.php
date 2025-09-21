<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get the raw DELETE data
    $json_data = file_get_contents('php://input');

    // Decode JSON data into an associative array
    $data = json_decode($json_data, true);

    // Check if JSON decoding was successful and the user_email parameter is present
    if ($data !== null && isset($data['user_email'])) {
        // include database connection
        include '../config.php';

        try {
            // Delete query for the users table based on user_email
            $query = "DELETE FROM users WHERE user_email = :v_user_email";

            // Prepare the query for execution
            $stmt = $con->prepare($query);

            // posted value
            $v_user_email = $data['user_email'];

            // bind the parameter
            $stmt->bindParam(':v_user_email', $v_user_email);

            // Execute the query
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(array('result' => 'success'));
            } else {
                http_response_code(500);
                echo json_encode(array('result' => 'fail'));
            }
        } catch (PDOException $exception) {
            http_response_code(400);
            die('ERROR: ' . $exception->getMessage());
        }
    } else {
        // Return error response if JSON decoding failed or data is missing
        http_response_code(400);
        echo json_encode(array('result' => 'error', 'message' => 'Invalid JSON data or missing user_email parameter.'));
    }
} else {
    // Return error response for other request methods
    http_response_code(405);
    echo json_encode(array('result' => 'error', 'message' => 'Method not allowed.'));
}
?>
