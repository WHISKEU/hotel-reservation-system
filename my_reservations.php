<?php
include '../includes/connection.php';
include '../includes/customer_auth.php';

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT
        reservations.reservation_id,
        reservations.check_in,
        reservations.check_out,
        reservations.total_price,
        reservations.reservation_status,
        rooms.room_number,
        room_types.type_name

    FROM reservations

    INNER JOIN rooms
        ON reservations.room_id = rooms.room_id

    INNER JOIN room_types
        ON rooms.room_type_id = room_types.room_type_id

    WHERE reservations.user_id = '$user_id'

    ORDER BY reservations.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>

    <link rel="stylesheet" href="../assets/css/customer_index.css">
</head>

<body>

<div class="container">

    <h1>My Reservations</h1>

    <table border="1" cellpadding="10">

        <thead>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php while ($row = mysqli_fetch_assoc($query)) { ?>

            <tr>

                <td><?php echo htmlspecialchars($row['room_number']); ?></td>

                <td><?php echo htmlspecialchars($row['type_name']); ?></td>

                <td><?php echo htmlspecialchars($row['check_in']); ?></td>

                <td><?php echo htmlspecialchars($row['check_out']); ?></td>

                <td>
                    ₱<?php echo number_format($row['total_price'], 2); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['reservation_status']); ?>
                </td>

                <td>

                    <?php if ($row['reservation_status'] == "Pending") { ?>

                        <a href="edit_reservation.php?id=<?php echo $row['reservation_id']; ?>">
                            Edit
                        </a>

                        |

                        <a href="cancel_reservation.php?id=<?php echo $row['reservation_id']; ?>"
                           onclick="return confirm('Cancel this reservation?')">
                            Cancel
                        </a>

                    <?php } else { ?>

                        -

                    <?php } ?>

                </td>

            </tr>

        <?php } ?>

        </tbody>

    </table>

    <br>

    <a href="customer_index.php">← Back to Dashboard</a>

</div>

</body>

</html>
