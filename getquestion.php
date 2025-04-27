<?php
// Return JSON responses
header("Content-Type: application/json");
// Start session
session_start();

// Include DB connection
include 'connect.php';

// Read the JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);
    exit;
}

// Check if quiz_id is provided
if (isset($data['quiz_id'])) {

    $quiz_id = $data['quiz_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // First, check if the quiz belongs to the logged-in user
        $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ? AND created_by = ?");
        $stmt->execute([$quiz_id, $user_id]);

        if ($stmt->rowCount() == 0) {
            echo json_encode([
                "success" => false,
                "message" => "Quiz not found or you don't have permission to view questions"
            ]);
            exit;
        }

        // Fetch all questions for this quiz
        $questionsStmt = $conn->prepare("SELECT id, question, option_a, option_b, option_c, option_d, correct_option FROM questions WHERE quiz_id = ?");
        $questionsStmt->execute([$quiz_id]);

        $questions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "questions" => $questions
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
        "message" => "quiz_id is required"
    ]);
}
?>
