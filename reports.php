<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $sql = "INSERT INTO reports (user_id, subject, message)
            VALUES ('$user_id', '$subject', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo "Report submitted!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>