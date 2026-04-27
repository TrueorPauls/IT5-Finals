<?php
include("config.php"); // Using your established connection

// Fetching reports - newest first is standard for an inbox/repository
$query = "SELECT * FROM reports ORDER BY email DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabesera Cafe Admin - Insights</title>
    <link rel="stylesheet" href="reportrepo.css">
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
                <li><a href="book_event.php">Event Reservations</a></li>
                <li><a href="sandbox.php">Sandbox Settings</a></li>
                <li><a href="logout.php" class="btn">Logout</a></li>
            </ul>
        </nav>

        <h1 id="reporttitle">Insights</h1>

        <div class="table-container">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Reporter Name</th>
                        <th>Email Address</th>
                        <th>Subject</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                    </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No reports found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>