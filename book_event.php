<?php
include("config.php");

// FIRST: Process the update
if (isset($_POST['update_status'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_query = "UPDATE booking SET status = '$new_status' WHERE id = '$booking_id'";
    
    if (mysqli_query($conn, $update_query)) {
        // Use a header redirect to "refresh" the page properly and clear POST data
        header("Location: book_event.php?msg=updated");
        exit();
    }
}

// SECOND: Fetch the data (Now it will fetch the updated version)
$query = "SELECT * FROM booking ORDER BY date ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabesera Cafe Admin - Reservations</title>
    <link rel="stylesheet" href="booking.css">
</head>
<body>
    <section class="container">
        <nav class="navbar">
            <div class="logo">
                <a href="admin dashboard.html"><img src="qt=q_95.webp" alt="Kabesera Cafe"></a>
                <a id="kabhome" href="admin dashboard.html">Kabesera Cafe</a>
            </div>
            <ul>
                <li><a href="orders.php">Order Management</a></li>
                <li><a href="report_repository.php">Insights</a></li>
                <li><a href="book_event.php">Event Reservations</a></li>
                <li><a href="sandbox.php">Sandbox Settings</a></li>
                <li><a href="logout.php" class="btn">Logout</a></li>
            </ul>
        </nav>

        <h1 id="reporttitle">Event Reservations</h1>

        <div class="table-container">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Client Name</th>
                        <th>Event Type</th>
                        <th>Date / Slot</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td>#EV-<?= $row['id'] ?></td>
    <td>
        <strong><?= htmlspecialchars($row['client']) ?></strong><br>
        <small><?= htmlspecialchars($row['contact']) ?></small>
    </td>
    <td><?= htmlspecialchars($row['eventtype']) ?></td>
    <td>
        <strong><?= $row['date'] ?><br></strong>
        <strong><?= ucfirst($row['timeslot']) ?></strong>
    </td>
    <td><?= nl2br(htmlspecialchars($row['details'])) ?></td>
    
    <form method="POST" action="book_event.php">
        <td>
            <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
            <select name="status" class="status-select">
                <option value="pending" <?= ($row['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="confirmed" <?= ($row['status'] == 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                <option value="canceled" <?= ($row['status'] == 'canceled') ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </td>
        <td>
            <button type="submit" name="update_status" class="save-btn">Update</button>
        </td>
    </form>
</tr>
<?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>