<?php
session_start();
include("connect.php");

if (isset($_POST['Submit'])) {
    $username = $_POST['un'];
    $password = $_POST['pw'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {

            $_SESSION['user'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == "admin") {
                header("Location: admin_dashboard.php");
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