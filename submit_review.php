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
$api_url = 'https://api-tyqn.onrender.com/predict';

$data = json_encode(['review' => $review]);

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => $data,
        'timeout' => 30
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($api_url, false, $context);

if ($response === FALSE) {
    echo json_encode([
        'error' => 'API request failed'
    ]);
    exit;
}

$result = json_decode($response, true);

error_log("API Response: " . json_encode($result));
// prediction: 1 = positive, 0 = negative
$prediction = strtolower($result['prediction']);
$sentiment = ($prediction == '1' || $prediction == 'positive') ? 'positive' : 'negative';
$confidence  = round((float)($result["confidence"] ?? 0), 2);

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

mysqli_query($conn, "INSERT INTO reviews (user_id, reviewer_name, review_text, sentiment, confidence, created_at)
                     VALUES ($uid_val, '$name_esc', '$review_escaped', '$sentiment_esc', $confidence, NOW())");

echo json_encode([
    'success'    => true,
    'review'     => $review,
    'sentiment'  => $sentiment,
    'confidence' => round($confidence, 2) . '%',
    'reviewer'   => $user_name,
]);
?>
