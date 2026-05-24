<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once '../../api/config/db_connect.php';

// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "UPDATE bask_reviews SET status = 'approved' WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: ../index.php?success=Testimonial approved successfully");
        exit();
    } else {
        header("Location: ../index.php?error=Error approving testimonial: " . mysqli_error($conn));
        exit();
    }
} else {
    header("Location: ../index.php?error=Invalid testimonial ID");
    exit();
}
?>