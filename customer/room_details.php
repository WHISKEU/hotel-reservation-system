<?php

include '../includes/customer_auth.php';
include '../includes/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$room_id = (int) $_GET['id'];

$stmt = mysqli_prepare(
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
    WHERE rooms.room_id = ?
    LIMIT 1
    "
);

mysqli_stmt_bind_param($stmt, "i", $room_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$room = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Details</title>
    <link rel="stylesheet" href="../assets/css/customer.css">
</head>
<body>

<div class="container">

    <h1>
        Room <?php echo htmlspecialchars($room['room_number']); ?>
    </h1>

    <hr>

    <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room['type_name']); ?></p>
    <p><strong>Price:</strong> ₱<?php echo number_format($room['price'], 2); ?></p>
    <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> Guest(s)</p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($room['description']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($room['status']); ?></p>

    <?php if ($room['status'] == "Available"): ?>
        <form method="POST" action="book_room.php">
            <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">

            <label>Check-in Date</label>
            <input type="date" name="check_in" required>

            <label>Check-out Date</label>
            <input type="date" name="check_out" required>

            <button type="submit">Book Room</button>
        </form>
    <?php else: ?>
        <p>This room is currently unavailable.</p>
    <?php endif; ?>

    <a href="index.php">← Back to Available Rooms</a>

</div>

</body>
</html>