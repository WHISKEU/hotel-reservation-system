<?php
include '../includes/connection.php';
include '../includes/customer_auth.php';

$user_id = (int) $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "
    SELECT
        reservations.reservation_id,
        reservations.check_in,
        reservations.check_out,
        reservations.total_price,
        reservations.reservation_status,
        reservations.created_at,
        rooms.room_id,
        rooms.room_number,
        room_types.type_name
    FROM reservations
    INNER JOIN rooms
        ON reservations.room_id = rooms.room_id
    INNER JOIN room_types
        ON rooms.room_type_id = room_types.room_type_id
    WHERE reservations
