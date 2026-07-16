<?php

include 'includes/connection.php';

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

<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
<link rel="stylesheet" href="CSS/style.css">
</head>

<body>

<div class="container">

    <div class="form-card">

        <div class="form-header">
            <h1>Hotel Reservation System</h1>
            <h2>Create an Account</h2>
        </div>

        <?php if (!empty($error)) : ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)) : ?>
            <div class="success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">

            <div class="form-group">
                <label for="fname">First Name</label>
                <input
                    type="text" id="fname" name="fname" placeholder="First Name"
                    value="<?php echo isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : ''; ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="mname">Middle Name</label>
                <input type="text" id="mname" name="mname" placeholder="Middle Name"
                    value="<?php echo isset($_POST['mname']) ? htmlspecialchars($_POST['mname']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="lname">Last Name</label>
                <input type="text" id="lname" name="lname" placeholder="Last Name"
                    value="<?php echo isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : ''; ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="bday">Birthday</label>
                <input type="date" id="bday" name="bday"
                    value="<?php echo isset($_POST['bday']) ? $_POST['bday'] : ''; ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="09XXXXXXXXX"pattern="[0-9]{11}"
                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" minlength="8"
                    required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password"minlength="8"
                    required>
            </div>

            <button type="submit" name="submit" class="btn-primary">
                Register
            </button>

        </form>

        <p class="form-footer">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>

    </div>

</div>

</body>
</html>