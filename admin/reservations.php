<?php

include '../includes/admin_auth.php';
include '../includes/connection.php';
include '../includes/admin_sidebar.php';

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
<!-- HTML content for displaying reservations will go here -->
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Reservations</title>

</head>

<body>

<div class="content">

    <h1>Reservations</h1>

    <table border="1" cellpadding="10">

        <th>
            <tr>
                <th>Customer Name</th>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </th>

<?php while($row = mysqli_fetch_assoc($reservations)): ?>

<tr>

    <td>

        <?php
        echo htmlspecialchars(
            $row['fname'] . " " . $row['lname']
        );
        ?>
    </td>

    <td>
        <?php echo htmlspecialchars($row['room_number']); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($row['type_name']); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($row['check_in']); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($row['check_out']); ?>
    </td>

    <td>
        ₱<?php echo number_format($row['total_price'],2); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($row['reservation_status']); ?>
    </td>

    <td>
    <?php if ($row['reservation_status'] == "Pending"): ?>
        <a href="reservations.php?confirm=<?php echo $row['reservation_id']; ?>">
            Confirm
        </a>
    <?php elseif ($row['reservation_status'] == "Confirmed"): ?>
        <a href="reservations.php?complete=<?php echo $row['reservation_id']; ?>">
            Complete
        </a>
    <?php else: ?>
        -
    <?php endif; ?>
    </td>

</tr>

<?php endwhile; ?>

</table>
</div>
</body>
</html>