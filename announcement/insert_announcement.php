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
    if ($data !== null && isset($data['announcement_title']) && isset($data['announcement_content']) && isset($data['user_id']) && isset($data['category_id'])) {
        // include database connection
        include '../config.php';

        try {
            // Insert query for the announcements table
            $query = "INSERT INTO announcements (announcement_title, announcement_content, user_id, category_id, view_count, timestamp) VALUES (:v_title, :v_content, :v_user_id, :v_category_id, :v_view_count, NOW())";

            // Prepare the query for execution
            $stmt = $con->prepare($query);

            // posted values
            $v_title = $data['announcement_title'];
            $v_content = $data['announcement_content'];
            $v_user_id = $data['user_id'];
            $v_category_id = $data['category_id'];
            $v_view_count = 0; // Initialize view count to 0

            // bind the parameters
            $stmt->bindParam(':v_title', $v_title);
            $stmt->bindParam(':v_content', $v_content);
            $stmt->bindParam(':v_user_id', $v_user_id);
            $stmt->bindParam(':v_category_id', $v_category_id);
            $stmt->bindParam(':v_view_count', $v_view_count);

            // Execute the query
            if ($stmt->execute()) {
                http_response_code(201); // 201 Created
                echo json_encode(array('result' => 'success', 'message' => 'Announcement created successfully.'));
            } else {
                http_response_code(500);
                echo json_encode(array('result' => 'fail', 'message' => 'Failed to create announcement.'));
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
