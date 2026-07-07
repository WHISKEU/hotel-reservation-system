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
    font-family:'Poppins',sans-serif;
}

body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;

    background:linear-gradient(
        135deg,
        #cdb4db 0%,
        #ffc8dd 35%,
        #ffafcc 65%,
        #bde0fe 85%,
        #a2d2ff 100%
    );
}

.container{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
}

.form-card{
    width:100%;
    max-width:430px;
    background:rgba(255,255,255,.90);
    backdrop-filter:blur(12px);
    border-radius:22px;
    padding:40px;
    box-shadow:
        0 20px 40px rgba(0,0,0,.15);
    animation:slideUp .6s ease;
}

.form-header{
    text-align:center;
    margin-bottom:30px;
}

.form-header h1{
    color:#7a5ca4;
    font-size:32px;
    margin-bottom:8px;
    font-weight:700;
}

.form-header h2{
    color:#666;
    font-size:20px;
    font-weight:400;
}

.form-group{
    margin-bottom:22px;
}

.form-group label{
    display:block;
    margin-bottom:8px;
    color:#555;
    font-weight:500;
}

.form-group input{
    width:100%;
    padding:15px;
    border:2px solid #bde0fe;
    border-radius:14px;
    background:#fff;
    font-size:15px;
    transition:.3s ease;
}

.form-group input::placeholder{
    color:#999;
}

.form-group input:focus{
    outline:none;
    border-color:#ffafcc;
    box-shadow:0 0 0 5px rgba(255,175,204,.25);
    transform:translateY(-2px);
}

.btn-primary{
    width:100%;
    padding:15px;
    border:none;
    border-radius:14px;
    background:linear-gradient(
        90deg,
        #ffafcc,
        #cdb4db
    );

    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition:.35s;
}

.btn-primary:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 25px rgba(205,180,219,.45);
}

.btn-primary:active{
    transform:scale(.98);
}

.error{
    margin-bottom:20px;
    padding:14px;
    background:#ffe5ec;
    border-left:6px solid #ffafcc;
    border-radius:10px;
    color:#b00020;
    font-size:14px;
}

p{

    margin-top:25px;

    text-align:center;

    color:#666;
}

p a{
    color:#7a5ca4;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
}

p a:hover{
    color:#ff69a8;
    text-decoration:underline;
}

::-webkit-scrollbar{
    width:8px;
}

::-webkit-scrollbar-thumb{
    background:#cdb4db;
    border-radius:20px;
}

@keyframes slideUp{

    from{
        opacity:0;
        transform:translateY(30px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

@media(max-width:500px){
    .form-card{
        padding:30px 25px;
    }

    .form-header h1{
        font-size:26px;
    }

    .form-header h2{
        font-size:18px;
    }

}
  </style>
    
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
