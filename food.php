<?php
session_start();
include("config.php");

$logged_in = isset($_SESSION['user']);

// Add to cart
if ($logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_item_id'])) {
    $email = $_SESSION['user'];
    $uid_res = mysqli_query($conn, "SELECT id FROM users WHERE email='" . mysqli_real_escape_string($conn, $email) . "'");
    $uid_row = mysqli_fetch_assoc($uid_res);
    $user_id = $uid_row['id'];
    $item_id = (int)$_POST['menu_item_id'];

    $check = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id=$user_id AND menu_item_id=$item_id");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $nq = $row['quantity'] + 1;
        mysqli_query($conn, "UPDATE cart SET quantity=$nq WHERE id={$row['id']}");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, menu_item_id, quantity) VALUES ($user_id, $item_id, 1)");
    }
    $added = true;
}

// Cart count
$cart_count = 0;
if ($logged_in) {
    $email = $_SESSION['user'];
    $uid_res = mysqli_query($conn, "SELECT id FROM users WHERE email='" . mysqli_real_escape_string($conn, $email) . "'");
    $uid_row = mysqli_fetch_assoc($uid_res);
    if ($uid_row) {
        $user_id = $uid_row['id'];
        $cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id=$user_id");
        $cc_row = mysqli_fetch_assoc($cc);
        $cart_count = $cc_row['total'] ?? 0;
    }
}

// FIX: Load sandbox settings from DB
$sandbox = array();
$sb_res = mysqli_query($conn, "SELECT setting_key, setting_value FROM sandbox_settings");
if ($sb_res) {
    while ($sb_row = mysqli_fetch_assoc($sb_res)) {
        $sandbox[$sb_row['setting_key']] = $sb_row['setting_value'];
    }
}

// FIX: Read theme and apply colors
$theme = (isset($sandbox['theme']) && $sandbox['theme'] === 'dark') ? 'dark' : 'light';
$body_bg     = ($theme === 'dark') ? '#1a1210' : '#d8e2dc';
$body_color  = ($theme === 'dark') ? '#f0ebe3' : '#000000';
$items_bg    = ($theme === 'dark') ? '#1a1210' : '#d8e2dc';
$card_bg     = ($theme === 'dark') ? '#2a1f1c' : '#f2f4f1';
$price_color = ($theme === 'dark') ? '#4ade80' : '#14532d';
$title_color = ($theme === 'dark') ? '#4ade80' : '#14532d';
$text_muted  = ($theme === 'dark') ? '#9a8880' : '#666666';

// FIX: Read names and prices from sandbox, fall back to defaults
$f10_name  = isset($sandbox['f10_name'])  ? $sandbox['f10_name']  : 'The Sharing Spread';
$f10_price = isset($sandbox['f10_price']) ? $sandbox['f10_price'] : '450';
$f11_name  = isset($sandbox['f11_name'])  ? $sandbox['f11_name']  : 'House Blends';
$f11_price = isset($sandbox['f11_price']) ? $sandbox['f11_price'] : '165';
$f12_name  = isset($sandbox['f12_name'])  ? $sandbox['f12_name']  : 'Classic Tapa Silog';
$f12_price = isset($sandbox['f12_price']) ? $sandbox['f12_price'] : '320';
$f13_name  = isset($sandbox['f13_name'])  ? $sandbox['f13_name']  : 'Truffle Adobo Pasta';
$f13_price = isset($sandbox['f13_price']) ? $sandbox['f13_price'] : '380';
$f14_name  = isset($sandbox['f14_name'])  ? $sandbox['f14_name']  : 'Lechon Kawali';
$f14_price = isset($sandbox['f14_price']) ? $sandbox['f14_price'] : '425';
$f15_name  = isset($sandbox['f15_name'])  ? $sandbox['f15_name']  : 'Artisanal Fruit Donuts';
$f15_price = isset($sandbox['f15_price']) ? $sandbox['f15_price'] : '95';
$f16_name  = isset($sandbox['f16_name'])  ? $sandbox['f16_name']  : 'Botanical Cakes';
$f16_price = isset($sandbox['f16_price']) ? $sandbox['f16_price'] : 'Custom';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Page</title>
    <link rel="stylesheet" href="food style.css">
    <!-- FIX: Apply sandbox theme to customer food page -->
    <style>
        body { background-color: <?php echo $body_bg; ?>; color: <?php echo $body_color; ?>; }
        .menu-items { background-color: <?php echo $items_bg; ?>; }
        .food-feature { background-color: <?php echo $card_bg; ?>; }
        .feature-info h4 { color: <?php echo $title_color; ?>; }
        .price { color: <?php echo $price_color; ?>; }
        .item-desc { color: <?php echo $price_color; ?>; }
        #foodprice { color: <?php echo $price_color; ?>; }
        #foodtitle { color: <?php echo $body_color; ?>; }
        .group-title, .sticky-header {
            background-color: <?php echo $items_bg; ?>;
            color: <?php echo $body_color; ?>;
        }
        #desc { color: <?php echo $text_muted; ?>; }
    </style>
</head>
<body>

<?php if (isset($added)): ?>
<div class="flash-added">&#10003; Added to cart!</div>
<?php endif; ?>

<nav class="main-nav">
    <div class="logo">
        <a href="index.php"><img src="qt=q_95.webp" alt="Kabesera Cafe"></a>
        <a id="kabhome" href="index.php">Kabesera Cafe</a>
    </div>
    <ul class="nav-links">
        <li id="foodbtn"><a href="food.php" style="color:#22c55e">Food</a></li>
        <li id="coffeebtn"><a href="coffee.php">Coffee</a></li>
        <li id="eventsbtn"><a href="events.php">Events</a></li>
        <?php if ($logged_in): ?>
        <li><a href="cart.php">Cart<?php if ($cart_count > 0): ?><span class="cart-badge"><?= $cart_count ?></span><?php endif; ?></a></li>
        <li><a href="order_history.php">My Orders</a></li>
        <li><a href="reviews.php"">Reviews</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
        <?php else: ?>
        <li><a href="login.php" class="btn">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<section class="menu-page">
    <div class="menu-sidebar">
        <div class="sidebar-content">
            <h2>The Menu</h2>
            <ul class="category-links">
                <li><a href="#breakfast" class="menu-link">Breakfast</a></li>
                <li><a href="#lunch" class="menu-link">Lunch & Dinner</a></li>
                <li><a href="#pastries" class="menu-link">Bakery & Sweets</a></li>
            </ul>
        </div>
    </div>

    <div class="menu-items">

        <!-- BREAKFAST -->
        <section id="breakfast" class="menu-section">
            <h3 class="sticky-header">Breakfast & Snacks</h3>

            <div class="food-feature">
                <img src="kabesera-cafe-s-fresh (3) - Copy - Copy.jpg" alt="Nachos and Fries">
                <div class="feature-info">
                    <h4><?= htmlspecialchars($f10_name) ?></h4>
                    <p id="desc">Perfect for groups. Our signature Nachos and Loaded Fries, made fresh for every table.</p>
                    <span class="price">&#8369;<?= htmlspecialchars($f10_price) ?></span>
                    <?php if ($logged_in): ?>
                    <form method="POST"><input type="hidden" name="menu_item_id" value="10"><button type="submit" class="add-btn">+ Add to Cart</button></form>
                    <?php else: ?><a href="login.php" class="login-hint">Log in to order</a><?php endif; ?>
                </div>
            </div>

            <div class="food-grid">
                <div class="food-item">
                    <img src="kabesera-cafe-house-blends.jpg" alt="Coffee">
                    <div class="item-desc">
                        <h5 id="foodtitle"><?= htmlspecialchars($f11_name) ?></h5>
                        <span id="foodprice">&#8369;<?= htmlspecialchars($f11_price) ?></span>
                        <?php if ($logged_in): ?>
                        <form method="POST"><input type="hidden" name="menu_item_id" value="11"><button type="submit" class="add-btn">+ Add to Cart</button></form>
                        <?php else: ?><a href="login.php" class="login-hint">Log in to order</a><?php endif; ?>
                    </div>
                </div>
                <div class="food-item">
                    <img src="kabesera-cafe-s-fresh (4).jpg" alt="Classic Silog">
                    <div class="item-desc">
                        <h5 id="foodtitle"><?= htmlspecialchars($f12_name) ?></h5>
                        <span id="foodprice">&#8369;<?= htmlspecialchars($f12_price) ?></span>
                        <?php if ($logged_in): ?>
                        <form method="POST"><input type="hidden" name="menu_item_id" value="12"><button type="submit" class="add-btn">+ Add to Cart</button></form>
                        <?php else: ?><a href="login.php" class="login-hint">Log in to order</a><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- LUNCH & DINNER -->
        <section id="lunch" class="menu-section">
            <h3 class="sticky-header">Lunch & Dinner</h3>

            <div class="food-feature reverse">
                <img src="kabesera-cafe-s-fresh.jpg" alt="Flatlay Menu">
                <div class="feature-info">
                    <h4>Signature Heritage Plates</h4>
                    <p id="desc">Traditional Filipino favorites reimagined. From our Crispy Binagoongan to our Kare-Kare Confit.</p>
                </div>
            </div>

            <div class="food-grid">
                <div class="food-item">
                    <img src="kabesera-cafe-s-fresh (1).jpg" alt="Adobo Pasta">
                    <div class="item-desc">
                        <h5 id="foodtitle"><?= htmlspecialchars($f13_name) ?></h5>
                        <span id="foodprice">&#8369;<?= htmlspecialchars($f13_price) ?></span>
                        <?php if ($logged_in): ?>
                        <form method="POST"><input type="hidden" name="menu_item_id" value="13"><button type="submit" class="add-btn">+ Add to Cart</button></form>
                        <?php else: ?><a href="login.php" class="login-hint">Log in to order</a><?php endif; ?>
                    </div>
                </div>
                <div class="food-item">
                    <img src="kabesera-cafe-s-local.jpg" alt="Crispy Pork">
                    <div class="item-desc">
                        <h5 id="foodtitle"><?= htmlspecialchars($f14_name) ?></h5>
                        <span id="foodprice">&#8369;<?= htmlspecialchars($f14_price) ?></span>
                        <?php if ($logged_in): ?>
                        <form method="POST"><input type="hidden" name="menu_item_id" value="14"><button type="submit" class="add-btn">+ Add to Cart</button></form>
                        <?php else: ?><a href="login.php" class="login-hint">Log in to order</a><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- BAKERY -->
        <section id="pastries" class="menu-section">
            <h3 class="sticky-header">Bakery & Sweets</h3>

            <div class="food-feature">
                <img src="kabesera-cafe-s-fresh (2) - Copy - Copy.jpg" alt="Donuts">
                <div class="feature-info">
                    <h4><?= htmlspecialchars($f15_name) ?></h4>
                    <p id="desc">Soft, brioche-style donuts topped with fresh strawberries, kiwi, and house-made glaze.</p>
                    <span class="price">&#8369;<?= htmlspecialchars($f15_price) ?> /pc</span>
                    <?php if ($logged_in): ?>
                    <form method="POST"><input type="hidden" name="menu_item_id" value="15"><button type="submit" class="add-btn">+ Add to Cart</button></form>
                    <?php else: ?><a href="login.php" class="login-hint">Log in to order</a><?php endif; ?>
                </div>
            </div>

            <div class="food-grid">
                <div class="food-item">
                    <img src="celebrate-kabesera-cafe.jpg" alt="Celebration Cake">
                    <div class="item-desc">
                        <h5 id="foodtitle"><?= htmlspecialchars($f16_name) ?></h5>
                        <span id="foodprice"><?= htmlspecialchars($f16_price) ?></span>
                        <span style="font-size:0.8rem;color:<?php echo $text_muted; ?>;">Contact us to order</span>
                    </div>
                </div>
            </div>
        </section>

    </div>
</section>

<footer id="foodreviews">
    <p>FOR CUSTOMER REVIEW/SENTIMENT ANALYSIS</p>
</footer>

</body>
</html>
