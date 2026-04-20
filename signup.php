<?php
include 'config.php';

$secretKey = "6LdAP8AsAAAAAL-1IIVrVehmyTUQLhx4E_K3tAes";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $captcha = $_POST['g-recaptcha-response'];

    // Verify captcha
    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha"
    );

    $response = json_decode($verify);

    if (!$response->success) {
        die("reCAPTCHA verification failed!");
    }

    // Continue signup
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (fullname, email, password)
            VALUES ('$fullname', '$email', '$password')";

    if ($conn->query($sql)) {
        echo "Signup successful!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>