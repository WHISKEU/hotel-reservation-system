<?php

include '../includes/admin_auth.php';
include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

</head>

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
}

.content{
    margin-left:260px;
    padding:40px;
}

.dashboard-header{
    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    margin-bottom:35px;
}

.dashboard-header h1{
    color:#7d5ba6;
    font-size:34px;
    margin-bottom:10px;
}

.dashboard-header p{
    color:#666;
    font-size:18px;
}

.dashboard-cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
    gap:25px;
    margin-bottom:35px;
}

.card{
    background:white;
    border-radius:20px;
    padding:30px;
    text-align:center;
    transition:.35s;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    cursor:pointer;
}

.card:hover{
    transform:translateY(-8px);
    box-shadow:0 15px 30px rgba(205,180,219,.45);
}

.card:nth-child(1){
    border-top:8px solid #cdb4db;
}

.card:nth-child(2){
    border-top:8px solid #ffc8dd;
}

.card:nth-child(3){
    border-top:8px solid #ffafcc;
}

.card:nth-child(4){
    border-top:8px solid #a2d2ff;
}

.card:nth-child(5){
    border-top:8px solid #bde0fe;
}

.card:nth-child(6){
    border-top:8px solid #ffafcc;
}

.icon{
    font-size:55px;
    margin-bottom:18px;
}

.card h2{
    color:#7d5ba6;
    margin-bottom:10px;
    font-size:24px;
}

.card p{
    color:#666;
    line-height:1.6;
}

.welcome-box{
    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.welcome-box h2{
    color:#7d5ba6;
    margin-bottom:15px;
}

.welcome-box p{
    color:#666;
    line-height:1.8;
}

@media(max-width:900px){

.content{
    margin-left:0;
    padding:20px;
}

.dashboard-header h1{
    font-size:28px;
}

.dashboard-cards{
    grid-template-columns:1fr;
}
</style>

    
<body>

<div class="content">

    <div class="dashboard-header">

        <h1>
            Welcome,
            <?php echo htmlspecialchars($_SESSION['fname']); ?>!
        </h1>

        <p>Hotel Reservation System Administrator Dashboard</p>

    </div>

    <div class="dashboard-cards">

        <div class="card">

            <div class="icon">🏨</div>

            <h2>Rooms</h2>

            <p>Manage hotel rooms and availability.</p>

        </div>

        <div class="card">

            <div class="icon">🛏️</div>

            <h2>Room Types</h2>

            <p>Create and update room categories.</p>

        </div>

        <div class="card">

            <div class="icon">📅</div>

            <h2>Reservations</h2>

            <p>Manage all guest reservations.</p>

        </div>

        <div class="card">

            <div class="icon">👥</div>

            <h2>Guests</h2>

            <p>View registered customers.</p>

        </div>

        <div class="card">

            <div class="icon">💳</div>

            <h2>Payments</h2>

            <p>Track reservation payments.</p>

        </div>

        <div class="card">

            <div class="icon">📊</div>

            <h2>Reports</h2>

            <p>Generate reservation reports.</p>

        </div>

    </div>

    <div class="welcome-box">

        <h2>Administrator Panel</h2>

        <p>

            Welcome to the Hotel Reservation System Admin Dashboard.

            Use the navigation menu on the left to manage hotel rooms,
            reservations, guests, payments, room types, and reports.

        </p>

    </div>

</div>

</body>

</html>

<?php

include '../includes/admin_footer.php';

?>
