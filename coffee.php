<?php
session_start();
include("connect.php");

$logged_in = isset($_SESSION['user']);

// Add to cart from this page
if ($logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_item_id'])) {
    $username = $_SESSION['user'];
    $uid_res = mysqli_query($conn, "SELECT id FROM users WHERE username='" . mysqli_real_escape_string($conn, $username) . "'");
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

// Get cart count
$cart_count = 0;
if ($logged_in) {
    $username = $_SESSION['user'];
    $uid_res = mysqli_query($conn, "SELECT id FROM users WHERE username='" . mysqli_real_escape_string($conn, $username) . "'");
    $uid_row = mysqli_fetch_assoc($uid_res);
    if ($uid_row) {
        $user_id = $uid_row['id'];
        $cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id=$user_id");
        $cc_row = mysqli_fetch_assoc($cc);
        $cart_count = $cc_row['total'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee – Kabesera Cafe</title>
    <link rel="stylesheet" href="coffee style.css">
</head>
<body>

<?php if (isset($added)): ?>
<div class="flash-added">✓ Added to cart!</div>
<?php endif; ?>

<nav class="main-nav">
    <div class="logo">
        <a href="index.php"><img src="qt=q_95.webp" alt="Kabesera Cafe"></a>
        <a id="kabhome" href="index.php">Kabesera Cafe</a>
    </div>
    <ul class="nav-links">
        <li id="foodbtn"><a href="food.php">Food</a></li>
        <li id="coffeebtn"><a href="coffee.php" style="color:#22c55e">Coffee</a></li>
        <li id="eventsbtn"><a href="events.html">Events</a></li>
        <?php if ($logged_in): ?>
        <li><a href="cart.php">🛒 Cart<?php if ($cart_count > 0): ?><span class="cart-badge"><?= $cart_count ?></span><?php endif; ?></a></li>
        <li><a href="order_history.php">My Orders</a></li>
        <li><a href="logout.php" class="btn">Log Out</a></li>
        <?php else: ?>
        <li><a href="login.php" class="btn">Log In</a></li>
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
                <?php
                $espresso_items = [
                    ['id'=>1, 'name'=>'Espresso',   'img'=>'espresso.jpg',   'price'=>130],
                    ['id'=>2, 'name'=>'Americano',  'img'=>'americano.jpg',  'price'=>130],
                    ['id'=>3, 'name'=>'Cappuccino', 'img'=>'cappuccino.jpg', 'price'=>155],
                ];
                foreach ($espresso_items as $m): ?>
                <div class="menu-row">
                    <img src="<?= $m['img'] ?>" alt="<?= $m['name'] ?>">
                    <div class="menu-content">
                        <span class="item-name"><?= $m['name'] ?></span>
                    </div>
                    <div class="add-to-cart-btn">
                        <span class="price">₱<?= $m['price'] ?></span>
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
                <?php
                $latte_items = [
                    ['id'=>4, 'name'=>'Pandan Latte',        'img'=>'pandan.jpg',  'price'=>185, 'desc'=>'Espresso, pandan, coconut milk'],
                    ['id'=>5, 'name'=>'Spanish Latte',       'img'=>'spanish.jpg', 'price'=>175, 'desc'=>'Sweetened condensed milk & espresso'],
                    ['id'=>6, 'name'=>'Salted Caramel Latte','img'=>'caramel.jpg', 'price'=>180, 'desc'=>'House-made caramel with sea salt'],
                ];
                foreach ($latte_items as $m): ?>
                <div class="menu-row">
                    <img src="<?= $m['img'] ?>" alt="<?= $m['name'] ?>">
                    <div class="item-info">
                        <span class="item-name"><?= $m['name'] ?></span>
                        <span class="item-detail"><?= $m['desc'] ?></span>
                    </div>
                    <div class="add-to-cart-btn">
                        <span class="price">₱<?= $m['price'] ?></span>
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
                <?php
                $nc_items = [
                    ['id'=>7, 'name'=>'Premium Hot Cocoa', 'img'=>'cocoa.jpg',      'price'=>160],
                    ['id'=>8, 'name'=>'Matcha Latte',      'img'=>'matcha.jpg',     'price'=>185],
                    ['id'=>9, 'name'=>'Chocolate Milk',    'img'=>'chocolate.jpg',  'price'=>150],
                ];
                foreach ($nc_items as $m): ?>
                <div class="menu-row">
                    <img src="<?= $m['img'] ?>" alt="<?= $m['name'] ?>">
                    <span class="item-name"><?= $m['name'] ?></span>
                    <div class="add-to-cart-btn">
                        <span class="price">₱<?= $m['price'] ?></span>
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
