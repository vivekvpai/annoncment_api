<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the announcement_id parameter is set in the URL
    if (isset($_GET['announcement_id'])) {
        // include database connection
        include '../config.php';

        try {
            // Get the announcement_id from the URL query string
            $v_announcement_id = $_GET['announcement_id'];

            // Select query to retrieve a single announcement with associated user and category details
            $query = "
                SELECT 
                    a.announcement_id, 
                    a.announcement_title, 
                    a.announcement_content, 
                    a.view_count, 
                    a.timestamp,
                    u.user_name,
                    c.category_name,
                    c.category_id
                FROM 
                    announcements a
                INNER JOIN 
                    users u ON a.user_id = u.user_id
                INNER JOIN 
                    categories c ON a.category_id = c.category_id
                WHERE
                    a.announcement_id = :v_announcement_id
                LIMIT 0,1";

            // Prepare the query for execution
            $stmt = $con->prepare($query);

            // Bind the parameter
            $stmt->bindParam(':v_announcement_id', $v_announcement_id);

            // Execute the query
            $stmt->execute();

            // Fetch the announcement data
            $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if an announcement was found
            if ($announcement) {
                // Increment view count
                $update_query = "UPDATE announcements SET view_count = view_count + 1 WHERE announcement_id = :v_announcement_id";
                $update_stmt = $con->prepare($update_query);
                $update_stmt->bindParam(':v_announcement_id', $v_announcement_id);
                $update_stmt->execute();

                // Get the updated view count from the database
                $announcement['view_count']++;

                http_response_code(200); // 200 OK
                echo json_encode(array('result' => 'success', 'data' => $announcement));
            } else {
                http_response_code(404); // 404 Not Found
                echo json_encode(array('result' => 'error', 'message' => 'Announcement not found.'));
            }
        } catch (PDOException $exception) {
            http_response_code(400);
            die('ERROR: ' . $exception->getMessage());
        }
    } else {
        // Return error response if data is missing
        http_response_code(400);
        echo json_encode(array('result' => 'error', 'message' => 'Missing announcement_id parameter.'));
    }
} else {
    // Return error response for other request methods
    http_response_code(405);
    echo json_encode(array('result' => 'error', 'message' => 'Method not allowed.'));
}
?>
