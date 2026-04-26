<?php
session_start();
include("config.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['user'];
$user_sql = "SELECT id, full_name FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'";
$user_res = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_res);
$user_id = $user['id'];

// Status filter
$filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : 'all';
$where = "WHERE o.user_id = $user_id";
if ($filter !== 'all') {
    $where .= " AND o.status = '$filter'";
}

$orders_sql = "SELECT * FROM orders o $where ORDER BY o.created_at DESC";
$orders_res = mysqli_query($conn, $orders_sql);
$orders = [];
while ($row = mysqli_fetch_assoc($orders_res)) {
    // Fetch items for each order
    $oid = $row['id'];
    $items_res = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $oid");
    $row['items'] = [];
    while ($item = mysqli_fetch_assoc($items_res)) {
        $row['items'][] = $item;
    }
    $orders[] = $row;
}

$statuses = ['all', 'Pending', 'Preparing', 'Shipped', 'Completed', 'Cancelled'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders – Kabesera Cafe</title>
    <link rel="stylesheet" href="order_history style.css">
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
        <li><a href="cart.php">Cart</a></li>
        <li><a href="order_history.php" style="color:#22c55e;">My Orders</a></li>
        <li><a href="logout.php" class="btn">Log Out</a></li>
    </ul>
</nav>

<div class="page-header">
    <h1>My Orders</h1>
    <p>Hello, <?= htmlspecialchars($user['full_name'] ?: $email) ?> — here is your order history.</p>
</div>

<div class="history-wrapper">

    <!-- FILTER TABS -->
    <div class="filter-bar">
        <?php foreach ($statuses as $s): ?>
        <a href="?status=<?= $s ?>"
           class="filter-btn <?= $filter === $s ? 'active' : '' ?>">
            <?= $s === 'all' ? 'All Orders' : $s ?>
        </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($orders)): ?>
    <div class="empty-orders">
        <div class="icon">📋</div>
        <h2>No orders yet</h2>
        <p><?= $filter === 'all' ? "You haven't placed any orders yet." : "No orders with status: $filter" ?></p>
        <a href="coffee.php">Browse Menu</a>
    </div>

    <?php else: ?>
    <?php foreach ($orders as $order): ?>

    <div class="order-card">

        <!-- HEADER -->
        <div class="order-card-header">
            <div>
                <span class="order-num"><?= htmlspecialchars($order['order_number']) ?></span>
                <div class="order-date">Placed on <?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></div>
            </div>
            <span class="status-badge status-<?= $order['status'] ?>"><?= $order['status'] ?></span>
        </div>

        <!-- META -->
        <div class="order-card-body">
            <div class="order-meta">
                <div class="order-meta-item">
                    <label>Name</label>
                    <span><?= htmlspecialchars($order['full_name']) ?></span>
                </div>
                <div class="order-meta-item">
                    <label>Delivery Address</label>
                    <span><?= htmlspecialchars($order['address']) ?></span>
                </div>
                <div class="order-meta-item">
                    <label>Items</label>
                    <span><?= count($order['items']) ?> item(s)</span>
                </div>
            </div>

            <!-- ITEMS TABLE -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th style="text-align:right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td>₱<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td style="text-align:right;font-weight:600">₱<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- NOTES -->
        <?php if (!empty($order['notes'])): ?>
        <div class="order-notes">
            📝 <strong>Notes:</strong> <?= htmlspecialchars($order['notes']) ?>
        </div>
        <?php endif; ?>

        <!-- TOTAL -->
        <div class="order-total-row">
            Total: ₱<?= number_format($order['total_amount'], 2) ?>
        </div>

    </div>
    <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>