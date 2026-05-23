document.addEventListener("DOMContentLoaded", () => {
  // ---- 1. FETCH REVIEWS ----
  const homeContainer = document.getElementById("recentReviewsContainer");
  const fullContainer = document.getElementById("fullReviewsContainer");

  const container = homeContainer || fullContainer;

  if (container) {
    // If on home page, limit to 3. If on review page, fetch all (or limit to something high)
    // Using 0 or a high number for "all". get_reviews.php handles logic.
    // Let's explicitly say 3 for home, and 100 for review page.
    const limit = homeContainer ? 3 : 100;

    // Use API_CONFIG.BASE_URL if it exists, otherwise fall back to relative path (for local non-hybrid test)
    const baseUrl =
      typeof API_CONFIG !== "undefined" && API_CONFIG.BASE_URL
        ? API_CONFIG.BASE_URL
        : "";
    const url = `${baseUrl}/get_reviews.php?limit=${limit}`;

    console.log(`Fetching reviews from: ${url}`); // Debugging

    fetch(url)
      .then((res) => {
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        return res.json();
      })
      .then((data) => {
        container.innerHTML = ""; // Clear "Loading..."

        if (data.length === 0) {
          container.innerHTML = `<div style="text-align:center; width:100%;">No reviews yet.</div>`;
          return;
        }

        data.forEach((review, index) => {
          const rating = parseInt(review.rating);
          const stars = [...Array(5)]
            .map(
              (_, i) =>
                `<span class="star${i < rating ? " filled" : ""}">&#9733;</span>`,
            )
            .join("");

          // For the full review page, we might want to hide some initially (like the original PHP did > 6)
          // But for simplicity/hybrid, let's just show all or let CSS handle it.
          // The original PHP logic: $is_hidden = $counter > 6 ? ' hidden_review' : '';
          let isHiddenClass = "";
          if (fullContainer && index >= 6) {
            isHiddenClass = " hidden_review";
            // Ensure View More button is visible if there are hidden reviews
            const viewMoreBtn = document.getElementById("viewMoreBtn");
            if (viewMoreBtn) viewMoreBtn.style.display = "inline-block";
          } else if (fullContainer && index < 6) {
            // Ensure View More button is hidden if few reviews (logic handled by default display:none)
          }

          // Handle potential XSS by escaping is done by textContent usually, but here we are building HTML string.
          // We should be careful. The original PHP used htmlspecialchars.
          // In JS, we can use a helper or assign textContent.
          // For safety, let's escape function:
          const escape = (str) => {
            const p = document.createElement("p");
            p.textContent = str;
            return p.innerHTML;
          };

          const card = `
                        <div class="customer_review_card${isHiddenClass}" ${isHiddenClass ? 'style="display:none"' : ""}>
                        <div class="review_avatar_wrapper">
                            <svg class="review_avatar" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M0 216C0 149.7 53.7 96 120 96l8 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-8 0c-30.9 0-56 25.1-56 56l0 8 64 0c35.3 0 64 28.7 64 64l0 64c0 35.3-28.7 64-64 64l-64 0c-35.3 0-64-28.7-64-64l0-32 0-32 0-72zm256 0c0-66.3 53.7-120 120-120l8 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-8 0c-30.9 0-56 25.1-56 56l0 8 64 0c35.3 0 64 28.7 64 64l0 64c0 35.3-28.7 64-64 64l-64 0c-35.3 0-64-28.7-64-64l0-32 0-32 0-72z"/>
                            </svg>
                        </div>
                        <div class="review_name">${escape(review.name)}</div>
                        <hr class="review_line"/>
                        <div class="review_stars">${stars}</div>
                        <div class="review_text_body">“${escape(review.review)}”</div>
                        </div>
                    `;
          container.innerHTML += card;
        });
      })
      .catch((err) => {
        console.error("Failed to load reviews:", err);
        container.innerHTML = `<div style="text-align:center; color:red;">Failed to load reviews. Please try again later.</div>`;
      });
  }

  // ---- 2. HANDLE VIEW MORE ----
  const viewMoreBtn = document.getElementById("viewMoreBtn");
  if (viewMoreBtn) {
    viewMoreBtn.addEventListener("click", function () {
      const hiddenReviews = document.querySelectorAll(".hidden_review");
      if (hiddenReviews.length === 0) return;

      // Check specific logic: are they currently shown?
      // Note: In the loop above, I set style="display:none" for hidden ones.
      const isHidden = hiddenReviews[0].style.display === "none";

      if (isHidden) {
        hiddenReviews.forEach((card) => (card.style.display = "block"));
        viewMoreBtn.textContent = "View Less";
      } else {
        hiddenReviews.forEach((card) => (card.style.display = "none"));
        viewMoreBtn.textContent = "View More";
        document
          .querySelector(".review_grid_container")
          .scrollIntoView({ behavior: "smooth" });
      }
    });
  }

  // ---- 3. POST REVIEW ----
  const reviewForm = document.getElementById("reviewForm");
  if (reviewForm) {
    reviewForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const baseUrl =
        typeof API_CONFIG !== "undefined" && API_CONFIG.BASE_URL
          ? API_CONFIG.BASE_URL
          : "";
      const submitUrl = `${baseUrl}/post_review.php`;

      // Display loading or disable button here if desired
      const submitBtn = reviewForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.textContent;
      submitBtn.textContent = "Submitting...";
      submitBtn.disabled = true;

      fetch(submitUrl, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert("Thank you for your review! We appreciate your feedback.");
            reviewForm.reset();
            // Optional: reload reviews so the new one appears (if it was approved instantly, but logic says approved only)
          } else {
            alert("Error: " + data.message);
          }
        })
        .catch((error) => {
          alert("An error occurred: " + error.message);
          console.error(error);
        })
        .finally(() => {
          submitBtn.textContent = originalBtnText;
          submitBtn.disabled = false;
        });
    });
  }
});
