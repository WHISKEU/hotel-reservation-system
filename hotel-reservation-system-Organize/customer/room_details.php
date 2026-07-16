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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/customer.css">
</head>
<body>

<div class="wrapper">
    <?php include '../includes/customer_sidebar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1>Room <?php echo htmlspecialchars($room['room_number']); ?></h1>
        </div>

        <div class="details-layout">
            <section class="room-card">
                <h2>Room Information</h2>

                <div class="room-info-grid">
                    <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room['type_name']); ?></p>
                    <p><strong>Price:</strong> ₱<?php echo number_format($room['price'], 2); ?></p>
                    <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> Guest(s)</p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($room['description']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($room['status']); ?></p>
                </div>

                <a class="back-link" href="index.php">← Back to Available Rooms</a>
            </section>

            <aside class="booking-card">
                <?php if ($room['status'] == "Available"): ?>
                    <h2>Book This Room</h2>

                    <form method="POST" action="book_room.php">
                        <input type="hidden" name="room_id" value="<?php echo (int)$room['room_id']; ?>">

                        <label for="check_in">Check-in Date</label>
                        <input type="date" id="check_in" name="check_in" required>

                        <label for="check_out">Check-out Date</label>
                        <input type="date" id="check_out" name="check_out" required>

                        <button type="submit" class="btn-primary">Book Room</button>
                    </form>
                <?php else: ?>
                    <h2>Unavailable</h2>
                    <p>This room is currently unavailable.</p>
                <?php endif; ?>
            </aside>
        </div>
    </main>
</div>

</body>
</html>