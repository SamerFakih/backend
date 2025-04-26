<?php
//Start session and connect to database
session_start();
include 'connect.php';
header("Content-Type: application/json");

//Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);
    exit;
}

//Parse JSON input and validate fields
$data = json_decode(file_get_contents("php://input"), true);

//(continued): Validate required fields
if (isset($data['title']) && isset($data['description'])) {
    $title = $data['title'];
    $description = $data['description'];
    $user_id = $_SESSION['user_id'];

    try {
        //Insert quiz into database if valid
        $stmt = $conn->prepare("INSERT INTO quizzes (title, description, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $user_id]);

        //Return success JSON response
        echo json_encode([
            "success" => true,
            "message" => "Quiz created successfully"
        ]);
    } catch (PDOException $e) {
        //Handle database error
        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ]);
    }

} else {
    //Handle missing fields
    echo json_encode([
        "success" => false,
        "message" => "Title and description are required"
    ]);
}

