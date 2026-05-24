<?php
require_once '../api/config/db_connect.php'; // Make sure your DB connection works

// Example user info (replace with form input if needed)
$email = 'admin@baskcafe.site';
$password = 'admin';

// Step 1: Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Step 2: Prepare SQL query
$sql = "INSERT INTO bask_admin (email, password_hash) VALUES (?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Step 3: Bind parameters and execute
$stmt->bind_param("ss", $email, $hashed_password);

if ($stmt->execute()) {
    echo "✅ User created successfully.";
} else {
    echo "❌ Error creating user: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
