<?php
// Include database connection
require_once '../api/config/db_connect.php';

// Handle status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$status_condition = ($status_filter !== 'all') ? "WHERE status = '$status_filter'" : "";

// Handle search filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_condition = ($status_condition === "") 
        ? "WHERE (name LIKE '%$search%' OR email LIKE '%$search%' OR message LIKE '%$search%')" 
        : "AND (name LIKE '%$search%' OR email LIKE '%$search%' OR message LIKE '%$search%')";
} else {
    $search_condition = "";
}

// Fetch messages with filters
$messages_query = "SELECT * FROM bask_contact $status_condition $search_condition ORDER BY created_at DESC";
$messages_result = mysqli_query($conn, $messages_query);

// Handle mark as read action
if (isset($_POST['action']) && isset($_POST['message_id'])) {
    $action = $_POST['action'];
    $id = mysqli_real_escape_string($conn, $_POST['message_id']);
    
    if ($action === 'mark_read') {
        $update_query = "UPDATE messages SET status = 'read' WHERE id = '$id'";
        $success_message = "Message marked as read!";
    }
    
    if (mysqli_query($conn, $update_query)) {
        // Refresh the page to show updated data
        header("Location: messages.php?status=$status_filter&search=$search&success=$success_message");
        exit();
    } else {
        $error_message = "Error updating message: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Bask Dashboard</title>
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
        .filter-input {
            padding: 6px 10px;
            border: 1px solid #d3c9b6;
            border-radius: 4px;
            width: 200px;
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
        .status-unread {
            background-color: #f0ad4e;
            color: white;
        }
        .status-read {
            background-color: #5bc0de;
            color: white;
        }
        .status-replied {
            background-color: #5cb85c;
            color: white;
        }
        .message-row-unread {
            font-weight: bold;
            background-color: #f9f7f3;
        }
        .mark-read-btn {
            padding: 6px 10px;
            background-color: #5bc0de;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
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
            <h1 class="dashboard-title">Customer Messages</h1>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="content-section">
                <h2 class="section-title">Filter Messages</h2>
                <div class="divider"></div>
                
                <form method="GET" action="messages.php">
                    <div class="filter-container">
                        <div class="filter-group">
                            <label class="filter-label" for="status">Status:</label>
                            <select class="filter-select" id="status" name="status">
                                <option value="all" <?php echo ($status_filter === 'all') ? 'selected' : ''; ?>>All</option>
                                <option value="unread" <?php echo ($status_filter === 'unread') ? 'selected' : ''; ?>>Unread</option>
                                <option value="read" <?php echo ($status_filter === 'read') ? 'selected' : ''; ?>>Read</option>
                                <option value="replied" <?php echo ($status_filter === 'replied') ? 'selected' : ''; ?>>Replied</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label" for="search">Search:</label>
                            <input type="text" class="filter-input" id="search" name="search" placeholder="Search name, email, message..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        
                        <button type="submit" class="filter-button">Apply Filters</button>
                    </div>
                </form>
            </div>
            
            <div class="content-section">
                <h2 class="section-title">Messages</h2>
                <div class="divider"></div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($messages_result) > 0) {
                            while ($row = mysqli_fetch_assoc($messages_result)) {
                                // Generate status badge class
                                $status_class = 'status-' . $row['status'];
                                
                                // Add special class for unread messages
                                $row_class = ($row['status'] === 'unread') ? 'message-row-unread' : '';
                                
                                echo "<tr class='$row_class'>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['contact_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                                echo "<td><span class='status-badge " . $status_class . "'>" . ucfirst($row['status']) . "</span></td>";
                                echo "<td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                                echo "<td>";
                                
                                // Show reply button for all messages
                                echo "<a href='reply.php?id=" . $row['id'] . "'><button class='reply-btn'>Reply</button></a> ";
                                
                                // Show mark as read button for unread messages
                                if ($row['status'] === 'unread') {
                                    echo "<form method='POST' style='display:inline-block; margin-top: 5px;'>";
                                    echo "<input type='hidden' name='message_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='mark_read'>";
                                    echo "<button type='submit' class='mark-read-btn'>Mark as Read</button>";
                                    echo "</form>";
                                }
                                
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No messages found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>