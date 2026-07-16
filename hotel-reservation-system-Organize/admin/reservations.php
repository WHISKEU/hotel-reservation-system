<?php

include '../includes/admin_auth.php';
include '../includes/connection.php';

// CONFIRM RESERVATION

if (isset($_GET['confirm'])) {

    $reservation_id = (int) $_GET['confirm'];

    $stmt = mysqli_prepare(
        $conn,
        "
        UPDATE reservations
        SET reservation_status = 'Confirmed'
        WHERE reservation_id = ?
        "
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $reservation_id
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: reservations.php");
        exit();

    }

    mysqli_stmt_close($stmt);

}

// COMPLETE RESERVATION

if (isset($_GET['complete'])) {

    $reservation_id = (int) $_GET['complete'];

    $stmt = mysqli_prepare(
        $conn,
        "
        UPDATE reservations
        SET reservation_status = 'Completed'
        WHERE reservation_id = ?
        "
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $reservation_id
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: reservations.php");
        exit();

    }

    mysqli_stmt_close($stmt);

}

// Fetch all reservations with user and room details
$reservations = mysqli_query(
    $conn,
    "
    SELECT

        reservations.reservation_id,
        reservations.check_in,
        reservations.check_out,
        reservations.total_price,
        reservations.reservation_status,

        users.fname,
        users.lname,

        rooms.room_number,

        room_types.type_name

    FROM reservations

    INNER JOIN users
        ON reservations.user_id = users.user_id

    INNER JOIN rooms
        ON reservations.room_id = rooms.room_id

    INNER JOIN room_types
        ON rooms.room_type_id = room_types.room_type_id

    ORDER BY reservations.created_at DESC
    "
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations - Hotel Reservation System</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

<div class="wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="main-content">
        
        <div class="dashboard-header">
            <h1>Reservations</h1>
            <p>Monitor customer bookings, update check-in/check-out statuses, and manage hotel operational flow.</p>
        </div>

        <div class="card" style="background: white; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); overflow: hidden; margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background-color: #e8f0fe; border-bottom: 2px solid #ddd;">
                        <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem;">Customer Name</th>
                        <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">Room Number</th>
                        <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem;">Room Type</th>
                        <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">Check In</th>
                        <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">Check Out</th>
                        <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem;">Total Price</th>
                        <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">Status</th>
                        <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($reservations) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($reservations)): ?>
                        <tr style="border-bottom: 1px solid #eee; transition: background 0.2s; background: white;">
                            <td style="padding: 15px; font-weight: 600; color: #333; font-size: 0.9rem;">
                                <?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?>
                            </td>
                            <td style="padding: 15px; text-align: center; color: #555; font-size: 0.9rem; font-weight: bold;">
                                <?php echo htmlspecialchars($row['room_number']); ?>
                            </td>
                            <td style="padding: 15px; color: #666; font-size: 0.9rem;">
                                <?php echo htmlspecialchars($row['type_name']); ?>
                            </td>
                            <td style="padding: 15px; text-align: center; color: #555; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($row['check_in']); ?>
                            </td>
                            <td style="padding: 15px; text-align: center; color: #555; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($row['check_out']); ?>
                            </td>
                            <td style="padding: 15px; color: #333; font-weight: 600; font-size: 0.9rem;">
                                ₱<?php echo number_format($row['total_price'],2); ?>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <?php 
                                    $statusClass = "color: #d69e2e; background: #fefcbf; border: 1px solid #faf089;"; // Pending
                                    if ($row['reservation_status'] == 'Confirmed') {
                                        $statusClass = "color: #2b6cb0; background: #ebf8ff; border: 1px solid #bee3f8;";
                                    } elseif ($row['reservation_status'] == 'Completed') {
                                        $statusClass = "color: #2f855a; background: #f0fff4; border: 1px solid #c6f6d5;";
                                    }
                                ?>
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($row['reservation_status']); ?>
                                </span>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <?php if ($row['reservation_status'] == "Pending"): ?>
                                    <a href="reservations.php?confirm=<?php echo $row['reservation_id']; ?>" style="color: #2b6cb0; text-decoration: none; border: 1px solid #2b6cb0; padding: 5px 12px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; transition: 0.2s;">
                                        Confirm
                                    </a>
                                <?php elseif ($row['reservation_status'] == "Confirmed"): ?>
                                    <a href="reservations.php?complete=<?php echo $row['reservation_id']; ?>" style="color: #2f855a; text-decoration: none; border: 1px solid #2f855a; padding: 5px 12px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; transition: 0.2s;">
                                        Complete
                                    </a>
                                <?php else: ?>
                                    <span style="color: #ccc; font-size: 0.9rem;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px; color: #777;">No reservations found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

</body>
</html>