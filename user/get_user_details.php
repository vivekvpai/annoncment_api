<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the user_id parameter is set in the URL
    if (isset($_GET['user_id'])) {
        // include database connection
        include '../config.php';

        try {
            // Get the user's ID from the URL query string
            $user_id = intval($_GET['user_id']);

            // Select query to retrieve user details based on user_id
            // NOTE: We are intentionally not selecting the user_password for security.
            $query = "SELECT user_id, user_name, user_email FROM users WHERE user_id = :user_id";

            // Prepare the query for execution
            $stmt = $con->prepare($query);

            // Bind the parameter with proper type
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Fetch the user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if a user was found
            if ($user) {
                http_response_code(200);
                echo json_encode(array('result' => 'success', 'data' => $user));
            } else {
                http_response_code(404);
                echo json_encode(array('result' => 'error', 'message' => 'User not found.'));
            }
        } catch (PDOException $exception) {
            http_response_code(400);
            die('ERROR: ' . $exception->getMessage());
        }
    } else {
        // Return error response if data is missing
        http_response_code(400);
        echo json_encode(array('result' => 'error', 'message' => 'Missing user_id parameter.'));
    }
} else {
    // Return error response for other request methods
    http_response_code(405);
    echo json_encode(array('result' => 'error', 'message' => 'Method not allowed.'));
}
?>
