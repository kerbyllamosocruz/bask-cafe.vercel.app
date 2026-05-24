<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../api/config/db_connect.php';

// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Fetch message details
    $query = "SELECT * FROM bask_contact WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $message = mysqli_fetch_assoc($result);
    } else {
        // Redirect back with error message
        header("Location: messages.php?error=Message not found");
        exit();
    }
} else {
    // Redirect back with error message
    header("Location: messages.php?error=Invalid message ID");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply_text = $_POST['reply'];
    $subject = isset($_POST['subject']) ? $_POST['subject'] : "Re: Your message to Bask Cafe";
    
    // Email headers
    $headers = "From: Bask Cafe <support@baskcafe.site>\r\n";
    $headers .= "Reply-To: support@baskcafe.site\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Create plain text email body
    $email_body = "Dear " . $message['name'] . ",\n\n";
    $email_body .= $reply_text . "\n\n";
    $email_body .= "Thank you for reaching out to us!\n\n";
    $email_body .= "Best regards,\nBask Cafe Team\n\n";
    $email_body .= "--------------------\n";
    $email_body .= "Your original message:\n" . $message['message'] . "\n";
    $email_body .= "--------------------\n";
    
    // Send the email
    $mail_sent = mail($message['email'], $subject, $email_body, $headers);
    
    if ($mail_sent) {
        // Update the message status to 'replied'
        $update_query = "UPDATE bask_contact SET status = 'replied' WHERE id = '$id'";
        
        if (mysqli_query($conn, $update_query)) {
            // Redirect back to messages page with success message
            header("Location: messages.php?success=Reply sent successfully to " . htmlspecialchars($message['email']));
            exit();
        } else {
            $error = "Error updating message status: " . mysqli_error($conn);
        }
    } else {
        $error = "Error sending email. Please check your server's mail configuration.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Message - Bask Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Base styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Libre Baskerville', serif;
}

body {
  background-color: #f5f5f5;
}

.container {
  display: flex;
  min-height: 100vh;
  margin: 0 auto;
  background-color: white;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Sidebar styles */
.sidebar {
  width: 200px;
  background-color: #1A2916;
  color: white;
  display: flex;
  flex-direction: column;
  position: relative;
}

.logo {
  padding: 15px;
  text-align: center;
}

.logo img {
  height: 45px;
  width: auto;
  display: block;
}

.admin-text {
  padding: 20px;
  font-size: 18px;
  color: white;
  text-align: center;
  font-family: 'Libre Baskerville', serif;
}

.nav-menu ul {
  list-style: none;
}

.nav-menu li {
  padding: 15px 20px;
}

.nav-menu li.active {
  background-color: #8a9a80;
}

.nav-menu a {
  color: white;
  text-decoration: none;
  display: block;
  font-family: 'Libre Baskerville', serif;
}

.logout-container {
  margin-top: auto;
  padding: 20px;
}

.logout-btn {
  width: 100%;
  padding: 10px;
  background-color: #5a6a55;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

/* Main content styles */
.main-content {
  flex: 1;
  padding: 20px 30px;
}

.dashboard-title {
  font-size: 28px;
  margin-bottom: 20px;
  color: #333;
}

.content-section {
  background-color: #EFEBE2;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 20px;
}

.section-title {
  color: #5a3e2b;
  font-size: 24px;
  margin-bottom: 10px;
  font-weight: bold;
}

.divider {
  height: 1px;
  background-color: #d3c9b6;
  margin-bottom: 15px;
}

/* Message details styles */
.message-details {
  background-color: #f9f7f3;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.message-details p {
  margin-bottom: 10px;
  line-height: 1.5;
}

.message-details strong {
  color: #5a3e2b;
  font-weight: bold;
}

/* Form styles */
.reply-form {
  max-width: 100%;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
  color: #5a3e2b;
}

.form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #d3c9b6;
  border-radius: 4px;
  background-color: #fff;
  font-family: 'Libre Baskerville', serif;
  font-size: 14px;
}

textarea.form-control {
  min-height: 200px;
  resize: vertical;
}

.submit-btn {
  padding: 10px 20px;
  background-color: #7d5a50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  font-family: 'Libre Baskerville', serif;
  transition: background-color 0.3s;
}

.submit-btn:hover {
  background-color: #6a4c43;
}

/* Status messages */
.success-message {
  background-color: #dff0d8;
  color: #3c763d;
  padding: 10px;
  margin-bottom: 20px;
  border-radius: 4px;
}

.error-message {
  background-color: #f2dede;
  color: #a94442;
  padding: 10px;
  margin-bottom: 20px;
  border-radius: 4px;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <a href="/">
                    <img src="../asset/logo.png" alt="Bask Cafe Logo" id="logo">
                  </a>
            </div>
            <div class="admin-text">Admin</div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="testimonials.php">Testimonials</a></li>
                    <li class="active"><a href="messages.php">Messages</a></li>
                </ul>
            </nav>
            <div class="logout-container">
                <a href="logout.php"><button class="logout-btn">Logout</button></a>
            </div>
        </div>
        <div class="main-content">
            <h1 class="dashboard-title">Reply to Message</h1>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="content-section">
                <h2 class="section-title">Message Details</h2>
                <div class="divider"></div>
                
                <div class="message-details">
                    <p><strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($message['contact_no']); ?></p>
                    <p><strong>Message:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
                    <p><strong>Received:</strong> <?php echo date('F j, Y, g:i a', strtotime($message['created_at'])); ?></p>
                </div>
            </div>
            
            <div class="content-section">
                <h2 class="section-title">Compose Reply</h2>
                <div class="divider"></div>
                
                <form class="reply-form" method="POST">
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" value="Re: Your message to Bask Cafe" required>
                    </div>
                    <div class="form-group">
                        <label for="reply">Your Reply</label>
                        <textarea class="form-control" id="reply" name="reply" rows="10" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Send Reply</button>
                </form>
            </div>
        </div>
    </div>
    <script>
document.querySelector('.reply-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submit

    const form = e.target;
    const formData = new FormData(form);
    formData.append('id', '<?php echo $id; ?>'); // Append message ID

    fetch('reply_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageBox = document.createElement('div');
        messageBox.classList.add(data.success ? 'success-message' : 'error-message');
        messageBox.innerText = data.message;

        form.parentNode.insertBefore(messageBox, form);

        if (data.success) {
            form.reset();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
    </script>
</body>
</html>