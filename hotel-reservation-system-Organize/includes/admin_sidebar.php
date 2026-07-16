<aside class="sidebar">

    <h2>Admin Panel</h2>
    <p>
        Welcome,<br>
        <?php echo htmlspecialchars($_SESSION['fname']); ?>
    </p>

    <ul class="sidebar-menu">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="rooms.php">Rooms</a></li>
        <li><a href="room_types.php">Room Types</a></li>
        <li><a href="reservations.php">Reservations</a></li>
    </ul>

    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-btn">
            Logout
        </a>
    </div>

</aside>