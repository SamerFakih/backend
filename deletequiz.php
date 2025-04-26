<?php
// Return JSON responses
header("Content-Type: application/json");
// Start the session
session_start();

// Include DB connection
include 'connect.php';

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

