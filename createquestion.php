<?php
// Return JSON responses
header("Content-Type: application/json");
// Start session
session_start();

// Include DB connection
include 'connect.php';

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);
    exit;
}

// Check if required fields are provided
if (isset($data['quiz_id'], $data['question'], $data['option_a'], $data['option_b'], $data['option_c'], $data['option_d'], $data['correct_option'])) {

    $quiz_id = $data['quiz_id'];
    $question = $data['question'];
    $option_a = $data['option_a'];
    $option_b = $data['option_b'];
    $option_c = $data['option_c'];
    $option_d = $data['option_d'];
    $correct_option = $data['correct_option'];
    $user_id = $_SESSION['user_id'];

    try {
        // First, check if the quiz belongs to the logged-in user
        $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ? AND created_by = ?");
        $stmt->execute([$quiz_id, $user_id]);

        if ($stmt->rowCount() == 0) {
            echo json_encode([
                "success" => false,
                "message" => "Quiz not found or you don't have permission to add questions to it"
            ]);
            exit;
        }

        // Insert the question into the database
        $insertStmt = $conn->prepare("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->execute([$quiz_id, $question, $option_a, $option_b, $option_c, $option_d, $correct_option]);

        echo json_encode([
            "success" => true,
            "message" => "Question added successfully"
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required"
    ]);
}
?>
