<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("config.php");
 
$secretKey = "6LdAP8AsAAAAAL-1IIVrVehmyTUQLhx4E_K3tAes";
$error     = "";
$email     = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Submit'])) {
 
    $email         = mysqli_real_escape_string($conn, trim($_POST['un']));
    $password      = $_POST['pw'];
    $selected_role = $_POST['role'] ?? '';
 
    if (empty($selected_role)) {
        $error = "Please select whether you are a Customer or Admin.";
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
        $sql    = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
 
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($password === $row['password']) {
                if ($row['role'] !== $selected_role) {
                    $error = "Incorrect role selected for this account.";
                } else {
                    $_SESSION['user']    = $row['email'];
                    $_SESSION['role']    = $row['role'];
                    $_SESSION['user_id'] = $row['id'];
 
                    if ($row['role'] === 'admin') {
                        header("Location: admin_dashboard.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit;
                }
            } else {
                $error = "Incorrect password. Please try again.";
            }
        } else {
            $error = "No account found with that email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login style.css">
</head>
<body>
    <img src="qt=q_95.webp" class="logoimg">
    <a href="index.php"><h1 id="kablogo">Kabesera Cafe</h1></a>
<img src="kabesera-cafe-s-nature (6) (1).jpg" class="logimg">
    <form method="post" action="login.php" class="loginbox">
    <div class="login">
        <h1>Welcome Back!</h1>
        <p id="intro">Enter your Email and Password to proceed</p>
 
        <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
 
        <p id="un">Email:
            <input type="text" name="un" placeholder="Email"
                   value="<?= htmlspecialchars($email) ?>">
        </p>
        <p id="pw">Password:
            <input type="password" name="pw" placeholder="Password">
        </p>
 
        <p id="customer"><input type="radio" name="role" value="customer"
            <?= (($_POST['role'] ?? '') === 'customer') ? 'checked' : '' ?>> Customer</p>
        <p id="admin"><input type="radio" name="role" value="admin"
            <?= (($_POST['role'] ?? '') === 'admin') ? 'checked' : '' ?>> Admin</p>
 
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="g-recaptcha" data-sitekey="6LdAP8AsAAAAAMY2L3-Qu5K1tVMnXUAg_Jwi5q_o"></div> 
        <button id="submit" type="submit" name="Submit">Log-In</button>
        <br>
        <p id="or">or</p>
        <a id="sign" href="signup.php">Sign Up</a><br>
    </div>
    </form>
</body>
</html>