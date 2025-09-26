<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // include database connection
    include '../config.php';

    try {
        // Select query to retrieve all announcements with associated user and category details
        $query = "
            SELECT 
                a.announcement_id, 
                a.announcement_title, 
                a.announcement_content, 
                a.view_count, 
                a.timestamp,
                u.user_name,
                u.user_id,
                c.category_name,
                c.category_id
            FROM 
                announcements a
            INNER JOIN 
                users u ON a.user_id = u.user_id
            INNER JOIN 
                categories c ON a.category_id = c.category_id
            ORDER BY 
                a.timestamp DESC";

        // Prepare the query for execution
        $stmt = $con->prepare($query);

        // Execute the query
        $stmt->execute();

        // Fetch all announcements as an associative array
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if any announcements were found
        if ($announcements) {
            http_response_code(200); // 200 OK
            echo json_encode(array('result' => 'success', 'data' => $announcements));
        } else {
            http_response_code(404); // 404 Not Found
            echo json_encode(array('result' => 'error', 'message' => 'No announcements found.'));
        }
    } catch (PDOException $exception) {
        http_response_code(400);
        die('ERROR: ' . $exception->getMessage());
    }
} else {
    // Return error response for other request methods
    http_response_code(405);
    echo json_encode(array('result' => 'error', 'message' => 'Method not allowed.'));
}
?>
