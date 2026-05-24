<?php
session_start();
require_once '../api/config/db_connect.php';

function displayError($message) {
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login Error</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .container {
                background-color: white;
                padding: 30px 25px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                text-align: center;
                width: 350px;
            }
            h2 {
                margin-bottom: 20px;
                color: #d9534f;
            }
            p {
                font-size: 16px;
                margin-bottom: 25px;
            }
            .button {
                padding: 10px 20px;
                background-color: #5cb85c;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                display: inline-block;
            }
            .button:hover {
                background-color: #4cae4c;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h2>Login Failed</h2>
        <p>' . htmlspecialchars($message) . '</p>
        <a class="button" href="/">Back to Login</a>
    </div>
    </body>
    </html>
    ';
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // user-input password

    if (empty($email) || empty($password)) {
        displayError("Both fields are required!");
    }

    // Prepare and execute query to fetch password_hash for the given email
    $stmt = $conn->prepare("SELECT password_hash FROM bask_admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($password_hash); // password_hash from DB
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            // ✅ SESSION SET HERE
            $_SESSION['email'] = $email;
            $_SESSION['logged_in'] = true;

            // Redirect to dashboard
            header("Location: ../dashboard/");
            exit();
        } else {
            displayError("Invalid password, please try again.");
        }
    } else {
        displayError("Invalid email, please try again.");
    }

    $stmt->close();
    $conn->close();
}
?>