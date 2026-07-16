<?php
// Include your existing admin authorization security check & database connection
include '../includes/admin_auth.php';
include '../includes/connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hotel Reservation</title>
    
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

<div class="wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="main-content">
        
        <div class="dashboard-header">
            <h1>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['fname']); ?>!
            </h1>
            <p>Hotel Reservation System Administrator Dashboard</p>
        </div>

        <div class="dashboard-cards">

            <div class="card">
                <div class="icon">🏨</div>
                <h2>Rooms</h2>
                <p>Manage hotel rooms and availability.</p>
                <a href="rooms.php">View Rooms</a>
            </div>

            <div class="card">
                <div class="icon">🛏️</div>
                <h2>Room Types</h2>
                <p>Create and update room categories.</p>
                <a href="room_types.php">View Room Types</a>
            </div>

            <div class="card">
                <div class="icon">📅</div>
                <h2>Reservations</h2>
                <p>Manage all guest reservations and payments.</p>
                <a href="reservations.php">View Reservations</a>
            </div>

        </div>

        <div class="welcome-box">
            <h2>Administrator Panel</h2>
            <p>
                Welcome to the Hotel Reservation System Admin Dashboard.
                Use the navigation menu on the left to manage hotel rooms,
                reservations, payments, room types, and reports.
            </p>
        </div>

    </main>
</div>

</body>
</html>