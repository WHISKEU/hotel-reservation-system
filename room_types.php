<?php

include '../includes/admin_auth.php';
include '../includes/connection.php';

$error = "";
$success = "";

$edit = false;

$type_name = "";
$description = "";
$price = "";

// Handle Add Room Type
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

    // Validation
    if (empty($type_name)) {

        $error = "Room type is required.";

    } elseif (empty($description)) {

        $error = "Description is required.";

    } elseif (empty($price)) {

        $error = "Price is required.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $error = "Price must be greater than zero.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE room_types
             SET type_name = ?, description = ?, price = ?
             WHERE room_type_id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssdi",
            $type_name,
            $description,
            $price,
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

    <style>
   
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');



*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{

    background:linear-gradient(
        135deg,
        #cdb4db,
        #ffc8dd,
        #bde0fe,
        #a2d2ff
    );

    min-height:100vh;
    padding:40px;
}



.content{

    max-width:1100px;
    margin:auto;
    background:#fff;
    padding:35px;
    border-radius:20px;
    box-shadow:0 15px 35px rgba(0,0,0,.12);
}

h1{
    text-align:center;
    color:#7a5ca4;
    margin-bottom:30px;
    font-size:32px;
}

.error{
    background:#ffe5ec;
    color:#b00020;
    border-left:6px solid #ffafcc;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

form{
    margin-bottom:35px;
}

.form-group{
    margin-bottom:20px;
}

.form-group label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
    color:#555;
}

.form-group input,
.form-group textarea{
    width:100%;
    padding:14px;
    border:2px solid #bde0fe;
    border-radius:12px;
    font-size:15px;
    transition:.3s;
    resize:vertical;
}

.form-group textarea{
    min-height:120px;
}

.form-group input:focus,
.form-group textarea:focus{
    outline:none;
    border-color:#ffafcc;
    box-shadow:0 0 0 5px rgba(255,175,204,.25);
}

button{
    background:linear-gradient(
        90deg,
        #ffafcc,
        #cdb4db
    );

    color:white;
    border:none;
    padding:12px 24px;
    border-radius:12px;
    cursor:pointer;
    font-size:15px;
    font-weight:600;
    transition:.3s;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(205,180,219,.4);
}

.btn-cancel{
    display:inline-block;
    margin-left:10px;
    padding:12px 24px;
    background:#a2d2ff;
    color:#333;
    text-decoration:none;
    border-radius:12px;
    font-weight:600;
    transition:.3s;
}

.btn-cancel:hover{
    background:#bde0fe;
}

hr{
    margin:35px 0;
    border:none;
    border-top:2px solid #e6e6e6;
}

table{
    width:100%;
    border-collapse:collapse;
    overflow:hidden;
    border-radius:15px;
    background:white;
}

table th{
    background:#cdb4db;
    color:white;
    padding:15px;
    text-align:left;
}

table td{
    padding:15px;
    border-bottom:1px solid #eee;
}

table tr:nth-child(even){
    background:#fdf7fb;
}

table tr:hover{
    background:#eef7ff;
    transition:.3s;
}

table a{
    text-decoration:none;
    font-weight:600;
    color:#7a5ca4;
    transition:.3s;
}

table a:hover{
    color:#ff69a8;
}

@media(max-width:768px){
    body{
        padding:20px;
    }

    .content{
        padding:20px;
        overflow-x:auto;
    }

    table{
        min-width:700px;
    }

    h1{
        font-size:26px;
    }

    button,
    .btn-cancel{
        width:100%;
        margin-top:10px;
        margin-left:0;
    }

}
  </style>
    
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

            <label>Room Type (e.g. Standard Room, Deluxe Room, Suite, Family Room)</label>

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
