<?php

session_start();
include 'includes/connection.php';

$error = "";

if (isset($_POST['login'])) {

    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (empty($email)) {
        $error = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (empty($password)) {
        $error = "Password is required.";
    } else {

        // Check if the email exists
        $stmt = mysqli_prepare($conn, "
            SELECT user_id, fname, role, password
            FROM users
            WHERE email = ?
        ");

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            // Get user data
            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {

                // Store session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['fname'] = $user['fname'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == "admin") {

                    header("Location: admin/dashboard.php");
                    exit();

                } else {

                    header("Location: customer/index.php");
                    exit();

                }

            } else {

                $error = "Invalid email or password.";

            }

        } else {

            $error = "Invalid email or password.";

        }

        mysqli_stmt_close($stmt);

    }

}

?>

<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">

    <div class="form-card">

        <div class="form-header">
            <h1>Hotel Reservation System</h1>
            <h2>Login</h2>
        </div>

        <?php if (!empty($error)) : ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required>
            </div>

            <button type="submit" name="login" class="btn-primary">
                Login
            </button>

        </form>

        <p>
            Don't have an account?
            <a href="registration.php">Register here</a>
        </p>

    </div>

</div>

</body>
</html>