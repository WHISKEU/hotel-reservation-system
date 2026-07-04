<?php

include 'connection.php';

$error = "";
$success = "";

if (isset($_POST['submit'])) {

    // Get form data
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname']);
    $lname = trim($_POST['lname']);
    $bday = $_POST['bday'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($fname)) {
        $error = "First name is required.";
    } elseif (empty($lname)) {
        $error = "Last name is required.";
    } elseif (empty($bday)) {
        $error = "Birthday is required.";
    } elseif (empty($email)) {
        $error = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (empty($phone)) {
        $error = "Phone number is required.";
    } elseif (!preg_match("/^[0-9]{11}$/", $phone)) {
        $error = "Phone number must be exactly 11 digits.";
    } elseif (empty($password)) {
        $error = "Password is required.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif (empty($confirm_password)) {
        $error = "Please confirm your password.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {

        // Check if email already exists
        $stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $error = "Email already exists.";

        } else {

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Set default role
            $role = "customer";

            // Insert new user
            $stmt = mysqli_prepare($conn, "
                INSERT INTO users
                (fname, mname, lname, bday, email, phone, role, password)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssss",
                $fname,
                $mname,
                $lname,
                $bday,
                $email,
                $phone,
                $role,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {

                header("Location: login.php");
                exit();

            } else {

                $error = "Registration failed.";

            }

        }
    }
}

?>