<?php
$host = 'localhost';
$db = 'u613448336_db_main';
$user = 'u613448336_main';
$pass = '!AC.dev1';

header('Content-Type: application/json');

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $review_text = mysqli_real_escape_string($conn, $_POST['review_text']);

    $sql = "INSERT INTO reviews (username, rating, review_text) VALUES ('$username', '$rating', '$review_text')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode([
            'success' => true,
            'username' => $username,
            'rating' => $rating,
            'review_text' => $review_text
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
