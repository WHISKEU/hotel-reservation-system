<?php

include '../includes/admin_auth.php';
include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';

?>

<div class="content">

    <h1>
        Welcome,
        <?php echo htmlspecialchars($_SESSION['fname']); ?>!
    </h1>

    <p>Administrator Dashboard</p>

</div>

<?php

include '../includes/admin_footer.php';

?>