<?php

include '../includes/admin_auth.php';
include '../includes/connection.php';
include '../includes/admin_sidebar.php';

$error = "";
$success = "";

$edit = false;

$type_name = "";
$description = "";
$price = "";
$capacity = "";

// Handle Add Room Type
if (isset($_POST['add_room_type'])) {

    $type_name = trim($_POST['type_name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $capacity = trim($_POST['capacity']);

    if (empty($type_name)) {

        $error = "Room type is required.";

    } elseif (empty($description)) {

        $error = "Description is required.";

    } elseif (empty($price)) {

        $error = "Price is required.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $error = "Price must be greater than zero.";

    } elseif (empty($capacity)) {

    $error = "Capacity is required.";

    } elseif (!is_numeric($capacity) || $capacity <= 0) {

        $error = "Capacity must be greater than zero.";

    } else {

        // Check duplicate
        $stmt = mysqli_prepare(
            $conn,
            "SELECT room_type_id FROM room_types WHERE type_name = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $type_name);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $error = "Room type already exists.";

        } else {

            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO room_types
                (type_name, description, price, capacity)
                VALUES (?, ?, ?, ?, )"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssdi",
                $type_name,
                $description,
                $price,
                $capacity
            );

            if (mysqli_stmt_execute($stmt)) {

                header("Location: room_types.php");
                exit();

            } else {

                $error = "Failed to add room type.";

            }

        }

        mysqli_stmt_close($stmt);

    }

}

$room_types = mysqli_query(
    $conn,
    "SELECT * FROM room_types ORDER BY room_type_id ASC"
);

// Handle Edit

if (isset($_GET['edit'])) {

    $edit_id = $_GET['edit'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM room_types WHERE room_type_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $edit_id);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        $edit = true;
        $room_type_id = $row['room_type_id'];

        $type_name = $row['type_name'];
        $description = $row['description'];
        $price = $row['price'];
        $capacity = $row['capacity'];

    } else {

        header("Location: room_types.php");
        exit();

    }

    mysqli_stmt_close($stmt);
}

// UPDATE

if (isset($_POST['update_room_type'])) {

    $room_type_id = $_POST['room_type_id'];
    $type_name = trim($_POST['type_name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $capacity = trim($_POST['capacity']);

    // Validation
    if (empty($type_name)) {

        $error = "Room type is required.";

    } elseif (empty($description)) {

        $error = "Description is required.";

    } elseif (empty($price)) {

        $error = "Price is required.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $error = "Price must be greater than zero.";

    } elseif (empty($capacity)) {

        $error = "Capacity is required.";

    } elseif (!is_numeric($capacity) || $capacity <= 0) {

        $error = "Capacity must be greater than zero.";

    }
    else {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE room_types
             SET type_name = ?, description = ?, price = ?, capacity = ?
             WHERE room_type_id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssdii",
            $type_name,
            $description,
            $price,
            $capacity,
            $room_type_id
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: room_types.php");
            exit();

        } else {

            $error = "Failed to update room type.";

        }

        mysqli_stmt_close($stmt);

    }

}

// Handle Delete
if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM room_types WHERE room_type_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $delete_id);

    if (mysqli_stmt_execute($stmt)) {

        header("Location: room_types.php");
        exit();

    } else {

        $error = "Failed to delete room type.";

    }

    mysqli_stmt_close($stmt);

}


?>



<!-- HTML content for the room types page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Types - Hotel Reservation System</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="content">

    <h1>Room Types</h1>

    <?php if(!empty($error)): ?>

        <div class="error">
            <?php echo $error; ?>
        </div>

    <?php endif; ?>

    <form method="POST">
        <?php if ($edit): ?>
            <input
                type="hidden"
                name="room_type_id"
                value="<?php echo $room_type_id; ?>">
        <?php endif; ?>


        <div class="form-group">

            <label>Room Type</label>

            <input type="text" name="type_name"
                value="<?php echo htmlspecialchars($type_name); ?>"
                required>

        </div>

        <div class="form-group">

            <label>Description</label>

            <textarea name="description" required><?php echo htmlspecialchars($description); ?></textarea>

        </div>

        <div class="form-group">

            <label>Price</label>

            <input type="number" name="price" step="0.01"min="1"
                value="<?php echo htmlspecialchars($price); ?>"
                required>

        </div>

        <div class="form-group">

            <label>Capacity</label>

            <input
                type="number"
                name="capacity"
                min="1"
                value="<?php echo htmlspecialchars($capacity); ?>"
                required>

        </div>

        <?php if ($edit): ?>

            <button type="submit" name="update_room_type">
                Update Room Type
            </button>

            <a href="room_types.php" class="btn-cancel">
                Cancel
            </a>

        <?php else: ?>

            <button type="submit" name="add_room_type">
                Add Room Type
            </button>

        <?php endif; ?>

    </form>

    <hr>

    <table>

        <tr>

            <th>ID</th>
            <th>Room Type</th>
            <th>Description</th>
            <th>Price</th>
            <th>Capacity</th>
            <th>Action</th>

        </tr>

        <?php while($row = mysqli_fetch_assoc($room_types)): ?>

        <tr>

            <td><?php echo $row['room_type_id']; ?></td>
            <td><?php echo htmlspecialchars($row['type_name']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td>₱<?php echo number_format($row['price'],2); ?></td>
            <td><?php echo $row['capacity']; ?></td>
            <td>

                <a href="room_types.php?edit=<?php echo $row['room_type_id']; ?>">

                    Edit

                </a>

                |

                <a
                    href="room_types.php?delete=<?php echo $row['room_type_id']; ?>"
                    onclick="return confirm('Delete this room type?');">
                    Delete
                </a>

            </td>

        </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>
</html>