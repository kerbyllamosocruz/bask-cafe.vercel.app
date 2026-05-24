<?php
require_once '../../api/config/db_connect.php';
// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Update testimonial status to declined
    $query = "UPDATE bask_reviews SET status = 'declined' WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        // Redirect back to dashboard with success message
        header("Location: ../index.php?success=Testimonial declined successfully");
        exit();
    } else {
        // Redirect back with error message
        header("Location: ../index.php?error=Error declining testimonial: " . mysqli_error($conn));
        exit();
    }
} else {
    // Redirect back with error message
    header("Location: ../index.php?error=Invalid testimonial ID");
    exit();
}
?>