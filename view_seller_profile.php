<?php
@include 'db_config.php';
session_start();

if(isset($_SESSION['buyer_id'])){
    $buyer_id = $_SESSION['buyer_id'];
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Provider Profile | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <main>
        <?php include 'header.php'; 
        $provider_id = $_GET['provider_id'];
        $select_provider = $conn->prepare("SELECT * FROM `service_providers` WHERE provider_id = '$provider_id'");
        $select_provider->execute();
        $fetch_providers = $select_provider->fetch(PDO::FETCH_ASSOC);
        ?>

        <div class="container mt-3">
            <div class="row">

                <div class="col-4">
                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                            <img src="uploaded_img/<?= $fetch_providers['profile_picture']; ?>" width="100" height="100" alt="Profile" class="rounded-circle">
                            <h3><?= $fetch_providers['provider_name']; ?></h3> 
                            <h4><?= $fetch_providers['email']; ?> <?php if($fetch_providers['is_verified'] == 1) { ?> <img src="images/patch-check-fill.svg" title="Email is verified"> <?php } ?></h4>
                            <p><?= $fetch_providers['provider_bio']; ?></p> 
                        </div>
                    </div>
                </div>

                <?php
                $prov = $fetch_providers['provider_name'];
                $show_products = $conn->prepare("SELECT * FROM `services` WHERE provider_name = '$prov' AND approval = 'yes' ORDER BY service_id DESC");
                $show_products->execute();
                if($show_products->rowCount() > 0){
                    while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                ?>

                <div class="col-4">
                    <div class="card shadow-sm">
                        <img src="uploaded_img/<?= $fetch_products['image']; ?>" class="bd-placeholder-img card-img-top" width="100%" height="225" >
                        <div class="card-body">
                            <p class="card-text"><?= $fetch_products['service_name']; ?></p>
                            <p class="card-text"><?= $fetch_products['description']; ?></p>
                            <p class="card-text">Price: LKR <?= $fetch_products['price']; ?>/=</p>
                            <p class="card-text"><?= $fetch_products['category']; ?> / <?= $fetch_products['sub_category']; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <?php
                                    $provider_name =  $fetch_products['provider_name'];
                                    $select_provider_id = $conn->prepare("SELECT provider_id FROM `service_providers` WHERE provider_name = '$provider_name'");
                                    $select_provider_id->execute();
                                    $fetch_provider_id = $select_provider_id->fetch(PDO::FETCH_ASSOC);

                                    if(!isset($buyer_id)){?> <!-- checks if user is logged in and blocks from accessing the checkout page-->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="usernotloggedin()">Book Service</button>
                                    <?php }else{ ?>
                                        <form action="book_service.php" method="get">
                                            <input type="hidden" name="buyer_id" value="<?= $fetch_profile['buyer_id']; ?>">
                                            <input type="hidden" name="service_id" value="<?= $fetch_products['service_id']; ?>">
                                            <input type="hidden" name="provider_id" value="<?= $fetch_provider_id['provider_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" name="book">Book Service</a>
                                        </form>
                                    <?php } ?>
                                </div>
                                <small class="text-body-secondary"><?= $fetch_products['created_at']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <?php } }else{ ?>
                    <div class="container mt-5">
                        <div class="row">
                            <div class="col-5">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h4 class="text">This provider haven't posted any services yet</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                <?php } ?> 

            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/common.js"></script>
</body>
</html>