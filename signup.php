<?php
session_start();
include("config.php");

$secretKey = "6LdAP8AsAAAAAL-1IIVrVehmyTUQLhx4E_K3tAes";
$error     = "";
$success   = false;
$fullname  = "";
$email     = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['Submit'])) {

    $email    = mysqli_real_escape_string($conn, trim($_POST['unsign']));
    $password = $_POST['pwsign'];
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname'] ?? ''));

    if (empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($secretKey !== "YOUR_SECRET_KEY_HERE") {
        $captcha = $_POST['g-recaptcha-response'] ?? '';
        if (empty($captcha)) {
            $error = "Please complete the reCAPTCHA.";
        } else {
            $verify   = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
            $response = json_decode($verify);
            if (!$response->success) {
                $error = "reCAPTCHA verification failed. Please try again.";
            }
        }
    }

    if (empty($error)) {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "An account with this email already exists.";
        } else {
            $hashed = $password;
            $insert = "INSERT INTO users (email, password, full_name, role)
                       VALUES ('$email', '$hashed', '$fullname', 'customer')";
            if (mysqli_query($conn, $insert)) {
                $success = true;
                $email    = "";
                $fullname = "";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up – Kabesera Cafe</title>
    <link rel="stylesheet" href="sign style.css">
    
</head>
<body>
    <img src="qt=q_95.webp" class="logoimg">
    <a href="index.php"><h1 id="kablogo">Kabesera Cafe</h1></a>

    <form method="post" action="signup.php" class="loginbox">
    <div class="login">
        <h1>Sign Up</h1>
        <p id="intro">Be a part of the family</p>

        <?php if ($success): ?>
        <div class="success-msg">
            ✓ Account created! <a href="login.php">Log in now →</a>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <p class="un">Full Name:
            <input type="text" name="fullname" placeholder="Your full name"
                   value="<?= htmlspecialchars($fullname) ?>">
        </p>
        <p class="un">Email:
            <input type="text" name="unsign" placeholder="Email"
                   value="<?= htmlspecialchars($email) ?>">
        </p>
        <p id="pw">Password:
            <input type="password" name="pwsign" placeholder="Password (min. 6 chars)">
        </p>

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="g-recaptcha" data-sitekey="6LdAP8AsAAAAAMY2L3-Qu5K1tVMnXUAg_Jwi5q_o"></div>

        <button id="submit" type="submit" name="Submit">Sign Up</button>
        <br>
        <p id="or">or</p>
        <a id="return" href="login.php">Already a user? LOGIN</a>
    </div>
    </form>
</body>
</html>