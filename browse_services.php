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
    <title>Browse Services | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
</head>
<body>
    <main>
      <?php include 'header.php'; ?>

        <div class="container mt-5">
            <form action="search.php" method="post" class="d-flex" role="search">
              <input class="form-control me-2" type="search" placeholder="Search for services..." name="search_box" aria-label="Search" required>
              <button class="btn btn-lg btn-primary" name="search_btn" value="search" type="submit">Search</button>
            </form>
            
            <form action="" method="get">
              <div class="col-md-4 mt-2">
                <select name="category" class="form-select" aria-label="Default select example" required>
                  <option selected disabled value="">Filter by Category</option>
                  <?php
                  $service_category = $conn->prepare("SELECT * FROM `service_categories`");
                  $service_category->execute();
                  while($fetch_category = $service_category->fetch(PDO::FETCH_ASSOC)){
                  ?>
                    <option value="<?= $fetch_category['category_name']; ?>"><?= $fetch_category['category_name']; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col mt-2">
                <button class="btn btn-primary" name="filter" value="true" type="submit">Apply Filter</button>
              </div>
            </form>
        </div>

      <div class="container marketing">
        <div class="container mt-5">
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

            <?php // the codes below fetches all the details of all approved services
            if(isset($_GET['filter'])){
              $category=$_GET['category'];
              $show_products=$conn->prepare("SELECT * FROM `services` WHERE approval = 'yes' AND category = '$category' ORDER BY service_id DESC");
              $show_products->execute();
            }else{
              $show_products = $conn->prepare("SELECT * FROM `services` WHERE approval = 'yes' ORDER BY service_id DESC");
              $show_products->execute();
            }

            if($show_products->rowCount() > 0){
              while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){
                $provider_name =  $fetch_products['provider_name'];
                $select_provider_id = $conn->prepare("SELECT provider_id FROM `service_providers` WHERE provider_name = '$provider_name'");
                $select_provider_id->execute();
                $fetch_provider_id = $select_provider_id->fetch(PDO::FETCH_ASSOC); // fetches the provider_id of the specific services to be passed as GET to the book services page
              
            ?>

            <div class="col">
              <div class="card shadow-sm">
                <img src="uploaded_img/<?= $fetch_products['image']; ?>" class="bd-placeholder-img card-img-top" width="100%" height="225" >
                <div class="card-body">
                  <p class="card-text"><?= $fetch_products['service_name']; ?></p>
                  <p class="card-text"><?= $fetch_products['description']; ?></p>
                  <p class="card-text">Price: LKR <?= $fetch_products['price']; ?>/=</p>
                  <p class="card-text"><a href="view_seller_profile.php?provider_id=<?= $fetch_provider_id['provider_id']; ?>" style="text-decoration: none;"><?= $fetch_products['provider_name']; ?></a></p>
                  <p class="card-text"><?= $fetch_products['category']; ?> / <?= $fetch_products['sub_category']; ?></p>
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                      <?php if(!isset($buyer_id)){ ?> <!-- checks if user is logged in and blocks from accessing the checkout page-->
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
            
            </div>
          </div>
        </div>
        
        <div class="container mt-3">
          <div class="row">
            <div class="col-4">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h4 class="text">No Services Available </h4>
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
    <script src="js/common.js"></script>
</body>
</html>