<?php
session_start();
include("connect.php");

$logged_in = isset($_SESSION['user']);
$cart_count = 0;

if ($logged_in) {
    $username = $_SESSION['user'];
    $uid_res = mysqli_query($conn, "SELECT id FROM users WHERE username='" . mysqli_real_escape_string($conn, $username) . "'");
    $uid_row = mysqli_fetch_assoc($uid_res);
    if ($uid_row) {
        $user_id = $uid_row['id'];
        $cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id=$user_id");
        $cc_row = mysqli_fetch_assoc($cc);
        $cart_count = (int)($cc_row['total'] ?? 0);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabesera Cafe</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-badge {
            background: #22c55e;
            color: #fff;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 0.78rem;
            font-weight: bold;
            margin-left: 4px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <section class="container">

    <nav class="navbar">
        <div class="logo">
            <a href="index.php"><img src="qt=q_95.webp" alt="Kabesera Cafe"></a>
            <a id="kabhome" href="index.php">Kabesera Cafe</a>
        </div>
        <ul>
            <li><a href="food.php">Food</a></li>
            <li><a href="coffee.php">Coffee</a></li>
            <li><a href="events.html">Events</a></li>
            <li><a href="#contacts">Contact</a></li>
            <li><a href="#aboutus">About Us</a></li>
            <?php if ($logged_in): ?>
                <li>
                    <a href="cart.php">🛒 Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-badge"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="order_history.php">My Orders</a></li>
                <li><a href="logout.php" class="btn">Log Out</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn">Log In</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div>
        <img src="qt=q_95.webp" alt="Kabesera Cafe" id="taglineimg">
        <h1 id="tagline">Kabesera Cafe</h1>
        <p id="tagdesc">The heart of every gathering.</p>
    </div>
    <h1 id="aboutus">About Us</h1>
    <hr id="aboutline">
    <img id="storyimg" src="kabesera-cafe-s-nature (1).jpg">
    <img id="storyimg2" src="2024-11-19.jpg">
    <img id="storyimg3" src="kabesera-cafe-s-nature (2).jpg">
    <div id="story">
        <p>Nestled in a serene garden setting, our café offers a refined yet welcoming dining experience complemented by a scenic overlooking view. Guests may enjoy a thoughtfully curated selection of freshly brewed coffee, hearty rice meals with well-prepared viands, and a variety of desserts and light snacks. <br><br>Designed as both a place of relaxation and gathering, the space provides a calm atmosphere during the day and transforms into an ideal venue for intimate events and special occasions in the evening.
<br><br>With its harmonious blend of good food, comforting ambiance, and natural surroundings, the café invites guests to unwind, connect, and create meaningful moments.</p>
    </div>
        <div class="highlight-overlay">
            <div class="card card-coffee">
                <div class="card-content">
                    <h3>Special Brewed Coffee</h3>
                    <p>Single-origin roasted beans.</p>
                </div>
            </div>
            <div class="card card-pastry">
                <div class="card-content">
                    <h3>Culinary Classics</h3>
                    <p>From hand-stretched pasta to warm, signature rice plates.</p>
                </div>
            </div>
            <div class="card card-events">
                <div class="card-content">
                    <h3>Garden Events</h3>
                    <p>Live music and weekend workshops.</p>
                </div>
            </div>
    </div>
    </section>
    <footer id="contacts">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6724.533922972201!2d121.16985820326182!3d14.52881320729157!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c1beec0729eb%3A0xf56e6a82c695b39!2sKabesera%20Caf%C3%A9!5e0!3m2!1sen!2sph!4v1775355779679!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        <h1 id="address">Address</h1>
        <p id="addp">Block 8 Lot 2, Eastridge Village, Angono, 1930 Rizal</p>
        <h1 id="contactno">Contact Us</h1>
        <img src="phone logo.png" id="phonelogo">
        <p id="contactp">09667625352</p>
        <h1 id="socmed">Social Media</h1>
        <a href="https://www.facebook.com/kabesera"><img src="facebook logo.png" id="fblogo"></a>
        <a href="https://www.instagram.com/kabeseracafe/"><img src="instagram logo.png" id="iglogo"></a>
    </footer>
</body> 
</html>
