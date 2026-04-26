<?php
// submit_review.php
// Receives review from the form, calls Flask ML API, saves result to DB, returns JSON
session_start();
include("config.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$review = trim($_POST['review'] ?? '');

if (empty($review)) {
    echo json_encode(['error' => 'Please write a review first.']);
    exit;
}

if (strlen($review) < 5) {
    echo json_encode(['error' => 'Review is too short.']);
    exit;
}

// ── Call Flask ML API ─────────────────────────────────────────────────────────
$api_url = 'http://localhost:5000/predict';
$payload = json_encode(['review' => $review]);

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    echo json_encode(['error' => 'Could not reach the review analysis service. Please try again later.']);
    exit;
}

$result = json_decode($response, true);

if (!$result || isset($result['error'])) {
    echo json_encode(['error' => 'Analysis failed. Please try again.']);
    exit;
}

// prediction: 1 = positive, 0 = negative
$prediction_raw  = $result['prediction'];
$sentiment       = ($prediction_raw == '1') ? 'positive' : 'negative';

// ── Save to DB ────────────────────────────────────────────────────────────────
// Get user info if logged in
$user_id   = $_SESSION['user_id'] ?? null;
$user_name = 'Anonymous';

if ($user_id) {
    $u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT full_name, email FROM users WHERE id=$user_id"));
    $user_name = $u['full_name'] ?: $u['email'];
}

$review_escaped = mysqli_real_escape_string($conn, $review);
$sentiment_esc  = mysqli_real_escape_string($conn, $sentiment);
$name_esc       = mysqli_real_escape_string($conn, $user_name);
$uid_val        = $user_id ? $user_id : 'NULL';

mysqli_query($conn, "INSERT INTO reviews (user_id, reviewer_name, review_text, sentiment, created_at)
                     VALUES ($uid_val, '$name_esc', '$review_escaped', '$sentiment_esc', NOW())");

echo json_encode([
    'success'    => true,
    'review'     => $review,
    'sentiment'  => $sentiment,
    'reviewer'   => $user_name,
]);
?>
