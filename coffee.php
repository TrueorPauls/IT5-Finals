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

// Load sandbox settings
$sandbox = array();
$sb_res = mysqli_query($conn, "SELECT setting_key, setting_value FROM sandbox_settings");
if ($sb_res) {
    while ($sb_row = mysqli_fetch_assoc($sb_res)) {
        $sandbox[$sb_row['setting_key']] = $sb_row['setting_value'];
    }
}

// Menu items from sandbox (names + prices)
$espresso_items = array(
    array('id'=>1, 'name'=> isset($sandbox['c1_name']) ? $sandbox['c1_name'] : 'Espresso',   'img'=>'espresso.jpg',   'price'=> isset($sandbox['c1_price']) ? $sandbox['c1_price'] : 130),
    array('id'=>2, 'name'=> isset($sandbox['c2_name']) ? $sandbox['c2_name'] : 'Americano',  'img'=>'americano.jpg',  'price'=> isset($sandbox['c2_price']) ? $sandbox['c2_price'] : 130),
    array('id'=>3, 'name'=> isset($sandbox['c3_name']) ? $sandbox['c3_name'] : 'Cappuccino', 'img'=>'cappuccino.jpg', 'price'=> isset($sandbox['c3_price']) ? $sandbox['c3_price'] : 155),
);
$latte_items = array(
    array('id'=>4, 'name'=> isset($sandbox['c4_name']) ? $sandbox['c4_name'] : 'Pandan Latte',         'img'=>'pandan.jpg',  'price'=> isset($sandbox['c4_price']) ? $sandbox['c4_price'] : 185, 'desc'=>'Espresso, pandan, coconut milk'),
    array('id'=>5, 'name'=> isset($sandbox['c5_name']) ? $sandbox['c5_name'] : 'Spanish Latte',        'img'=>'spanish.jpg', 'price'=> isset($sandbox['c5_price']) ? $sandbox['c5_price'] : 175, 'desc'=>'Sweetened condensed milk & espresso'),
    array('id'=>6, 'name'=> isset($sandbox['c6_name']) ? $sandbox['c6_name'] : 'Salted Caramel Latte', 'img'=>'caramel.jpg', 'price'=> isset($sandbox['c6_price']) ? $sandbox['c6_price'] : 180, 'desc'=>'House-made caramel with sea salt'),
);
$nc_items = array(
    array('id'=>7, 'name'=> isset($sandbox['c7_name']) ? $sandbox['c7_name'] : 'Premium Hot Cocoa', 'img'=>'cocoa.jpg',     'price'=> isset($sandbox['c7_price']) ? $sandbox['c7_price'] : 160),
    array('id'=>8, 'name'=> isset($sandbox['c8_name']) ? $sandbox['c8_name'] : 'Matcha Latte',      'img'=>'matcha.jpg',    'price'=> isset($sandbox['c8_price']) ? $sandbox['c8_price'] : 185),
    array('id'=>9, 'name'=> isset($sandbox['c9_name']) ? $sandbox['c9_name'] : 'Chocolate Milk',    'img'=>'chocolate.jpg', 'price'=> isset($sandbox['c9_price']) ? $sandbox['c9_price'] : 150),
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Page</title>
    <link rel="stylesheet" href="coffee style.css">
    <?php include("theme.php"); ?>
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
        <li><a href="food.php">Food</a></li>
        <li><a href="coffee.php" style="color:#22c55e;">Coffee</a></li>
        <li><a href="events.php">Events</a></li>
        <li><a href="index.php#contacts">Contact</a></li>
        <?php if ($logged_in): ?>
        <li><a href="cart.php">Cart<?php if ($cart_count > 0): ?><span class="cart-badge"><?= $cart_count ?></span><?php endif; ?></a></li>
        <li><a href="order_history.php">My Orders</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
        <?php else: ?>
        <li><a href="login.php" class="btn">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<section class="coffee-page">
    <div class="menu-sidebar">
        <div class="sidebar-content">
            <h2>Coffee & Brews</h2>
            <ul class="category-links">
                <li><a href="#espresso" class="menu-link">Espresso Bar</a></li>
                <li><a href="#signature" class="menu-link">Signature Lattes</a></li>
                <li><a href="#non-coffee" class="menu-link">Non-Coffee</a></li>
            </ul>
        </div>
    </div>

    <div class="menu-items">

        <!-- ESPRESSO BAR -->
        <section id="espresso" class="menu-section">
            <h3 class="section-title">Espresso Bar</h3>
            <div class="menu-list">
                <?php foreach ($espresso_items as $m): ?>
                <div class="menu-row">
                    <img src="<?= $m['img'] ?>" alt="<?= htmlspecialchars($m['name']) ?>">
                    <div class="menu-content">
                        <span class="item-name"><?= htmlspecialchars($m['name']) ?></span>
                    </div>
                    <div class="add-to-cart-btn">
                        <span class="price">&#8369;<?= htmlspecialchars($m['price']) ?></span>
                        <?php if ($logged_in): ?>
                        <form method="POST">
                            <input type="hidden" name="menu_item_id" value="<?= $m['id'] ?>">
                            <button type="submit" class="add-btn">+ Add to Cart</button>
                        </form>
                        <?php else: ?>
                        <a href="login.php" class="login-hint">Log in to order</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- SIGNATURE LATTES -->
        <section id="signature" class="menu-section">
            <h3 class="section-title">Signature Lattes</h3>
            <div class="menu-list">
                <?php foreach ($latte_items as $m): ?>
                <div class="menu-row">
                    <img src="<?= $m['img'] ?>" alt="<?= htmlspecialchars($m['name']) ?>">
                    <div class="item-info">
                        <span class="item-name"><?= htmlspecialchars($m['name']) ?></span>
                        <span class="item-detail"><?= $m['desc'] ?></span>
                    </div>
                    <div class="add-to-cart-btn">
                        <span class="price">&#8369;<?= htmlspecialchars($m['price']) ?></span>
                        <?php if ($logged_in): ?>
                        <form method="POST">
                            <input type="hidden" name="menu_item_id" value="<?= $m['id'] ?>">
                            <button type="submit" class="add-btn">+ Add to Cart</button>
                        </form>
                        <?php else: ?>
                        <a href="login.php" class="login-hint">Log in to order</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- NON-COFFEE -->
        <section id="non-coffee" class="menu-section">
            <h3 class="section-title">Non-Coffee</h3>
            <div class="menu-list">
                <?php foreach ($nc_items as $m): ?>
                <div class="menu-row">
                    <img src="<?= $m['img'] ?>" alt="<?= htmlspecialchars($m['name']) ?>">
                    <span class="item-name"><?= htmlspecialchars($m['name']) ?></span>
                    <div class="add-to-cart-btn">
                        <span class="price">&#8369;<?= htmlspecialchars($m['price']) ?></span>
                        <?php if ($logged_in): ?>
                        <form method="POST">
                            <input type="hidden" name="menu_item_id" value="<?= $m['id'] ?>">
                            <button type="submit" class="add-btn">+ Add to Cart</button>
                        </form>
                        <?php else: ?>
                        <a href="login.php" class="login-hint">Log in to order</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div>
</section>

<footer id="coffeereviews">
    <p>FOR CUSTOMER REVIEW/SENTIMENT ANALYSIS</p>
</footer>

</body>
</html>