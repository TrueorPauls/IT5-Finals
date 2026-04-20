<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $guests = $_POST['guests'];

    $sql = "INSERT INTO bookings (user_id, event_name, event_date, guests)
            VALUES ('$user_id', '$event_name', '$event_date', '$guests')";

    if ($conn->query($sql) === TRUE) {
        echo "Booking successful!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>