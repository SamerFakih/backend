<?php
//Set the response type to JSON
header("Content-Type: application/json");
//Start the session
session_start();

//Include your DB connection file
include 'connect.php';

// Read the JSON input from the request body
$data = json_decode(file_get_contents("php://input"), true);
//Check if email and password are provided
if (isset($data['email']) && isset($data['password'])) {

    $email = $data['email'];
    $password = $data['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {
                // Set the session user_id here!
                $_SESSION['user_id'] = $user['id'];

                echo json_encode([
                    "success" => true,
                    "message" => "Login successful",
                    "user" => [
                        "id" => $user['id'],
                        "username" => $user['username'],
                        "email" => $user['email']
                    ]
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Incorrect password"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "User not found"]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }

} else {
    echo json_encode(["success" => false, "message" => "Please provide email and password"]);
}
?>

