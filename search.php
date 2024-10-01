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
    <title>Search for services | LankanServices</title>

    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<body>
    <main>
        <?php include 'header.php'; ?>

        <div class="container mt-5">
            <form action="" method="post" class="d-flex" role="search">
                <input class="form-control me-2" type="search" value="<?php echo($_POST['search_box']) ?>" name="search_box" aria-label="Search" required>
                <button class="btn btn-lg btn-primary" name="search_btn" value="search" type="submit">Search</button>
            </form>
        </div>

        <div class="container mt-5">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                <?php 
                if(isset($_POST['search_btn'])){
                $search_box = $_POST['search_box'];
                $select_services = $conn->prepare("SELECT * FROM `services` WHERE approval ='yes' AND (provider_name LIKE '%{$search_box}%' OR service_name LIKE '%{$search_box}%' OR description LIKE '%{$search_box}%' OR category LIKE '%{$search_box}%') ");
                $select_services->execute();  // the code above searches for the words enterd by the user in name, descripton, provider name & category columns of the services table
                if($select_services->rowCount() > 0){
                    while($fetch_services = $select_services->fetch(PDO::FETCH_ASSOC)){

                        $provider_name =  $fetch_services['provider_name'];
                        $select_provider_id = $conn->prepare("SELECT provider_id FROM `service_providers` WHERE provider_name = '$provider_name'");
                        $select_provider_id->execute();
                        $fetch_provider_id = $select_provider_id->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="col">
                    <div class="card shadow-sm">
                        <img src="uploaded_img/<?= $fetch_services['image']; ?>" class="bd-placeholder-img card-img-top" width="100%" height="225" >
                        <div class="card-body">
                            <p class="card-text"><?= $fetch_services['service_name']; ?></p>
                            <p class="card-text"><?= $fetch_services['description']; ?></p>
                            <p class="card-text">Price: LKR <?= $fetch_services['price']; ?>/=</p>
                            <p class="card-text"><a href="view_seller_profile.php?provider_id=<?= $fetch_provider_id['provider_id']; ?>" style="text-decoration: none;"><?= $fetch_services['provider_name']; ?></a></p>
                            <p class="card-text"><?= $fetch_services['category']; ?> / <?= $fetch_services['sub_category']; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <?php if(!isset($buyer_id)){ ?> <!-- checks if user is logged in and blocks from accessing the checkout page-->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="usernotloggedin()">Book Service</button>
                                    <?php }else{ ?>
                                        <form action="book_service.php" method="get">
                                            <input type="hidden" name="buyer_id" value="<?= $fetch_profile['buyer_id']; ?>">
                                            <input type="hidden" name="service_id" value="<?= $fetch_services['service_id']; ?>">
                                            <input type="hidden" name="provider_id" value="<?= $fetch_provider_id['provider_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" name="book">Book Service</a>
                                        </form>
                                    <?php } ?>
                                </div>
                                <small class="text-body-secondary"><?= $fetch_services['created_at']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } }else{ ?>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h4 class="card-text"> No Results Found!</h4>
                            </div> 
                        </div>
                    </div>
                <?php } } ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/common.js"></script>
</body>
</html>