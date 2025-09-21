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

    // Check if JSON decoding was successful and the required data is present
    if ($data !== null && isset($data['announcement_id']) && isset($data['announcement_title']) && isset($data['announcement_content']) && isset($data['category_id'])) {
        // include database connection
        include '../config.php';

        try {
            // Update query for the announcements table
            $query = "UPDATE announcements SET announcement_title = :v_title, announcement_content = :v_content, category_id = :v_category_id WHERE announcement_id = :v_announcement_id";

            // Prepare the query for execution
            $stmt = $con->prepare($query);

            // posted values
            $v_announcement_id = $data['announcement_id'];
            $v_title = $data['announcement_title'];
            $v_content = $data['announcement_content'];
            $v_category_id = $data['category_id'];

            // bind the parameters
            $stmt->bindParam(':v_announcement_id', $v_announcement_id);
            $stmt->bindParam(':v_title', $v_title);
            $stmt->bindParam(':v_content', $v_content);
            $stmt->bindParam(':v_category_id', $v_category_id);

            // Execute the query
            if ($stmt->execute()) {
                http_response_code(200); // 200 OK
                echo json_encode(array('result' => 'success', 'message' => 'Announcement updated successfully.'));
            } else {
                http_response_code(500);
                echo json_encode(array('result' => 'fail', 'message' => 'Failed to update announcement.'));
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
