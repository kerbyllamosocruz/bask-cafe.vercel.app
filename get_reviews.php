<?php
// backend/get_reviews.php

// Allow CORS from any origin (or specific Vercel URL later)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once('config/db_connect.php');

// Check connection
if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Get limit from query parameter, default to all (high number)
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 1000;

// Validate limit
if ($limit <= 0)
    $limit = 1000;

$sql = "SELECT name, rating, review, created_at FROM bask_reviews WHERE status = 'approved' ORDER BY created_at DESC LIMIT ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$reviews = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reviews[] = $row;
}

header('Content-Type: application/json');
echo json_encode($reviews);
?>