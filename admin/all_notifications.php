<?php
@include '../db_config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:../index.php');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications | LankanServices</title>

    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="../css/adminnotifications.css" rel="stylesheet">
    
</head>
<body>
<?php include 'header.php'; ?>
<main class="container">

    <div class="d-flex align-items-center p-3 my-3 text-white bg-purple rounded shadow-sm">
        <img class="me-3" src="../images/bell-fill.svg" alt="" width="48" height="38">
        <div class="lh-1">
            <h1 class="h6 mb-0 text-white lh-1">Notifications</h1>
            <small>All Website Activities</small>
        </div>
    </div>
  
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h6 class="border-bottom pb-2 mb-0">All updates</h6>
    
        <?php 
        $select_notifications = $conn->prepare("SELECT * FROM `notifications` ORDER BY notification_id DESC"); // fetches all notifications and places the lastest one first
        $select_notifications->execute();
        if($select_notifications->rowCount() > 0){
            while($fetch_notifications = $select_notifications->fetch(PDO::FETCH_ASSOC)){ // while loop to display all notifications
        ?>
    
        <div class="d-flex text-body-secondary pt-3">
            <img src="../images/bell-fill.svg" class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="28" height="28">
            <p class="pb-3 mb-0 small lh-sm border-bottom">
                <strong class="d-block text-gray-dark">@<?= $fetch_notifications['username']; ?></strong>
                <?= $fetch_notifications['message']; ?>
            </p>
        </div>
    
        <?php } }else{ ?>
    
        <div class="d-flex text-body-secondary pt-3">
            <img src="../images/bell-fill.svg" class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="28" height="28">
            <p class="pb-3 mb-0 small lh-sm border-bottom">
                No notifications available ;(
            </p>
        </div>

        <?php } ?>
    </div>
</main>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>