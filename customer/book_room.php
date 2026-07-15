<?php
include '../includes/connection.php';
include '../includes/customer_auth.php';
include '../includes/customer_sidebar.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: customer_index.php");
    exit();
}

if (
    !isset($_POST['room_id'], $_POST['check_in'], $_POST['check_out']) ||
    !is_numeric($_POST['room_id'])
) {
    die("Invalid request.");
}

$user_id = (int) $_SESSION['user_id'];
$room_id = (int) $_POST['room_id'];
$check_in = trim($_POST['check_in']);
$check_out = trim($_POST['check_out']);

if (empty($check_in) || empty($check_out)) {
    die("Please fill in all required fields.");
}

if ($check_out <= $check_in) {
    die("Check-out date must be after check-in date.");
}

/*get room details and price*/
$stmt = mysqli_prepare($conn, "
    SELECT
        rooms.room_id,
        rooms.status,
        room_types.price
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

if ($room['status'] !== 'Available') {
    die("This room is no longer available.");
}

$days = (int) ((strtotime($check_out) - strtotime($check_in)) / 86400);

if ($days < 1) {
    die("Invalid date range.");
}

$total_price = $days * (float)$room['price'];

/*  Insert reservation */
$stmt_insert = mysqli_prepare($conn, "
    INSERT INTO reservations
        (user_id, room_id, check_in, check_out, total_price, reservation_status)
    VALUES
        (?, ?, ?, ?, ?, 'Pending')
");

mysqli_stmt_bind_param($stmt_insert, "iissd", $user_id, $room_id, $check_in, $check_out, $total_price);

if (!mysqli_stmt_execute($stmt_insert)) {
    die("Failed to create reservation.");
}

/* Mark room as occupied so it won't show as available again*/
$stmt_update = mysqli_prepare($conn, "
    UPDATE rooms
    SET status = 'Occupied'
    WHERE room_id = ?
");

mysqli_stmt_bind_param($stmt_update, "i", $room_id);
mysqli_stmt_execute($stmt_update);

header("Location: my_reservations.php");
exit();
?>