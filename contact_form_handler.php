<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('config/db_connect.php');

$response = ["success" => false, "message" => "Something went wrong."];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $contact_no = $_POST['contact_no'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    $status = "pending";

    if (empty($name) || empty($email) || empty($message)) {
        $response["message"] = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO bask_contact (name, contact_no, email, message, status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $name, $contact_no, $email, $message, $status);

            if ($stmt->execute()) {
                $response = ["success" => true, "message" => "Message sent successfully."];
            } else {
                $response["message"] = $stmt->error;
            }

            $stmt->close();
        } else {
            $response["message"] = "Database prepare failed: " . $conn->error;
        }
    }

    $conn->close();
} else {
    $response["message"] = "Invalid request method.";
}

echo json_encode($response);
exit;