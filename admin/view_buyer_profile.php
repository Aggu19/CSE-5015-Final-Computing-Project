<?php
@include '../db_config.php';
session_start();

$admin_id = $_SESSION['admin_id'];
$buyer_id = $_GET['buyer_id'];

if(!isset($admin_id)){
    header('location:../index.php');
};

if($buyer_id == '' || !isset($buyer_id)){
    header('location:admin_users.php');
};

if(isset($_GET['delete'])){
    $buyer_id = $_GET['delete'];
    $delete_query = $conn->prepare("DELETE FROM `service_buyers` WHERE buyer_id = ? ");
    $delete_query->execute([$buyer_id]);
    if($delete_query){
      echo"<script>alert('Service Buyer Profile Deleted!');</script>";
    }
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Buyer Profile | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <main>
        <?php include 'header.php';

        $select_buyer = $conn->prepare("SELECT * FROM `service_buyers` WHERE buyer_id = '$buyer_id'");
        $select_buyer->execute();
        $fetch_buyer = $select_buyer->fetch(PDO::FETCH_ASSOC);
        ?>
        
        <div class="container mt-3">
            <div class="row">

                <div class="col-4">
                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                            <img src="../uploaded_img/<?= $fetch_buyer['profile_picture']; ?>" width="100" height="100" alt="Profile" class="rounded-circle">
                            <h3><?= $fetch_buyer['buyer_name']; ?></h3>
                            <h4><?= $fetch_buyer['email']; ?> <?php if($fetch_buyer['is_verified'] == 1) { ?> <img src="../images/patch-check-fill.svg" title="Email is verified"> <?php } ?></h4>
                        </div>
                    </div>
                    <a href="view_buyer_profile.php?delete=<?= $fetch_buyer['buyer_id']; ?>" class="btn btn-outline-danger mt-3" onclick="return confirm('Do you really want to delete this buyer profile?')" >Delete This Buyer Profile</a>
                </div>

                <?php
                $show_bookings = $conn->prepare("SELECT * FROM `service_bookings` WHERE buyer_id = '$buyer_id' ORDER BY booking_id DESC");
                $show_bookings->execute();
                if($show_bookings->rowCount() > 0){
                while($fetch_bookings = $show_bookings->fetch(PDO::FETCH_ASSOC)){

                    $service_id = $fetch_bookings['service_id'];
                    $service_details = $conn->prepare("SELECT * FROM `services` WHERE service_id = '$service_id'");
                    $service_details->execute();
                    $fetch_service = $service_details->fetch(PDO::FETCH_ASSOC);
                ?>

                <div class="col-4">
                    <div class="card shadow-sm">
                        <img src="../uploaded_img/<?= $fetch_service['image']; ?>" class="bd-placeholder-img card-img-top" width="225" height="225" >
                        <div class="card-body">
                            <p class="card-text">Service: <?= $fetch_service['service_name']; ?></p>
                            <p class="card-text">Provider: <?= $fetch_service['provider_name']; ?></p>
                            <p class="card-text">Appointment: <?= $fetch_bookings['appointment_date']; ?></p>
                            <?php if($fetch_bookings['service_bundle'] != ''){ ?><p class="card-text">Added Service Bundle: <?= $fetch_bookings['service_bundle']; ?></p><?php } ?>
                            <p class="card-text">Price: LKR <?= $fetch_service['price']; ?>/=</p>
                            <p class="card-text"><?= $fetch_service['category']; ?> / <?= $fetch_service['sub_category']; ?></p>
                            <p class="card-text">Payment Method: <?= $fetch_bookings['payment_method']; ?></p>
                            <p class="card-text" style="color: <?php if($fetch_bookings['payment_status'] == 'pending'){echo'cadetblue';}elseif($fetch_bookings['payment_status'] == 'paid'){echo'green';}else{echo'red';} ?>">Payment Status: <?= $fetch_bookings['payment_status']; ?></p>
                            <p class="card-text" style="color: <?php if($fetch_bookings['status'] == 'pending'){echo'cadetblue';}elseif($fetch_bookings['status'] == 'approved'){echo'blue';}elseif($fetch_bookings['status'] == 'completed'){echo'green';}else{echo'red';} ?> ">Booking Status: <?= $fetch_bookings['status']; ?></p>
                        </div>
                    </div>
                </div>

                <?php } }else{ ?>
            </div>
        </div>
    
        <div class="container mt-5">
            <div class="row">
                <div class="col-5">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="text">No bookings available from this buyer</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>   

        <?php } ?>

    </main>
    
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>