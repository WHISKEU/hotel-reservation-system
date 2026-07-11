<?php

include '../includes/admin_auth.php';
include '../includes/connection.php';
include '../includes/admin_sidebar.php';

$error = "";
$success = "";

$edit = false;

$room_id = "";
$room_number = "";
$room_type_id = "";
$status = "";

$room_types = mysqli_query(
    $conn,
    "SELECT room_type_id, type_name
     FROM room_types
     ORDER BY type_name ASC"
);

// LOAD ROOM FOR EDIT

if (isset($_GET['edit'])) {

    $edit = true;

    $room_id = $_GET['edit'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM rooms WHERE room_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $room_id);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        $room_number = $row['room_number'];
        $room_type_id = $row['room_type_id'];
        $status = $row['status'];

    }

    mysqli_stmt_close($stmt);
}

if (isset($_POST['add_room'])) {

    $room_number = trim($_POST['room_number']);
    $room_type_id = $_POST['room_type_id'];
    $status = $_POST['status'];

    if (empty($room_number)) {

        $error = "Room number is required.";

    } elseif (empty($room_type_id)) {

        $error = "Please select a room type.";

    } elseif (empty($status)) {

        $error = "Please select a status.";

    } else {

        // Duplicate room number
        $stmt = mysqli_prepare(
            $conn,
            "SELECT room_id
             FROM rooms
             WHERE room_number = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $room_number
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $error = "Room number already exists.";

        } else {

            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO rooms
                (room_number, room_type_id, status)
                VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sis",
                $room_number,
                $room_type_id,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {

                header("Location: rooms.php");
                exit();

            } else {

                $error = "Failed to add room.";

            }

        }

        mysqli_stmt_close($stmt);

    }

}

// UPDATE ROOM
if (isset($_POST['update_room'])) {

    $room_id = $_POST['room_id'];
    $room_number = trim($_POST['room_number']);
    $room_type_id = $_POST['room_type_id'];
    $status = $_POST['status'];

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE rooms
         SET room_number = ?,
             room_type_id = ?,
             status = ?
         WHERE room_id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sisi",
        $room_number,
        $room_type_id,
        $status,
        $room_id
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: rooms.php");
        exit();

    } else {

        $error = "Failed to update room.";

    }

    mysqli_stmt_close($stmt);

}

// DELETE ROOM
if (isset($_GET['delete'])) {

    $room_id = $_GET['delete'];

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM rooms WHERE room_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $room_id);

    if (mysqli_stmt_execute($stmt)) {

        header("Location: rooms.php");
        exit();

    } else {

        die(mysqli_error($conn));
    }

}

$rooms = mysqli_query(
    $conn,
    "
    SELECT
        rooms.room_id,
        rooms.room_number,
        rooms.status,

        room_types.room_type_id,
        room_types.type_name,
        room_types.price,
        room_types.description

    FROM rooms

    INNER JOIN room_types

    ON rooms.room_type_id = room_types.room_type_id

    ORDER BY rooms.room_number
    "
);

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rooms</title>
        <link rel="stylesheet" href="../assets/css/admin.css">
    </head>
    <body>
        <div class="content">

            <h1>Rooms</h1>

            <?php if (!empty($error)): ?>
                <div class="error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">

                    <label>Room Number</label>

                    <input
                        type="text"
                        name="room_number"
                        value="<?php echo htmlspecialchars($room_number); ?>"
                        required>
                </div>

                <div class="form-group">

                    <label>Room Type</label>

                    <select name="room_type_id" required>

                        <option value="">
                            Select Room Type
                        </option>

                        <?php while($type = mysqli_fetch_assoc($room_types)): ?>

                            <option
                                value="<?php echo $type['room_type_id']; ?>"
                                <?php echo ($room_type_id == $type['room_type_id']) ? "selected" : ""; 
                                ?>
                            >

                                <?php echo htmlspecialchars($type['type_name']); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="form-group">

                <label>Status</label>

                <select name="status">

                <option value="Available"
                <?php if($status=="Available") echo "selected"; ?>>

                Available

                </option>

                <option value="Occupied"
                <?php if($status=="Occupied") echo "selected"; ?>>

                Occupied

                </option>

                <option value="Maintenance"
                <?php if($status=="Maintenance") echo "selected"; ?>>

                Maintenance

                </option>

                </select>

                </div>

            <?php if ($edit): ?>

                <input
                    type="hidden"
                    name="room_id"
                    value="<?php echo $room_id; ?>">

                <button
                    type="submit"
                    name="update_room">

                    Update Room

                </button>

                <a href="rooms.php">

                    Cancel

                </a>

            <?php else: ?>

                <button
                    type="submit"
                    name="add_room">

                    Add Room

                </button>

            <?php endif; ?>

            </form>
<hr>
<h2>Room List</h2>
<table border="1" cellpadding="10">

    <tr>
        <th>Room Number</th>
        <th>Room Type</th>
        <th>Price</th>
        <th>Description</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($rooms)): ?>

    <tr>

        <td><?php echo htmlspecialchars($row['room_number']); ?></td>

        <td><?php echo htmlspecialchars($row['type_name']); ?></td>

        <td>₱<?php echo number_format($row['price'], 2); ?></td>

        <td><?php echo htmlspecialchars($row['description']); ?></td>

        <td><?php echo htmlspecialchars($row['status']); ?></td>

        <td>

            <a href="rooms.php?edit=<?php echo $row['room_id']; ?>">
                Edit
            </a>

            |

            <a
                href="rooms.php?delete=<?php echo $row['room_id']; ?>"
                onclick="return confirm('Delete this room?');">

                Delete

            </a>

        </td>

    </tr>

    <?php endwhile; ?>

</table>

</div>

</body>
</html>