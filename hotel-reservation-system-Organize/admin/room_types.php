<?php

include '../includes/admin_auth.php';
include '../includes/connection.php';

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
                VALUES (?, ?, ?, ?)"
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Types - Hotel Reservation System</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

<div class="wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="main-content">
        
        <div class="dashboard-header">
            <h1>Room Types</h1>
            <p>Create and manage your hotel's structural categories, descriptions, pricing, and capacity.</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #f5c6cb;">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
            
            <div class="card" style="background: rgba(255, 255, 255, 0.9); padding: 25px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05);">
                <h2 style="color: #7a5ca4; margin-bottom: 20px; font-size: 1.3rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    <?php echo $edit ? "Edit Room Type" : "Add Room Type"; ?>
                </h2>

                <form method="POST">
                    <?php if ($edit): ?>
                        <input
                            type="hidden"
                            name="room_type_id"
                            value="<?php echo $room_type_id; ?>">
                    <?php endif; ?>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; color: #555; margin-bottom: 5px; font-size: 0.9rem;">Room Type</label>
                        <input type="text" name="type_name"
                            value="<?php echo htmlspecialchars($type_name); ?>"
                            required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; color: #555; margin-bottom: 5px; font-size: 0.9rem;">Description</label>
                        <textarea name="description" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; height: 100px; resize: vertical; box-sizing: border-box; font-family: inherit;"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; color: #555; margin-bottom: 5px; font-size: 0.9rem;">Price</label>
                        <input type="number" name="price" step="0.01" min="1"
                            value="<?php echo htmlspecialchars($price); ?>"
                            required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; color: #555; margin-bottom: 5px; font-size: 0.9rem;">Capacity</label>
                        <input
                            type="number"
                            name="capacity"
                            min="1"
                            value="<?php echo htmlspecialchars($capacity); ?>"
                            required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <?php if ($edit): ?>
                            <button type="submit" name="update_room_type" style="background: #7a5ca4; color: white; padding: 10px 18px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s;">
                                Update Room Type
                            </button>
                            <a href="room_types.php" class="btn-cancel" style="background: #e2e8f0; color: #4a5568; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; text-align: center; line-height: 1.5; font-size: 0.9rem;">
                                Cancel
                            </a>
                        <?php else: ?>
                            <button type="submit" name="add_room_type" style="background: #7a5ca4; color: white; padding: 10px 18px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; width: 100%;">
                                Add Room Type
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="card" style="background: white; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background-color: #e8f0fe; border-bottom: 2px solid #ddd;">
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">ID</th>
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem;">Room Type</th>
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem;">Description</th>
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem;">Price</th>
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">Capacity</th>
                            <th style="padding: 15px; color: #7a5ca4; font-weight: bold; font-size: 0.9rem; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($room_types)): ?>
                        <tr style="border-bottom: 1px solid #eee; transition: background 0.2s; background: white;">
                            <td style="padding: 15px; text-align: center; color: #666; font-size: 0.9rem;"><?php echo $row['room_type_id']; ?></td>
                            <td style="padding: 15px; font-weight: 600; color: #333; font-size: 0.9rem;"><?php echo htmlspecialchars($row['type_name']); ?></td>
                            <td style="padding: 15px; color: #666; font-size: 0.85rem; max-width: 220px; word-wrap: break-word;"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td style="padding: 15px; color: #333; font-weight: 600; font-size: 0.9rem;">₱<?php echo number_format($row['price'],2); ?></td>
                            <td style="padding: 15px; text-align: center; color: #555; font-size: 0.9rem;"><?php echo $row['capacity']; ?></td>
                            <td style="padding: 15px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <a href="room_types.php?edit=<?php echo $row['room_type_id']; ?>" style="color: #7a5ca4; text-decoration: none; border: 1px solid #7a5ca4; padding: 5px 12px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; transition: 0.2s;">
                                        Edit
                                    </a>
                                    <span style="color: #ddd;">|</span>
                                    <a
                                        href="room_types.php?delete=<?php echo $row['room_type_id']; ?>"
                                        onclick="return confirm('Delete this room type?');"
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