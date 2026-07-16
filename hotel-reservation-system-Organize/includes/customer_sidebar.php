<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<aside class="sidebar">
    <h2>Welcome,</h2>
    <p><?php echo htmlspecialchars($_SESSION['fname'] ?? 'Customer'); ?>!</p>

    <ul class="sidebar-menu">
        <li><a href="index.php">Home</a></li>
        <li><a href="my_reservations.php">My Reservations</a></li>
    </ul>

    <div class="sidebar-footer">
       <a href="../logout.php" class="logout-btn">Logout</a>
    </div>
</aside>