<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../api/config/db_connect.php';

header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
    exit;
}

$id = mysqli_real_escape_string($conn, $_POST['id']);
$reply_text = $_POST['reply'];
$subject = isset($_POST['subject']) ? $_POST['subject'] : "Re: Your message to Bask Café";

// Fetch message details
$query = "SELECT * FROM bask_contact WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Message not found']);
    exit;
}

$message = mysqli_fetch_assoc($result);

// Send email
$headers = "From: Bask Café <support@baskcafe.site>\r\n";
$headers .= "Reply-To: support@baskcafe.site\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$email_body = "Dear " . $message['name'] . ",\n\n";
$email_body .= $reply_text . "\n\n";
$email_body .= "Thank you for reaching out to us!\n\n";
$email_body .= "Best regards,\nBask Café Team\n\n";
$email_body .= "--------------------\n";
$email_body .= "Your original message:\n" . $message['message'] . "\n";
$email_body .= "--------------------\n";

$mail_sent = mail($message['email'], $subject, $email_body, $headers);

if ($mail_sent) {
    $update_query = "UPDATE bask_contact SET status = 'replied' WHERE id = '$id'";
    if (mysqli_query($conn, $update_query)) {
        echo json_encode(['success' => true, 'message' => 'Reply sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update message status']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email']);
}
