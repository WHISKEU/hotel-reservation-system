<?php
include '../includes/connection.php';
include '../includes/customer_auth.php';

// Fetch available rooms
$rooms = mysqli_query(
    $conn,
    "
    SELECT
        rooms.room_id,
        rooms.room_number,
        rooms.status,

        room_types.type_name,
        room_types.price,
        room_types.capacity,
        room_types.description

    FROM rooms

    INNER JOIN room_types
        ON rooms.room_type_id = room_types.room_type_id

    WHERE rooms.status = 'Available'

    ORDER BY rooms.room_number ASC
    "
);
?>
<!--
    Customer Dashboard Page
    Displays a welcome message and a list of available rooms.
-->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>

    <link rel="stylesheet" href="../assets/css/customer_index.css">
</head>

<body>

<div class="container">

    <h1>
        Welcome,
        <?php echo htmlspecialchars($_SESSION['fname']); ?>!
    </h1>

    <h2>Available Rooms</h2>

    <table border="1" cellpadding="10">

        <thead>

            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Price</th>
                <th>Capacity</th>
                <th>Description</th>
                <th>Action</th>
            </tr>

        </thead>

        <tbody>

        <?php while ($row = mysqli_fetch_assoc($rooms)): ?>

            <tr>

                <td><?php echo htmlspecialchars($row['room_number']); ?></td>

                <td><?php echo htmlspecialchars($row['type_name']); ?></td>

                <td>₱<?php echo number_format($row['price'],2); ?></td>

                <td><?php echo htmlspecialchars($row['capacity']); ?></td>

                <td><?php echo htmlspecialchars($row['description']); ?></td>

                <td>

                    <a href="room_details.php?id=<?php echo $row['room_id']; ?>">

                        View Room

                    </a>

                </td>

            </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

</div>

</body>
</html>