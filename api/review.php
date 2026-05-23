<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once('config/db_connect.php');

$query = "SELECT * FROM bask_reviews WHERE status = 'approved' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bask Café</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="icon" type="image" href="asset/favicon.jpg" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Lobster&display=swap" rel="stylesheet" />
</head>
<body>
  <header class="main_header">
    <div class="header_container">
      <div class="logo">
        <a href="/">
          <img src="asset/logo.png" alt="Bask Cafe Logo" id="logo" />
        </a>
      </div>
      <nav class="main_nav" id="mainNav">
        <a href="/index.html">Home</a>
        <a href="/about.html">About Us</a>
        <a href="/menu.html">Menu</a>
        <a href="/gallery.html">Gallery</a>
        <a href="/contact.html">Contact</a>
        <a href="/review.php" class="active">Review</a>
      </nav>
      <div class="hamburger" id="hamburger">☰</div>
    </div>
  </header>

  <div class="header_img_container">
    <img src="asset/index/header_img.png" width="100%" />
    <div class="header-text">
      <h1>Café Stories</h1>
      <p>What our customers love about Bask Café—real stories, real flavors.</p>
    </div>
  </div>
  <div class="review_container">
    <<iframe
  class="review_vid"
  width="560"
  height="315"
  src="https://www.youtube.com/embed/OC87OWn5Stc"
  title="Customer Interview"
  frameborder="0"
  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
  allowfullscreen>
</iframe>
    <div class="review_text">
      <h1>Hear from Our Customers</h1>
      <hr />
      <p>See what our customers have to say! Watch their stories and hear about their favorite Bask Café moments.</p>
    </div>
  </div>
      <div class="review_border_box">
    <h1>What Our Customers Are Saying</h1>
  </div>

  

  <?php if (mysqli_num_rows($result) > 0): ?>
  <div class="review_grid_container">
    <?php
      $counter = 0;
      while ($row = mysqli_fetch_assoc($result)):
        $counter++;
        $is_hidden = $counter > 6 ? ' hidden_review' : '';
    ?>
      <div class="customer_review_card<?= $is_hidden ?>">
        <div class="review_avatar_wrapper">
          <svg class="review_avatar" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
            <path d="M0 216C0 149.7 53.7 96 120 96l8 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-8 0c-30.9 0-56 25.1-56 56l0 8 64 0c35.3 0 64 28.7 64 64l0 64c0 35.3-28.7 64-64 64l-64 0c-35.3 0-64-28.7-64-64l0-32 0-32 0-72zm256 0c0-66.3 53.7-120 120-120l8 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-8 0c-30.9 0-56 25.1-56 56l0 8 64 0c35.3 0 64 28.7 64 64l0 64c0 35.3-28.7 64-64 64l-64 0c-35.3 0-64-28.7-64-64l0-32 0-32 0-72z"/>
          </svg>
        </div>
        <div class="review_name"><?= htmlspecialchars($row['name']) ?></div>
        <hr class="review_line"/>
        <div class="review_stars">
          <?php
            $rating = (int)$row['rating'];
            for ($i = 1; $i <= 5; $i++) {
              echo '<span class="star'.($i <= $rating ? ' filled' : '').'">&#9733;</span>';
            }
          ?>
        </div>
        <div class="review_text_body">
          “<?= htmlspecialchars($row['review']) ?>”
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <?php if ($counter > 6): ?>
    <div style="text-align: center; margin-top: 2rem;">
      <button id="viewMoreBtn" class="view_more_btn">View More</button>
    </div>
  <?php endif; ?>

<?php else: ?>
  <div style="text-align:center; padding: 2rem;">No customer reviews yet.</div>
<?php endif; ?>

  
  <div class="review_form_container">
      <h2 class="review_form_title">Share Your Experience</h2>
      <form id="reviewForm" action="post_review.php" method="POST">
        <input type="text" name="name" id="name" placeholder="Your Name" required />

        <select name="rating" id="rating" required>
          <option value="" disabled selected>Rating (1-5)</option>
          <option value="5">5 - Excellent</option>
          <option value="4">4 - Very Good</option>
          <option value="3">3 - Good</option>
          <option value="2">2 - Fair</option>
          <option value="1">1 - Poor</option>
        </select>

        <textarea name="review" id="review" rows="4" placeholder="Write your review..." required></textarea>

        <div class="review_button-container">
          <button type="submit">Submit</button>
        </div>
      </form>
  </div>
  <footer>
    <div class="footer_container">
      <div class="footer_content">
        <div class="logo">
          <a href="/">
            <img src="asset/logo.png" alt="Bask Cafe Logo" id = "logo">
            
          </a>
        </div>
        
        <nav class="nav_menu">
            <a href="/about.html">About Us</a>
            <a href="/menu.html">Menu</a>
            <a href="/gallery.html">Gallery</a>
            <a href="/contact.html">Contact</a>
            <a href="/review.php">Review</a>
          </nav>
      </div>
      
      <div class="footer_bottom">
        <div class="copyright">Bask Café&copy; 2025. All rights reserved.</div>
          <div class="footer_center">Designed & Developed by Aguilar & Cruz</div>

        <div class="social_icons">
          <a href="https://www.facebook.com/profile.php?id=61569599993182" aria-label="Facebook" target="_blank">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
            </svg>
          </a>
          <a href="https://www.instagram.com/baskcafe_/" aria-label="Instagram"target="_blank">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </footer>
  <script src="script.js" defer></script>
  <script src="hamburger.js" defer></script>
  <script>
document.getElementById("reviewForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("post_review.php", {
    method: "POST",
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Thank you for your review! We appreciate your feedback.");
        document.getElementById("reviewForm").reset();
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch(error => {
      alert("An error occurred: " + error.message);
    });
});

  document.addEventListener("DOMContentLoaded", function () {
    const viewMoreBtn = document.getElementById("viewMoreBtn");
    const hiddenReviews = document.querySelectorAll(".hidden_review");

    if (!viewMoreBtn) return;

    viewMoreBtn.addEventListener("click", function () {
      const isHidden = hiddenReviews[0].style.display === 'none' || hiddenReviews[0].style.display === '';

      if (isHidden) {
        hiddenReviews.forEach(card => card.style.display = 'block');
        viewMoreBtn.textContent = 'View Less';
      } else {
        hiddenReviews.forEach(card => card.style.display = 'none');
        viewMoreBtn.textContent = 'View More';

        document.querySelector('.review_grid_container').scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
</script>
</body>
</html>
