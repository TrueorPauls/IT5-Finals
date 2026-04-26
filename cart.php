<?php
session_start();
include("config.php");

// Must be logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['user'];
$user_sql = "SELECT id, full_name FROM users WHERE email = '$email'";
$user_res = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_res);
$user_id = $user['id'];

$message = "";
$msg_type = "";

// Handle Add to Cart (from menu pages)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'add') {
        $item_id = (int)$_POST['menu_item_id'];
        // Check if already in cart
        $check = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id=$user_id AND menu_item_id=$item_id");
        if (mysqli_num_rows($check) > 0) {
            $row = mysqli_fetch_assoc($check);
            $new_qty = $row['quantity'] + 1;
            mysqli_query($conn, "UPDATE cart SET quantity=$new_qty WHERE id={$row['id']}");
        } else {
            mysqli_query($conn, "INSERT INTO cart (user_id, menu_item_id, quantity) VALUES ($user_id, $item_id, 1)");
        }
        $message = "Item added to cart!";
        $msg_type = "success";
    }

    if ($_POST['action'] === 'update') {
        $cart_id = (int)$_POST['cart_id'];
        $qty = (int)$_POST['quantity'];
        if ($qty < 1) {
            mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id AND user_id=$user_id");
        } else {
            mysqli_query($conn, "UPDATE cart SET quantity=$qty WHERE id=$cart_id AND user_id=$user_id");
        }
    }

    if ($_POST['action'] === 'remove') {
        $cart_id = (int)$_POST['cart_id'];
        mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id AND user_id=$user_id");
        $message = "Item removed from cart.";
        $msg_type = "success";
    }

    if ($_POST['action'] === 'clear') {
        mysqli_query($conn, "DELETE FROM cart WHERE user_id=$user_id");
        $message = "Cart cleared.";
        $msg_type = "success";
    }
}

// Fetch cart items
$cart_sql = "SELECT cart.id as cart_id, cart.quantity, menu_items.* 
             FROM cart 
             JOIN menu_items ON cart.menu_item_id = menu_items.id 
             WHERE cart.user_id = $user_id";
$cart_result = mysqli_query($conn, $cart_sql);
$cart_items = [];
$subtotal = 0;
while ($row = mysqli_fetch_assoc($cart_result)) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $subtotal += $row['line_total'];
    $cart_items[] = $row;
}
$item_count = count($cart_items);
$total_qty = array_sum(array_column($cart_items, "quantity"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart </title>
    <link rel="stylesheet" href="cart style.css">
</head>
<body>

<nav>
    <div class="logo">
        <a href="index.php"><img src="qt=q_95.webp" alt="Kabesera Cafe"></a>
        <a id="kabhome" href="index.php">Kabesera Cafe</a>
    </div>
    <ul>
        <li><a href="food.php">Food</a></li>
        <li><a href="coffee.php">Coffee</a></li>
        <li><a href="events.php">Events</a></li>
        <li><a href="cart.php" style="color:#22c55e;font-weight:bold;">Cart (<?= $total_qty ?>)</a></li>
        <li><a href="order_history.php">My Orders</a></li>
        <li><a href="logout.php" class="btn">Log Out</a></li>
    </ul>
</nav>

<?php if ($message): ?>
<div class="flash-msg flash-<?= $msg_type ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="page-header">
    <h1>🛒 My Cart</h1>
    <p>Review your selections before checkout, <?= htmlspecialchars($user['full_name'] ?: $username) ?>.</p>
</div>

<div class="cart-wrapper">

    <?php if (empty($cart_items)): ?>
    <div class="cart-items-section">
        <div class="empty-cart">
            <div class="empty-icon">☕</div>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added anything yet. Browse our menu!</p>
            <a href="coffee.php" class="browse-btn">Browse Coffee</a>
            &nbsp;
            <a href="food.php" class="browse-btn" >Browse Food</a>
        </div>
    </div>
    <?php else: ?>

    <div class="cart-items-section">
        <?php foreach ($cart_items as $item): ?>
        <div class="cart-item">
            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" onerror="this.src='qt=q_95.webp'">
            <div class="cart-item-info">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <span class="category"><?= htmlspecialchars($item['category']) ?></span>
                <div class="unit-price">₱<?= number_format($item['price'], 2) ?> each</div>
                <div class="cart-item-controls">
                    <!-- Decrease -->
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                        <input type="hidden" name="quantity" value="<?= $item['quantity'] - 1 ?>">
                        <button type="submit" class="qty-btn">−</button>
                    </form>
                    <span class="qty-display"><?= $item['quantity'] ?></span>
                    <!-- Increase -->
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                        <input type="hidden" name="quantity" value="<?= $item['quantity'] + 1 ?>">
                        <button type="submit" class="qty-btn">+</button>
                    </form>
                </div>
            </div>
            <div class="cart-item-price">₱<?= number_format($item['line_total'], 2) ?></div>
            <!-- Remove -->
            <form method="POST">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                <button type="submit" class="remove-btn" title="Remove">✕</button>
            </form>
        </div>
        <?php endforeach; ?>

        <form method="POST" style="margin-top:10px;text-align:right">
            <input type="hidden" name="action" value="clear">
            <button type="submit" style="background:none;border:none;color:#c0392b;cursor:pointer;font-size:0.9rem;text-decoration:underline;">
                Clear entire cart
            </button>
        </form>
    </div>

    <!-- SUMMARY -->
    <div class="cart-summary-section">
        <div class="summary-box">
            <h2>Order Summary</h2>
            <?php foreach ($cart_items as $item): ?>
            <div class="summary-line">
                <span><?= htmlspecialchars($item['name']) ?> ×<?= $item['quantity'] ?></span>
                <span>₱<?= number_format($item['line_total'], 2) ?></span>
            </div>
            <?php endforeach; ?>
            <div class="summary-line total">
                <span>Total</span>
                <span>₱<?= number_format($subtotal, 2) ?></span>
            </div>
            <a href="checkout.php" class="checkout-btn">Proceed to Checkout →</a>
            <a href="coffee.php" class="continue-link">← Continue Shopping</a>
        </div>
    </div>

    <?php endif; ?>
</div>

</body>
</html>