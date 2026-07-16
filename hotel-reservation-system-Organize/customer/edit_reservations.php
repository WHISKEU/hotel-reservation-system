<?php
include '../includes/connection.php';
include '../includes/customer_auth.php';

if (!isset($_GET['id'])) {
    header("Location: my_reservations.php");
    exit();
}

$reservation_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn,"
SELECT *
FROM reservations
WHERE reservation_id=?
AND user_id=?
");

mysqli_stmt_bind_param($stmt,"ii",$reservation_id,$user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){
    die("Reservation not found.");
}

$reservation=mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

    $check_in=$_POST['check_in'];
    $check_out=$_POST['check_out'];

    $stmt=mysqli_prepare($conn,"
    UPDATE reservations
    SET
        check_in=?,
        check_out=?
    WHERE reservation_id=?
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $check_in,
        $check_out,
        $reservation_id
    );

    mysqli_stmt_execute($stmt);

    header("Location: my_reservations.php");
    exit();
}
?>

<!DOCTYPE html>

<html>

<head>
<link rel="stylesheet" href="../CSS/style.css">
<link rel="stylesheet" href="../CSS/customer.css">

<title>Edit Reservation</title>

</head>

<body>

<h2>Edit Reservation</h2>

<form method="POST">

<label>Check In</label><br>

<input
type="date"
name="check_in"
value="<?php echo $reservation['check_in'];?>"
required>

<br><br>

<label>Check Out</label><br>

<input
type="date"
name="check_out"
value="<?php echo $reservation['check_out'];?>"
required>

<br><br>

<button
type="submit"
name="update">

Update Reservation

</button>

</form>

</body>

</html>