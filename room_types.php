<?php

include '../includes/admin_auth.php';
include '../includes/connection.php';

$error = "";
$success = "";

$type_name = "";
$description = "";
$price = "";

if (isset($_POST['add_room_type'])) {

    $type_name = trim($_POST['type_name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);

    if (empty($type_name)) {

        $error = "Room type is required.";

    } elseif (empty($description)) {

        $error = "Description is required.";

    } elseif (empty($price)) {

        $error = "Price is required.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $error = "Price must be greater than zero.";

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
                (type_name, description, price)
                VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssd",
                $type_name,
                $description,
                $price
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

include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';
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

        <button
            type="submit"
            name="add_room_type">

            Add Room Type

        </button>

    </form>

    <hr>

    <table>

        <tr>

            <th>ID</th>
            <th>Room Type</th>
            <th>Description</th>
            <th>Price</th>
            <th>Action</th>

        </tr>

        <?php while($row = mysqli_fetch_assoc($room_types)): ?>

        <tr>

            <td><?php echo $row['room_type_id']; ?></td>
            <td><?php echo htmlspecialchars($row['type_name']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td>₱<?php echo number_format($row['price'],2); ?></td>
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

<?php

include '../includes/admin_footer.php';

?>