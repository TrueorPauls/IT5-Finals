<?php
session_start();
include("config.php");

$logged_in = isset($_SESSION['user']);
$cart_count = 0;
if ($logged_in) {
    $uid_res = mysqli_query($conn, "SELECT id FROM users WHERE email='" . mysqli_real_escape_string($conn, $_SESSION['user']) . "'");
    $uid_row = mysqli_fetch_assoc($uid_res);
    if ($uid_row) {
        $cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id={$uid_row['id']}");
        $cart_count = (int)(mysqli_fetch_assoc($cc)['total'] ?? 0);
    }
}

// Fetch recent reviews from DB
$reviews_res = mysqli_query($conn, "SELECT * FROM reviews ORDER BY created_at DESC LIMIT 20");
$reviews = [];
while ($r = mysqli_fetch_assoc($reviews_res)) $reviews[] = $r;

$positive = count(array_filter($reviews, fn($r) => $r['sentiment'] === 'positive'));
$negative = count($reviews) - $positive;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews – Kabesera Cafe</title>
    <link rel="stylesheet" href="reviews.css">
    
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <a href="index.php"><img src="qt=q_95.webp" alt="Kabesera Cafe"></a>
        <a id="kabhome" href="index.php">Kabesera Cafe</a>
    </div>
    <ul>
        <li><a href="food.php">Food</a></li>
        <li><a href="coffee.php">Coffee</a></li>
        <li><a href="events.html">Events</a></li>
        <li><a href="reviews.php" style="color:#22c55e;">Reviews</a></li>
        <?php if ($logged_in): ?>
        <li><a href="cart.php">🛒 Cart<?php if ($cart_count > 0): ?><span class="cart-badge"><?= $cart_count ?></span><?php endif; ?></a></li>
        <li><a href="order_history.php">My Orders</a></li>
        <li><a href="logout.php" class="btn">Log Out</a></li>
        <?php else: ?>
        <li><a href="login.php" class="btn">Log In</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="reviews-wrap">

    <div class="reviews-header">
        <h1>☕ Customer Reviews</h1>
        <p>Share your experience at Kabesera Cafe. Our AI will analyze your feedback.</p>
    </div>

    <!-- SENTIMENT SUMMARY -->
    <?php if (!empty($reviews)): ?>
    <div class="sentiment-summary">
        <div class="sentiment-card positive-card">
            <div class="num"><?= $positive ?></div>
            <div class="lbl">😊 Positive</div>
        </div>
        <div class="sentiment-card">
            <div class="num" style="color:#555"><?= count($reviews) ?></div>
            <div class="lbl">📝 Total</div>
        </div>
        <div class="sentiment-card negative-card">
            <div class="num"><?= $negative ?></div>
            <div class="lbl">😞 Negative</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- REVIEW FORM -->
    <div class="review-form-box">
        <h2>✍️ Leave a Review</h2>

        <?php if ($logged_in): ?>
        <textarea id="review-input" placeholder="Tell us about your experience at Kabesera Cafe..."></textarea>
        <br>
        <button class="submit-review-btn" id="submit-btn" onclick="submitReview()">
            Analyze & Submit Review
        </button>
        <div class="result-card" id="result-card"></div>

        <?php else: ?>
        <div class="login-required">
            <span class="lock-icon">🔒</span>
            <p>You need to be logged in to leave a review.</p>
            <a href="login.php" class="login-to-review-btn">Log In to Review</a>
            <span class="or-text">or</span>
            <a href="signup.php" class="login-to-review-btn signup-btn">Sign Up</a>
        </div>
        <?php endif; ?>

    </div>

    <!-- PAST REVIEWS -->
    <div class="reviews-list">
        <h2>Recent Reviews</h2>
        <div id="reviews-container">
            <?php if (empty($reviews)): ?>
            <div class="no-reviews">No reviews yet. Be the first to share your experience! ☕</div>
            <?php else: ?>
            <?php foreach ($reviews as $r): ?>
            <div class="review-card">
                <div class="sentiment-icon"><?= $r['sentiment'] === 'positive' ? '😊' : '😞' ?></div>
                <div class="review-body">
                    <div class="reviewer-name"><?= htmlspecialchars($r['reviewer_name']) ?></div>
                    <div class="review-text"><?= htmlspecialchars($r['review_text']) ?></div>
                    <div class="review-meta">
                        <span class="sentiment-badge badge-<?= $r['sentiment'] ?>"><?= $r['sentiment'] ?></span>
                        <span class="review-date"><?= date('M j, Y g:i A', strtotime($r['created_at'])) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
async function submitReview() {
    const textarea  = document.getElementById('review-input');
    const btn       = document.getElementById('submit-btn');
    const resultBox = document.getElementById('result-card');
    const review    = textarea.value.trim();

    if (!review) {
        showResult('error', '⚠️ Please write a review before submitting.');
        return;
    }

    // Loading state
    btn.disabled    = true;
    btn.textContent = 'Analyzing...';
    resultBox.style.display = 'none';

    try {
        const formData = new FormData();
        formData.append('review', review);

        const res  = await fetch('submit_review.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.error) {
            showResult('error', '⚠️ ' + data.error);
        } else {
            const icon    = data.sentiment === 'positive' ? '😊' : '😞';
            const label   = data.sentiment === 'positive' ? 'Positive' : 'Negative';
            const message = data.sentiment === 'positive'
                ? 'Thank you! Our AI detected a <strong>positive</strong> sentiment. We\'re glad you enjoyed it! 🎉'
                : 'Thank you for your feedback. Our AI detected a <strong>negative</strong> sentiment. We\'ll work to improve! 🙏';

            showResult(data.sentiment, icon + ' ' + message);

            // Prepend new review card to list without reloading
            prependReview(data.reviewer, review, data.sentiment);
            textarea.value = '';
        }
    } catch (err) {
        showResult('error', '⚠️ Could not connect to the review service. Is the Flask API running?');
    }

    btn.disabled    = false;
    btn.textContent = 'Analyze & Submit Review';
}

function showResult(type, message) {
    const box = document.getElementById('result-card');
    box.className  = 'result-card result-' + type;
    box.innerHTML  = message;
    box.style.display = 'block';
}

function prependReview(name, text, sentiment) {
    const container = document.getElementById('reviews-container');
    const noReviews = container.querySelector('.no-reviews');
    if (noReviews) noReviews.remove();

    const icon  = sentiment === 'positive' ? '😊' : '😞';
    const now   = new Date().toLocaleString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'numeric', minute:'2-digit' });

    const card = document.createElement('div');
    card.className = 'review-card';
    card.style.animation = 'fadeIn 0.4s ease';
    card.innerHTML = `
        <div class="sentiment-icon">${icon}</div>
        <div class="review-body">
            <div class="reviewer-name">${escHtml(name)}</div>
            <div class="review-text">${escHtml(text)}</div>
            <div class="review-meta">
                <span class="sentiment-badge badge-${sentiment}">${sentiment}</span>
                <span class="review-date">${now}</span>
            </div>
        </div>`;
    container.insertBefore(card, container.firstChild);
}

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

</body>
</html>
