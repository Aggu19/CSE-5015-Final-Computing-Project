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
    <title>Manage Users | LankanServices</title>

    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
</head>
<body>
<?php include 'header.php'; ?>
<main class="container">

    <h3 class="mt-5">Provider Profiles</h3>
    <div class="row mt-5">
        <?php 
        $select_providers = $conn->prepare("SELECT * FROM `service_providers`"); // fetches all providers
        $select_providers->execute();
        if($select_providers->rowCount() > 0){
            while($fetch_providers = $select_providers->fetch(PDO::FETCH_ASSOC)){
        ?>
            <div class="col-lg-4">
                <img src="../uploaded_img/<?= $fetch_providers['profile_picture']; ?>" class="bd-placeholder-img rounded-circle" width="140" height="140">
                <h2 class="fw-normal"><?= $fetch_providers['provider_name']; ?></h2>
                <p><?= $fetch_providers['email']; ?></p>
                <p><a class="btn btn-secondary" href="view_seller_profile.php?provider_id=<?= $fetch_providers['provider_id']; ?>">View Seller Profile &raquo;</a></p>
            </div>
        <?php 
            }
        }
        ?>
    </div>

    <h3 class="mt-5">Buyer Profiles</h3>
    <div class="row mt-5">
        <?php 
        $select_providers = $conn->prepare("SELECT * FROM `service_buyers`"); // fetches all buyers
        $select_providers->execute();
        if($select_providers->rowCount() > 0){
            while($fetch_buyers = $select_providers->fetch(PDO::FETCH_ASSOC)){
        ?>
            <div class="col-lg-4">
                <img src="../uploaded_img/<?= $fetch_buyers['profile_picture']; ?>" class="bd-placeholder-img rounded-circle" width="140" height="140">
                <h2 class="fw-normal"><?= $fetch_buyers['buyer_name']; ?></h2>
                <p><?= $fetch_buyers['email']; ?></p>
                <p><a class="btn btn-secondary" href="view_buyer_profile.php?buyer_id=<?= $fetch_buyers['buyer_id']; ?>">View Buyer Profile &raquo;</a></p>
            </div>
        <?php 
            }
        }
        ?>
    </div>

</main>
<?php include 'footer.php'; ?>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
</body>
</html>