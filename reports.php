<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $subject = $_POST['issuecateg'];
    $message = $_POST['desc'];

    $sql = "INSERT INTO reports (subject, message)
            VALUES ('$subject', '$message')";

    if ($conn->query($sql)) {
        echo "Report submitted!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>