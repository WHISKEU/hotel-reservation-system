<?php

$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "hotel_reservation_db";

$conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

echo "Connected successfully!";

?>