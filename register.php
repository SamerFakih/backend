<?php
header("Content-Type: application/json");
include './connect.php'; // Connect to DB

//Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

//Check if all fields are provided
if (
    isset($data['username']) &&
    isset($data['email']) &&
    isset($data['password'])
) {
    $username = $data['username'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash password

    try {
        //Check if username or email exists
        $check = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);

        if ($check->rowCount() > 0) {
            echo json_encode(["success" => false, "message" => "Username or email already exists"]);
        } else {
            //Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $password]);
            echo json_encode(["success" => true, "message" => "User registered successfully"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
}
?>
