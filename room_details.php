<?php
include '../includes/connection.php';
include '../includes/customer_auth.php';

if (!isset($_GET['id'])) {
    die("Room not found.");
}

$room_id = $_GET['id'];

$query = mysqli_query($conn, "
SELECT<?php
include '../includes/connection.php';
include '../includes/customer_auth.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: customer_index.php");
    exit();
}

$room_id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "
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
");

mysqli_stmt_bind_param($stmt, "i", $room_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Room not found.");
}

$room = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link rel="stylesheet" href="../assets/css/customer_index.css">
</head>
<body>

<div class="container">
    <h1>Room Details</h1>

    <p><strong>Room Number:</strong> <?php echo htmlspecialchars($room['room_number']); ?></p>
    <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room['type_name']); ?></p>
    <p><strong>Price:</strong> ₱<?php echo number_format((float)$room['price'], 2); ?> / night</p>
    <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> person(s)</p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($room['description']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($room['status']); ?></p>

    <?php if ($room['status'] === 'Available'): ?>
        <hr>

        <form action="book_room.php" method="POST">
            <input type="hidden" name="room_id" value="<?php echo (int)$room['room_id']; ?>">

            <label>Check-in Date</label><br>
            <input type="date" name="check_in" required><br><br>

            <label>Check-out Date</label><br>
            <input type="date" name="check_out" required><br><br>

            <button type="submit">Book Now</button>
        </form>
    <?php else: ?>
        <p><strong>This room is not available.</strong></p>
    <?php endif; ?>

    <br>
    <a href="customer_index.php">← Back</a>
</div>

</body>
</html>
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

WHERE rooms.room_id = '$room_id'
");

$room = mysqli_fetch_assoc($query);

if (!$room) {
    die("Room not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Room Details</title>

    <link rel="stylesheet" href="../assets/css/customer_index.css">

</head>

<body>

<div class="container">

    <h1>Room Details</h1>

    <hr>

    <p><strong>Room Number:</strong> <?php echo htmlspecialchars($room['room_number']); ?></p>

    <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room['type_name']); ?></p>

    <p><strong>Price:</strong> ₱<?php echo number_format($room['price'], 2); ?></p>

    <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> Person(s)</p>

    <p><strong>Description:</strong></p>

    <p><?php echo htmlspecialchars($room['description']); ?></p>

    <p><strong>Status:</strong> <?php echo htmlspecialchars($room['status']); ?></p>

    <hr>

    <?php if ($room['status'] == "Available") { ?>

        <form action="book_room.php" method="POST">

            <input
                type="hidden"
                name="room_id"
                value="<?php echo $room['room_id']; ?>">

            <label>Check-in Date</label><br>

            <input
                type="date"
                name="check_in"
                required>

            <br><br>

            <label>Check-out Date</label><br>

            <input
                type="date"
                name="check_out"
                required>

            <br><br>

            <button type="submit">

                Book Room

            </button>

        </form>

    <?php } else { ?>

        <p><strong>This room is currently unavailable.</strong></p>

    <?php } ?>

    <br>

    <a href="customer_index.php">← Back</a>

</div>

</body>

</html>
