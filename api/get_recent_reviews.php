<?php
ini_set('display_errors', 0); 
error_reporting(E_ALL);

require_once('config/db_connect.php');

// Add WHERE clause to filter only approved reviews
$sql = "SELECT name, rating, review FROM bask_reviews WHERE status = 'approved' ORDER BY created_at DESC LIMIT 3";
$result = mysqli_query($conn, $sql);

$reviews = [];

while ($row = mysqli_fetch_assoc($result)) {
  $reviews[] = $row;
}

header('Content-Type: application/json');
echo json_encode($reviews);
?>
