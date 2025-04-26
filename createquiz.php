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

