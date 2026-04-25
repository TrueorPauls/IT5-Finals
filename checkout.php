<?php
session_start();
include("config.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['user'];
$user_sql = "SELECT id, full_name, address FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'";
$user_res = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_res);
$user_id = $user['id'];

// Fetch cart
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

// Redirect if empty cart
if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

$error = "";
$success = false;
$order_number = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $address   = mysqli_real_escape_string($conn, trim($_POST['address']));
    $notes     = mysqli_real_escape_string($conn, trim($_POST['notes'] ?? ''));

    if (empty($full_name) || empty($address)) {
        $error = "Please fill in your name and delivery address.";
    } else {
        // Generate order number
        $order_number = 'KAB-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        // Insert order
        $insert_order = "INSERT INTO orders (user_id, order_number, full_name, address, notes, total_amount, status)
                         VALUES ($user_id, '$order_number', '$full_name', '$address', '$notes', $subtotal, 'Pending')";

        if (mysqli_query($conn, $insert_order)) {
            $order_id = mysqli_insert_id($conn);

            // Insert order items
            foreach ($cart_items as $item) {
                $item_name = mysqli_real_escape_string($conn, $item['name']);
                $price     = $item['price'];
                $qty       = $item['quantity'];
                $line      = $item['line_total'];
                $item_id   = $item['id'];
                mysqli_query($conn, "INSERT INTO order_items (order_id, menu_item_id, item_name, price, quantity, subtotal)
                                     VALUES ($order_id, $item_id, '$item_name', $price, $qty, $line)");
            }

            // Clear cart
            mysqli_query($conn, "DELETE FROM cart WHERE user_id=$user_id");

            $success = true;
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout – Kabesera Cafe</title>
    <link rel="stylesheet" href="checkout style.css">
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
        <li><a href="cart.php">🛒 Cart</a></li>
        <li><a href="order_history.php">My Orders</a></li>
        <li><a href="logout.php" class="btn">Log Out</a></li>
    </ul>
</nav>

<?php if ($success): ?>
<!-- SUCCESS STATE -->
<div style="margin-top:80px;padding:40px 60px;">
    <div class="success-card">
        <div class="success-icon">🎉</div>
        <h1>Order Placed!</h1>
        <p>Thank you! Your order has been received and is being prepared.</p>
        <div class="order-num"><?= htmlspecialchars($order_number) ?></div>
        <p style="color:#888;font-size:0.9rem;">Save this order number for your reference.</p>
        <div class="success-actions">
            <a href="order_history.php" class="btn-primary">View My Orders</a>
            <a href="coffee.php" class="btn-secondary">Order More</a>
            <a href="index.php" class="btn-secondary">Back to Home</a>
        </div>
    </div>
</div>

<?php else: ?>

<?php if ($error): ?>
<div class="flash-msg flash-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="page-header">
    <h1>Checkout</h1>
</div>

<div class="checkout-wrapper">

    <!-- FORM -->
    <div class="checkout-form-section">
        <div class="form-card">
            <h2>Delivery Details</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Your full name"
                           value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" placeholder="Street, Barangay, City, Province" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="notes">Order Notes <span style="color:#888;font-weight:normal">(optional)</span></label>
                    <textarea id="notes" name="notes" placeholder="Any special instructions? (e.g. no sugar, extra hot, etc.)"></textarea>
                </div>
                <div class="form-group" style="background:#fffbea;padding:14px;border-radius:8px;border:1px solid #ffe082;">
                    <strong>💳 Payment Method:</strong> Cash on Delivery / Pay at Counter
                </div>
                <button type="submit" class="place-order-btn">✓ Place Order — ₱<?= number_format($subtotal, 2) ?></button>
            </form>
        </div>
    </div>

    <!-- ORDER REVIEW -->
    <div class="order-review-section">
        <div class="review-box">
            <h2>Your Order</h2>
            <?php foreach ($cart_items as $item): ?>
            <div class="review-item">
                <div>
                    <div class="review-item-name"><?= htmlspecialchars($item['name']) ?></div>
                    <div class="review-item-qty">Qty: <?= $item['quantity'] ?></div>
                </div>
                <div class="review-item-price">₱<?= number_format($item['line_total'], 2) ?></div>
            </div>
            <?php endforeach; ?>
            <div class="review-total">
                <span>Total</span>
                <span>₱<?= number_format($subtotal, 2) ?></span>
            </div>
            <div style="margin-top:16px;padding:10px;background:#eaf4ef;border-radius:7px;font-size:0.85rem;color:#14532d;">
                📦 Estimated delivery: <strong>30–45 minutes</strong>
            </div>
        </div>
    </div>

</div>
<?php endif; ?>

</body>
</html>

