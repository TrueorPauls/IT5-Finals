<?php
session_start();
include("config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_sql = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
    mysqli_query($conn, $update_sql);
}

$query = "SELECT 
            o.*, 
            GROUP_CONCAT(oi.item_name SEPARATOR ', ') AS item_list,
            SUM(oi.quantity) AS total_qty
          FROM orders o
          LEFT JOIN order_items oi ON o.id = oi.order_id
          GROUP BY o.id
          ORDER BY o.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabesera Cafe Admin</title>
    <link rel="stylesheet" href="orders style.css">
</head>
<body>
    <section class="container">
        <nav class="navbar">
            <div class="logo">
                <a href="admin_dashboard.php"><img src="qt=q_95.webp" alt="Kabesera Cafe"></a>
                <a id="kabhome" href="admin_dashboard.php">Kabesera Cafe</a>
            </div>
            <ul>
                <li><a href="orders.php">Order Management</a></li>
                <li><a href="reportrepository.php">Insights</a></li>
                <li><a href="booking.html">Event Reservations</a></li>
                <li><a href="sandbox.php">Sandbox Settings</a></li>
                <li><a href="logout.php" class="btn">Logout</a></li>
            </ul>
        </nav>

        <h1 id="ordertitle">Order Management</h1>

        <div class="ordertaker">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>PO ID</th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Items</th>
                        <th>Qty</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['order_number']) ?></td>
                        <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['item_list']) ?></td>
                        <td><?= $row['total_qty'] ?></td>
                        <td>₱<?= number_format($row['total_amount'], 2) ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                <select name="status" class="status-dropdown">
                                    <?php 
                                    $statuses = ['Pending', 'Preparing', 'Shipped', 'Completed'];
                                    foreach ($statuses as $st): ?>
                                        <option value="<?= $st ?>" <?= ($row['status'] == $st) ? 'selected' : '' ?>>
                                            <?= $st ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                        </td>
                        <td>
                                <button type="submit" name="update_status" class="save-btn">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>