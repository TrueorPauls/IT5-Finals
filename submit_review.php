<?php
session_start();
include("config.php");

header('Content-Type: application/json');

// Must be logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'You must be logged in to leave a review.']);
    exit;
}

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

// ── Call Flask ML API ─────────────────────────────────────────────
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

$prediction = strtolower($result['prediction']);
$sentiment = ($prediction == '1' || $prediction == 'positive') ? 'positive' : 'negative';
$confidence  = round((float)($result["confidence"] ?? 0), 2);

error_log("Confidence extracted: " . $confidence);

// ── Get reviewer name from DB using email session ─────────────────
$email   = mysqli_real_escape_string($conn, $_SESSION['user']);
$u       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT full_name, email FROM users WHERE email='$email'"));
$user_name = ($u && !empty($u['full_name'])) ? $u['full_name'] : $email;
$user_id   = $_SESSION['user_id'] ?? 'NULL';

// ── Save to DB ────────────────────────────────────────────────────
$review_esc = mysqli_real_escape_string($conn, $review);
$sent_esc   = mysqli_real_escape_string($conn, $sentiment);
$name_esc   = mysqli_real_escape_string($conn, $user_name);

$query = "INSERT INTO reviews (user_id, reviewer_name, review_text, sentiment, confidence, created_at)
                     VALUES ($user_id, '$name_esc', '$review_esc', '$sent_esc', $confidence, NOW())";
error_log("Insert query: " . $query);
$insert_result = mysqli_query($conn, $query);
error_log("Insert result: " . ($insert_result ? "SUCCESS" : "FAILED - " . mysqli_error($conn)));

echo json_encode([
    'success'   => true,
    'review'    => $review,
    'sentiment' => $sentiment,
    'confidence' => round($confidence, 2) . '%',
    'reviewer'  => $user_name,
]);
?>
