<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../api/config/db_connect.php';

// Handle status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$status_condition = ($status_filter !== 'all') ? "WHERE status = '$status_filter'" : "";

// Handle rating filter
$rating_filter = isset($_GET['rating']) ? $_GET['rating'] : 'all';
if ($rating_filter !== 'all') {
    $rating_condition = ($status_condition === "") ? "WHERE rating = '$rating_filter'" : "AND rating = '$rating_filter'";
} else {
    $rating_condition = "";
}

// Fetch testimonials with filters
$testimonials_query = "SELECT * FROM bask_reviews $status_condition $rating_condition ORDER BY created_at DESC";
$testimonials_result = mysqli_query($conn, $testimonials_query);

// Handle approve/decline actions if submitted
if (isset($_POST['action']) && isset($_POST['testimonial_id'])) {
    $action = $_POST['action'];
    $id = mysqli_real_escape_string($conn, $_POST['testimonial_id']);
    
    if ($action === 'approve') {
        $update_query = "UPDATE bask_reviews SET status = 'approved' WHERE id = '$id'";
        $success_message = "Testimonial approved successfully!";
    } else if ($action === 'decline') {
        $update_query = "UPDATE bask_reviews SET status = 'declined' WHERE id = '$id'";
        $success_message = "Testimonial declined successfully!";
    }
    
    if (mysqli_query($conn, $update_query)) {
        // Refresh the page to show updated data
        header("Location: testimonials.php?status=$status_filter&rating=$rating_filter&success=$success_message");
        exit();
    } else {
        $error_message = "Error updating testimonial: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials - Bask Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-label {
            font-weight: bold;
            color: #5a3e2b;
        }
        .filter-select {
            padding: 6px 10px;
            border: 1px solid #d3c9b6;
            border-radius: 4px;
            background-color: white;
        }
        .filter-button {
            padding: 6px 12px;
            background-color: #7d5a50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #f0ad4e;
            color: white;
        }
        .status-approved {
            background-color: #5cb85c;
            color: white;
        }
        .status-declined {
            background-color: #d9534f;
            color: white;
        }
        .rating-stars {
            color: #f8d64e;
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
                    <li class="active"><a href="testimonials.php">Testimonials</a></li>
                    <li><a href="messages.php">Messages</a></li>
                </ul>
            </nav>
            <div class="logout-container">
                <a href="logout.php"><button class="logout-btn">Logout</button></a>
            </div>
        </div>
        <div class="main-content">
            <h1 class="dashboard-title">Testimonials</h1>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="content-section">
                <h2 class="section-title">Filter Testimonials</h2>
                <div class="divider"></div>
                
                <form method="GET" action="testimonials.php">
                    <div class="filter-container">
                        <div class="filter-group">
                            <label class="filter-label" for="status">Status:</label>
                            <select class="filter-select" id="status" name="status">
                                <option value="all" <?php echo ($status_filter === 'all') ? 'selected' : ''; ?>>All</option>
                                <option value="pending" <?php echo ($status_filter === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo ($status_filter === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                <option value="declined" <?php echo ($status_filter === 'declined') ? 'selected' : ''; ?>>Declined</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label" for="rating">Rating:</label>
                            <select class="filter-select" id="rating" name="rating">
                                <option value="all" <?php echo ($rating_filter === 'all') ? 'selected' : ''; ?>>All</option>
                                <option value="5" <?php echo ($rating_filter === '5') ? 'selected' : ''; ?>>5 Stars</option>
                                <option value="4" <?php echo ($rating_filter === '4') ? 'selected' : ''; ?>>4 Stars</option>
                                <option value="3" <?php echo ($rating_filter === '3') ? 'selected' : ''; ?>>3 Stars</option>
                                <option value="2" <?php echo ($rating_filter === '2') ? 'selected' : ''; ?>>2 Stars</option>
                                <option value="1" <?php echo ($rating_filter === '1') ? 'selected' : ''; ?>>1 Star</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="filter-button">Apply Filters</button>
                    </div>
                </form>
            </div>
            
            <div class="content-section">
                <h2 class="section-title">Customer Testimonials</h2>
                <div class="divider"></div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($testimonials_result) > 0) {
                            while ($row = mysqli_fetch_assoc($testimonials_result)) {
                                // Generate star rating display
                                $stars = '';
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $row['rating']) {
                                        $stars .= '★';
                                    } else {
                                        $stars .= '☆';
                                    }
                                }
                                
                                // Generate status badge class
                                $status_class = 'status-' . $row['status'];
                                
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td><span class='rating-stars'>" . $stars . "</span></td>";
                                echo "<td>" . htmlspecialchars($row['review']) . "</td>";
                                echo "<td><span class='status-badge " . $status_class . "'>" . ucfirst($row['status']) . "</span></td>";
                                echo "<td>" . date('M d, Y H:i:s', strtotime($row['created_at'])) . "</td>";
                                echo "<td>";
                                
                                // Only show approve/decline buttons for pending testimonials
                                if ($row['status'] === 'pending') {
                                    echo "<form method='POST' style='display:inline-block; margin-right: 5px;'>";
                                    echo "<input type='hidden' name='testimonial_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='approve'>";
                                    echo "<button type='submit' class='approve-btn'>Approve</button>";
                                    echo "</form>";
                                    
                                    echo "<form method='POST' style='display:inline-block;'>";
                                    echo "<input type='hidden' name='testimonial_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='decline'>";
                                    echo "<button type='submit' class='decline-btn'>Decline</button>";
                                    echo "</form>";
                                } else if ($row['status'] === 'approved') {
                                    echo "<form method='POST' style='display:inline-block;'>";
                                    echo "<input type='hidden' name='testimonial_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='decline'>";
                                    echo "<button type='submit' class='decline-btn'>Decline</button>";
                                    echo "</form>";
                                } else if ($row['status'] === 'declined') {
                                    echo "<form method='POST' style='display:inline-block;'>";
                                    echo "<input type='hidden' name='testimonial_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='approve'>";
                                    echo "<button type='submit' class='approve-btn'>Approve</button>";
                                    echo "</form>";
                                }
                                
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No testimonials found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>