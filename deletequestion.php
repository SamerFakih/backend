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

