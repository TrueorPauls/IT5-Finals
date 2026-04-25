<?php
session_start();
include("config.php");

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: log-in.php");
    exit;
}

// Handle status update
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

// Fetch all orders with their items
$orders_res = mysqli_query($conn, "SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
$orders = [];
while ($row = mysqli_fetch_assoc($orders_res)) {
    $oid = $row['id'];
    $items_res = mysqli_query($conn, "SELECT item_name, quantity FROM order_items WHERE order_id = $oid");
    $items = [];
    $total_qty = 0;
    while ($item = mysqli_fetch_assoc($items_res)) {
        $items[]    = $item['item_name'];
        $total_qty += $item['quantity'];
    }
    $row['items_list'] = implode(', ', $items);
    $row['total_qty']  = $total_qty;
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management – Kabesera Cafe</title>
    <link rel="stylesheet" href="admin dashboard style.css">
</head>
<body>

<nav>
    <div class="logo">
        <a href="admin_dashboard.php"><img src="qt=q_95.webp" alt="Kabesera Cafe"></a>
        <a id="kabhome" href="admin_dashboard.php" style="color:#f0ebe3;">Kabesera Cafe</a>
    </div>
    <ul>
        <li><a href="report repository.html">Insights</a></li>
        <li><a href="booking.html">Event Reservations</a></li>
        <li><a href="logout.php" class="btn">Log Out</a></li>
    </ul>
</nav>

<div class="page-content">
    <h1>Order Management</h1>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>PO ID</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Items</th>
                    <th>Qty</th>
                    <th>Total Price</th>
                    <th colspan="2">Status / Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr class="empty-row">
                    <td colspan="9">No orders yet. Orders will appear here once customers place them.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td class="po-id"><?= htmlspecialchars($order['order_number']) ?></td>
                    <td><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                    <td><?= htmlspecialchars($order['full_name']) ?></td>
                    <td><?= htmlspecialchars($order['address']) ?></td>
                    <td><?= htmlspecialchars($order['items_list']) ?></td>
                    <td><?= $order['total_qty'] ?></td>
                    <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                    <td colspan="2">
                        <form method="POST" style="display:flex; gap:8px; align-items:center;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" class="status-select">
                                <?php foreach (['Pending','Preparing','Shipped','Completed','Cancelled'] as $s): ?>
                                <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>>
                                    <?= $s ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="update-btn">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
