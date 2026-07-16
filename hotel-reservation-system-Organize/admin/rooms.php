<?php

include '../includes/admin_auth.php';
include '../includes/connection.php';

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
    <title>Rooms - Hotel Reservation System</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

<div class="wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="main-content">
        
        <div class="dashboard-header">
            <h1>Rooms</h1>
            <p>Assign actual, physical rooms to your existing categories, set status flags, and list active inventory.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #f5c6cb;">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
            
            <div class="card" style="background: rgba(255, 255, 255, 0.9); padding: 25px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05);">
                <h2 style="color: #7a5ca4; margin-bottom: 20px; font-size: 1.3rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    <?php echo $edit ? "Edit Room" : "Add Room"; ?>
                </h2>

                <form method="POST">
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; color: #555; margin-bottom: 5px; font-size: 0.9rem;">Room Number</label>
                        <input
                            type="text"
                            name="room_number"
                            value="<?php echo htmlspecialchars($room_number); ?>"
                            required 
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; color: #555; margin-bottom: 5px; font-size: 0.9rem;">Room Type</label>
                        <select name="room_type_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; background: white;">
                            <option value="">Select Room Type</option>
                            <?php while($type = mysqli_fetch_assoc($room_types)): ?>
                                <option
                                    value="<?php echo $type['room_type_id']; ?>"
                                    <?php echo ($room_type_id == $type['room_type_id']) ? "selected" : ""; ?>>
                                    <?php echo htmlspecialchars($type['type_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; color: #555; margin-bottom: 5px; font-size: 0.9rem;">Status</label>
                        <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; background: white;">
                            <option value="Available" <?php if($status=="Available") echo "selected"; ?>>Available</option>
                            <option value="Occupied" <?php if($status=="Occupied") echo "selected"; ?>>Occupied</option>
                            <option value="Maintenance" <?php if($status=="Maintenance") echo "selected"; ?>>Maintenance</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <?php if ($edit): ?>
                            <input
                                type="hidden"
                                name="room_id"
                                value="<?php echo $room_id; ?>">

                            <button type="submit" name="update_room" style="background: #7a5ca4; color: white; padding: 10px 18px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; flex: 1;">
                                Update Room
                            </button>
                            <a href="rooms.php" class="btn-cancel" style="background: #e2e8f0; color: #4a5568; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; text-align: center; line-height: 1.5; font-size: 0.9rem;">
                                Cancel
                            </a>
                        <?php else: ?>
                            <button type="submit" name="add_room" style="background: #7a5ca4; color: white; padding: 10px 18px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; width: 100%;">
                                Add Room
                            </button>
                        <?php endif; ?>
                    </div>

                </form>
            </div>

            <div class="card" style="background: white; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background-color: #e8f0fe; border-bottom: 2px solid #ddd;">
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">Room Number</th>
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem;">Room Type</th>
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem;">Price</th>
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem;">Description</th>
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">Status</th>
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($rooms)): ?>
                        <tr style="border-bottom: 1px solid #eee; transition: background 0.2s; background: white;">
                            <td style="padding: 15px; text-align: center; font-weight: 600; color: #333; font-size: 0.9rem;"><?php echo htmlspecialchars($row['room_number']); ?></td>
                            <td style="padding: 15px; color: #555; font-size: 0.9rem;"><?php echo htmlspecialchars($row['type_name']); ?></td>
                            <td style="padding: 15px; color: #333; font-weight: 600; font-size: 0.9rem;">₱<?php echo number_format($row['price'], 2); ?></td>
                            <td style="padding: 15px; color: #666; font-size: 0.85rem; max-width: 200px; word-wrap: break-word;"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td style="padding: 15px; text-align: center;">
                                <?php 
                                    $statusClass = "color: #2b6cb0; background: #ebf8ff; border: 1px solid #bee3f8;";
                                    if ($row['status'] == 'Occupied') {
                                        $statusClass = "color: #c53030; background: #fff5f5; border: 1px solid #fed7d7;";
                                    } elseif ($row['status'] == 'Maintenance') {
                                        $statusClass = "color: #9c4221; background: #fffaf0; border: 1px solid #feebc8;";
                                    }
                                ?>
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <a href="rooms.php?edit=<?php echo $row['room_id']; ?>" style="color: #7a5ca4; text-decoration: none; border: 1px solid #7a5ca4; padding: 5px 12px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; transition: 0.2s;">
                                        Edit
                                    </a>
                                    <span style="color: #ddd;">|</span>
                                    <a
                                        href="rooms.php?delete=<?php echo $row['room_id']; ?>"
                                        onclick="return confirm('Delete this room?');"
                                        style="color: #e53e3e; text-decoration: none; border: 1px solid #e53e3e; padding: 5px 12px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; transition: 0.2s;">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

</body>
</html>