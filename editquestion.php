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

// Check if all required fields are provided
if (
    isset($data['question_id']) &&
    isset($data['question']) &&
    isset($data['option_a']) &&
    isset($data['option_b']) &&
    isset($data['option_c']) &&
    isset($data['option_d']) &&
    isset($data['correct_option'])
) {
    $question_id = $data['question_id'];
    $question = $data['question'];
    $option_a = $data['option_a'];
    $option_b = $data['option_b'];
    $option_c = $data['option_c'];
    $option_d = $data['option_d'];
    $correct_option = $data['correct_option'];
    $user_id = $_SESSION['user_id'];

    try {
        // First, make sure the question belongs to a quiz created by the logged-in user
        $stmt = $conn->prepare("
            SELECT questions.* FROM questions
            JOIN quizzes ON questions.quiz_id = quizzes.id
            WHERE questions.id = ? AND quizzes.created_by = ?
        ");
        $stmt->execute([$question_id, $user_id]);

        if ($stmt->rowCount() == 0) {
            echo json_encode([
                "success" => false,
                "message" => "Question not found or you don't have permission to edit"
            ]);
            exit;
        }

        // Update the question
        $updateStmt = $conn->prepare("
            UPDATE questions
            SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?
            WHERE id = ?
        ");
        $updateStmt->execute([
            $question,
            $option_a,
            $option_b,
            $option_c,
            $option_d,
            $correct_option,
            $question_id
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Question updated successfully"
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
