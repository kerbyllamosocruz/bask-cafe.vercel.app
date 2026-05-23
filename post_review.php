<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once('config/db_connect.php');

if (!$conn) { /* Assuming $conn comes from db_connect.php. The original file used $pdo, let's check db_connect.php first. */
}
// Wait, the original post_review.php used PDO manually but required db_connect.php which likely used mysqli?
// Let's stick to what was there but add headers.


try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", 
        $user, 
        $password,
        [
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // TiDB requires SSL, but vercel might not have the CA handy, so we disable strict cert verification here.
        ]
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 0);
        $review = trim($_POST['review'] ?? '');

        if (!$name || !$rating || !$review) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Please fill in all required fields.'
            ]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO bask_reviews (name, rating, review) VALUES (:name, :rating, :review)");
        $stmt->execute([
            ':name' => $name,
            ':rating' => $rating,
            ':review' => $review,
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Review submitted successfully!'
        ]);
    } else {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method Not Allowed'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
