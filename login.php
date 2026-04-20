<?php
session_start();
include 'config.php';

$secretKey = "6LdAP8AsAAAAAL-1IIVrVehmyTUQLhx4E_K3tAes";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $captcha = $_POST['g-recaptcha-response'];

    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha"
    );

    $response = json_decode($verify);

    if (!$response->success) {
        die("reCAPTCHA verification failed!");
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            echo "Login successful!";
        } else {
            echo "Wrong password!";
        }
    } else {
        echo "User not found!";
    }
}
?>