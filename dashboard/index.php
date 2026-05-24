<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login if not logged in
    header("Location: /login/");
    exit;
}

require_once '../api/config/db_connect.php';
$testimonials_query = "SELECT * FROM bask_reviews ORDER BY created_at DESC";
$testimonials_result = mysqli_query($conn, $testimonials_query);

$messages_query = "SELECT * FROM bask_contact ORDER BY created_at DESC";
$messages_result = mysqli_query($conn, $messages_query);
?>

<script>
  let timeout;
  function resetTimer() {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      window.location.href = '/dashboard/logout.php';
    }, 10 * 60 * 1000);
  }

  window.onload = resetTimer;
  document.onmousemove = resetTimer;
  document.onkeydown = resetTimer;
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bask Café Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="icon" type="image" href="../asset/favicon.jpg">
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
                    <li class="active"><a href="index.php">Dashboard</a></li>
                    <li><a href="testimonials.php">Testimonials</a></li>
                    <li><a href="messages.php">Messages</a></li>
                </ul>
            </nav>
            <div class="logout-container">
                <a href="logout.php"><button class="logout-btn">Logout</button></a>
            </div>
        </div>
        <div class="main-content">
            <h1 class="dashboard-title">Dashboard</h1>
            
            <div class="content-section">
                <h2 class="section-title">Customer Testimonials</h2>
                <div class="divider"></div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    if (mysqli_num_rows($testimonials_result) > 0) {
        while ($row = mysqli_fetch_assoc($testimonials_result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['rating']) . "/5</td>";
            echo "<td>" . htmlspecialchars($row['review']) . "</td>";
            echo "<td><a href='actions/approve_testimonial.php?id=" . $row['id'] . "'><button class='approve-btn'>Approve</button></a></td>";
            echo "<td><a href='actions/decline_testimonial.php?id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to decline this testimonial?');\"><button class='decline-btn'>Decline</button></a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No testimonials found</td></tr>";
    }
    ?>
</tbody>

                </table>
            </div>
            
            <div class="content-section">
                <h2 class="section-title">Customer Messages</h2>
                <div class="divider"></div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($messages_result) > 0) {
                            while ($row = mysqli_fetch_assoc($messages_result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['contact_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                                echo "<td><a href='reply.php?id=" . $row['id'] . "'><button class='reply-btn'>Reply via Email</button></a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No messages found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>