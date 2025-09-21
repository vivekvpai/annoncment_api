<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Check if the request contains POST data
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $json_data = file_get_contents('php://input');

    // Decode JSON data into associative array
    $data = json_decode($json_data, true);

    // Check if JSON decoding was successful and required data is present
    if ($data !== null && isset($data['user_name']) && isset($data['user_email']) && isset($data['password'])) {
        // include database connection
        include '../config.php';

        try {
            // insert query for the users table
            $query = "INSERT INTO users SET user_name=:v_user_name, user_email=:v_user_email, user_password=:v_password";

            // prepare query for execution
            $stmt = $con->prepare($query);

            // posted values
            $v_user_name = $data['user_name'];
            $v_user_email = $data['user_email'];
            $v_password = $data['password'];

            // bind the parameters
            $stmt->bindParam(':v_user_name', $v_user_name);
            $stmt->bindParam(':v_user_email', $v_user_email);
            $stmt->bindParam(':v_password', $v_password);

            // Execute the query
            if($stmt->execute()) {
                http_response_code(200);
                echo json_encode(array('result' => 'success'));
            } else {
                http_response_code(500);
                echo json_encode(array('result' => 'fail'));
            }
        } catch(PDOException $exception) {
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
