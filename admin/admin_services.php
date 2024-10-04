<?php
@include '../db_config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:../index.php');
};

if(isset($_GET['approve'])){
  $service_id = $_GET['approve'];
  $approve_query = $conn->prepare("UPDATE `services` SET approval = 'yes' WHERE service_id = ? ");
  $approve_query->execute([$service_id]);
  if($approve_query){
    echo"<script>alert('Service Approved!');</script>";
  }
}

if(isset($_GET['delete'])){
  $service_id = $_GET['delete'];
  $delete_query = $conn->prepare("DELETE FROM `services` WHERE service_id = ? ");
  $delete_query->execute([$service_id]);
  if($delete_query){
    echo"<script>alert('Service Deleted!');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services | LankanServices</title>

    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
</head>
<body>
<main>
<?php include 'header.php'; ?>

<div class="container marketing">
      <div class="container mt-5">

        <h3>Manage Services</h3>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mt-3">

      <?php
        $show_products = $conn->prepare("SELECT * FROM `services` ORDER BY service_id DESC"); // fetches all services
        $show_products->execute();
        if($show_products->rowCount() > 0){
          while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){
            $prov_name = $fetch_products['provider_name']; // fetches the provider name using the provider_id from the services table
            $queryy = $conn->prepare("SELECT provider_id FROM `service_providers` WHERE provider_name = '$prov_name'");
            $queryy->execute();
            $fetch_queryy = $queryy->fetch(PDO::FETCH_ASSOC);
      ?>

            <div class="col">
              <div class="card shadow-sm">
                <img src="../uploaded_img/<?= $fetch_products['image']; ?>" class="bd-placeholder-img card-img-top" width="100%" height="225" >
                <div class="card-body">
                <p class="card-text"><?= $fetch_products['service_name']; ?></p>
                  <p class="card-text"><?= $fetch_products['description']; ?></p>
                  <p class="card-text">Price: LKR <?= $fetch_products['price']; ?>/=</p>
                  <p class="card-text"><?= $fetch_products['category']; ?> / <?= $fetch_products['sub_category']; ?></p>
                  <p class="card-text"><a href="view_seller_profile.php?provider_id=<?= $fetch_queryy['provider_id']; ?>" style="text-decoration: none;"><?= $fetch_products['provider_name']; ?></a></p>
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                        <?php if($fetch_products['approval']== 'no'){ ?><a href="admin_services.php?approve=<?= $fetch_products['service_id']; ?>" onclick="return confirm('Approve this service?');" class="btn btn-sm btn-outline-success">Approve</a><?php } ?>
                        <a href="admin_services.php?delete=<?= $fetch_products['service_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this service?');">Delete</a>
                    </div>
                    <small class="text-body-secondary"><?= $fetch_products['created_at']; ?></small> <small style="color: <?php if($fetch_products['approval'] == 'yes'){ echo"green";} else{ echo"red"; } ?>"> Approved? : <?= $fetch_products['approval']; ?></small>
                  </div>
                </div>
              </div>
            </div>

          <?php 
            } 
            }else{
            ?>
            <div class="col">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h4 class="card-text"> No services available ;(</h4>
                </div> 
              </div>
            </div>
            <?php } ?>

        </div>

      </div>
    </div>

</main>
<?php include 'footer.php'; ?>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>