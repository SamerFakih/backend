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

// Check if question_id is provided
if (isset($data['question_id'])) {
    $question_id = $data['question_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Check if the question belongs to a quiz created by the user
        $stmt = $conn->prepare("
            SELECT questions.* FROM questions
            JOIN quizzes ON questions.quiz_id = quizzes.id
            WHERE questions.id = ? AND quizzes.created_by = ?
        ");
        $stmt->execute([$question_id, $user_id]);

        if ($stmt->rowCount() == 0) {
            echo json_encode([
                "success" => false,
                "message" => "Question not found or you don't have permission to delete it"
            ]);
            exit;
        }

        // Delete the question
        $deleteStmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
        $deleteStmt->execute([$question_id]);

        echo json_encode([
            "success" => true,
            "message" => "Question deleted successfully"
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
        "message" => "Question ID is required"
    ]);
}
?>
