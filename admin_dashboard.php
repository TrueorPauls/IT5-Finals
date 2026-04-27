<?php
session_start();
include("config.php");

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $status   = mysqli_real_escape_string($conn, $_POST['status']);
    $allowed  = ['Pending', 'Preparing', 'Shipped', 'Completed', 'Cancelled'];
    if (in_array($status, $allowed)) {
        mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id");
    }
    header("Location: admin_dashboard.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabesera Cafe Admin</title>
    <link rel="stylesheet" href="admin dashboard style.css">

</head>

<body>
    <section class="container">

    <nav class="navbar">
        <div class="logo"><a href="admin_dashboard.php"><img src="qt=q_95.webp" alt="Kabesera Cafe"></a>
            <a id="kabhome" href="admin_dashboard.php">Kabesera Cafe</a>
        </div>
        <ul>
            <li><a href="orders.php">Order Management</a></li>
            <li><a href="reportrepository.php">Insights</a></li>
            <li><a href="book_event.php">Event Reservations</a></li>
            <li><a href="sandbox.php">Sandbox Settings</a></li>
            <li><a href="logout.php" class="btn">Logout</a></li>
        </ul>
    </nav>

    <div>
        <img src="qt=q_95.webp" alt="Kabesera Cafe" id="taglineimg">
        <h1 id="tagline">Kabesera Cafe</h1>
        <p id="tagdesc">The heart of every gathering.</p>
    </div>
</body> 

</html>
