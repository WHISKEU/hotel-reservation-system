<?php

session_start();
include 'connection.php';

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
    <style>
    
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');


*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    background:linear-gradient(135deg,#cdb4db,#ffc8dd,#bde0fe);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 20px;
}

/* Container */

.container{
    width:100%;
    display:flex;
    justify-content:center;
}

/* Card */

.form-card{
    background:#ffffff;
    width:100%;
    max-width:520px;
    padding:35px;
    border-radius:20px;
    box-shadow:0 15px 35px rgba(0,0,0,.12);
    animation:fadeIn .5s ease;
}

/* Header */

.form-header{
    text-align:center;
    margin-bottom:25px;
}

.form-header h1{
    color:#8d6cab;
    font-size:28px;
    margin-bottom:5px;
}

.form-header h2{
    color:#666;
    font-size:18px;
    font-weight:400;
}

/* Form */

.form-group{
    margin-bottom:18px;
}

.form-group label{
    display:block;
    margin-bottom:7px;
    color:#555;
    font-weight:500;
}

.form-group input{
    width:100%;
    padding:13px 15px;
    border:2px solid #bde0fe;
    border-radius:12px;
    outline:none;
    font-size:15px;
    transition:.3s;
    background:#fafafa;
}

.form-group input:focus{
    border-color:#a2d2ff;
    background:#fff;
    box-shadow:0 0 10px rgba(162,210,255,.4);
}

/* Placeholder */

::placeholder{
    color:#aaa;
}

/* Button */

.btn-primary{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:#ffafcc;
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
    margin-top:8px;
}

.btn-primary:hover{
    background:#cdb4db;
    transform:translateY(-2px);
    box-shadow:0 8px 18px rgba(205,180,219,.5);
}

/* Footer */

.form-footer{
    text-align:center;
    margin-top:22px;
    color:#666;
}

.form-footer a{
    color:#8d6cab;
    text-decoration:none;
    font-weight:600;
}

.form-footer a:hover{
    text-decoration:underline;
}

/* Error */

.error{
    background:#ffe5ec;
    color:#d62828;
    border-left:5px solid #ffafcc;
    padding:12px;
    border-radius:10px;
    margin-bottom:20px;
}

/* Success */

.success{
    background:#d8f3dc;
    color:#2d6a4f;
    border-left:5px solid #a2d2ff;
    padding:12px;
    border-radius:10px;
    margin-bottom:20px;
}

/* Date input */

input[type="date"]{
    color:#555;
}

/* Animation */

@keyframes fadeIn{

    from{
        opacity:0;
        transform:translateY(20px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }

}

/* Responsive */

@media(max-width:600px){

    .form-card{
        padding:25px;
    }

    .form-header h1{
        font-size:23px;
    }

    .form-header h2{
        font-size:16px;
    }

}</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="assets/css/style.css">
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
