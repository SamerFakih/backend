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
if (isset($data['quiz_id']) && (isset($data['title']) || isset($data['description']))) {

    $quiz_id = $data['quiz_id'];
    $user_id = $_SESSION['user_id'];
    $title = isset($data['title']) ? $data['title'] : null;
    $description = isset($data['description']) ? $data['description'] : null;

    try {
        // First: Check if the quiz belongs to this user
        $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ? AND created_by = ?");
        $stmt->execute([$quiz_id, $user_id]);

        if ($stmt->rowCount() == 0) {
            echo json_encode([
                "success" => false,
                "message" => "Quiz not found or you don't have permission to edit it"
            ]);
            exit;
        }

        // Build the dynamic SQL query based on which fields are provided
        $updates = [];
        $params = [];

        if ($title !== null) {
            $updates[] = "title = ?";
            $params[] = $title;
        }
        if ($description !== null) {
            $updates[] = "description = ?";
            $params[] = $description;
        }

        $params[] = $quiz_id; // quiz_id for WHERE clause

        $sql = "UPDATE quizzes SET " . implode(", ", $updates) . " WHERE id = ?";
        $updateStmt = $conn->prepare($sql);
        $updateStmt->execute($params);

        echo json_encode([
            "success" => true,
            "message" => "Quiz updated successfully"
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
        "message" => "quiz_id and at least one field (title or description) are required"
    ]);
}
?>
