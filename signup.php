<?php
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

    $email = $_POST['unsign'];
    $password = password_hash($_POST['pwsign'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (email, password)
            VALUES ('$email', '$password')";

    if ($conn->query($sql)) {
        echo "Signup successful!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>