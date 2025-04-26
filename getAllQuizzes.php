<?php
// Set the response to JSON
header("Content-Type: application/json");

// Include the database connection
include 'connect.php';

try {
    // Prepare the SQL query to select all quizzes
    $stmt = $conn->prepare("SELECT * FROM quizzes");
    $stmt->execute();

    // Fetch all quizzes as an associative array
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Send success response with all quizzes
    echo json_encode([
        "success" => true,
        "quizzes" => $quizzes
    ]);
    
} catch (PDOException $e) {
    // Send error response if something goes wrong
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}

