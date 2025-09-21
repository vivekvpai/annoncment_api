<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=UTF-8");

// Include database connection
include '../config.php';

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Check if email and password are provided
if(!isset($data->user_email) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode(array("status" => "error", "message" => "Email and password are required."));
    exit();
}

try {
    $email = $data->user_email;
    $password = $data->password;

    // Query to check if user exists
    $query = "SELECT user_id, user_name, user_email, user_password 
              FROM users 
              WHERE user_email = :email 
              LIMIT 1";
              
    $stmt = $con->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password (plain text comparison)
        if($password === $row['user_password']) {
            // Return success with user details
            http_response_code(200);
            echo json_encode(array(
                "status" => "success",
                "user_id" => $row['user_id'],
                "user_name" => $row['user_name'],
                "user_email" => $row['user_email']
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("status" => "error", "message" => "Invalid email or password."));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("status" => "error", "message" => "User not found."));
    }
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(array("status" => "error", "message" => "Login failed: " . $exception->getMessage()));
}
?>
