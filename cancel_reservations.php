<?php
include '../includes/connection.php';
include '../includes/customer_auth.php';

if(!isset($_GET['id'])){
    header("Location: my_reservations.php");
    exit();
}

$reservation_id=(int)$_GET['id'];
$user_id=$_SESSION['user_id'];

$stmt=mysqli_prepare($conn,"
UPDATE reservations

SET reservation_status='Cancelled'

WHERE reservation_id=?

AND user_id=?
");

mysqli_stmt_bind_param(
$stmt,
"ii",
$reservation_id,
$user_id
);

mysqli_stmt_execute($stmt);

header("Location: my_reservations.php");
exit();
?>
