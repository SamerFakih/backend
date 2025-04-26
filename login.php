<?php
//Set the response type to JSON
header("Content-Type: application/json");
//Start the session
session_start();

//Include your DB connection file
include 'connect.php';

// Read the JSON input from the request body
$data = json_decode(file_get_contents("php://input"), true);


