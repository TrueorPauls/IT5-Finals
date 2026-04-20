<?php
session_start();
include 'config.php';

$secretKey = "YOUR_SECRET_KEY";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // reCAPTCHA
    $captcha = $_POST['g-recaptcha-response'];
    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha"
    );
    $response = json_decode($verify);

    if (!$response->success) {
        die("reCAPTCHA failed!");
    }

    // MATCHED NAMES
    $email = $_POST['un'];
    $password = $_POST['pw'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            // OPTIONAL ROLE CHECK
            if ($_POST['role'] == "admin") {
                header("Location: admin_dashboard.html");
            } else {
                header("Location: index.html");
            }

        } else {
            echo "Wrong password!";
        }
    } else {
        echo "User not found!";
    }
}
?>