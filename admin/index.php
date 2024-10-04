<?php
@include '../db_config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:../index.php');
};

if(isset($_POST['updatefeaturedProviders'])){ // if update featured providers is clicked
  $deleteQuery = $conn->prepare("TRUNCATE `lankanservices`.`featured_providers`"); // empty the featured providers table
  $deleteQuery->execute();

  $providers = $_POST['featuredProviders']; // stores all 6 selected providers as array

  foreach($providers as $featuredProviders){ // converts the array into strings and inserts the featured providers names one by one
    $query = $conn->prepare("INSERT INTO `featured_providers` (provider_name) VALUES ('$featuredProviders')");
    $query->execute();
  }
  if($query){
    echo "<script>alert('Featured Providers Updated Successfully');</script>";
  }
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | LankanServices</title>

    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="../css/adminnotifications.css" rel="stylesheet">
    
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <div class="container mb-5 mt-5">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
      
      <?php
      $select_services = $conn->prepare("SELECT * FROM `services`");
      $select_services->execute();
      $number_of_services = $select_services->rowCount(); // gets the number of services available

      $select_bookings = $conn->prepare("SELECT * FROM `service_bookings`");
      $select_bookings->execute();
      $number_of_bookings = $select_bookings->rowCount(); // gets the number of bookings made

      $select_providers = $conn->prepare("SELECT * FROM `service_providers`");
      $select_providers->execute();
      $no_of_providers = $select_providers->rowCount(); // gets the number of service providers

      $select_buyers = $conn->prepare("SELECT * FROM `service_buyers`");
      $select_buyers->execute();
      $no_of_buyers = $select_buyers->rowCount(); // gets the number of service buyers

      $no_of_users = $no_of_providers + $no_of_buyers // add the number of buyers and providers = users
      ?>
      
      <div class="col">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-text"><?= $number_of_services; ?> Total Services</h3>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <a href="admin_services.php" type="button" class="btn btn-sm btn-outline-secondary">Manage Services</a>
              </div>
            </div>
          </div> 
        </div>
      </div>
      
      <div class="col">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-text"><?= $number_of_bookings; ?> Total Bookings</h3>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <a href="admin_bookings.php" type="button" class="btn btn-sm btn-outline-secondary">Manage Bookings</a>
              </div>
            </div>
          </div> 
        </div>
      </div>
      
      <div class="col">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-text"><?= $no_of_users; ?> Total Users</h3>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <a href="admin_users.php" type="button" class="btn btn-sm btn-outline-secondary">Manage Users</a>
              </div>
            </div>
          </div> 
        </div>
      </div>
    
    </div>
  </div>
  
  <div class="d-flex align-items-center p-3 my-3 text-white bg-purple rounded shadow-sm">
    <img class="me-3" src="../images/bell-fill.svg" alt="" width="48" height="38">
    <div class="lh-1">
      <h1 class="h6 mb-0 text-white lh-1">Notifications</h1>
      <small>Latest Website Activities</small>
    </div>
  </div>
  
  <div class="my-3 p-3 bg-body rounded shadow-sm">
    <h6 class="border-bottom pb-2 mb-0">Recent updates</h6>
    
    <?php 
    $select_notifications = $conn->prepare("SELECT * FROM `notifications` ORDER BY notification_id DESC LIMIT 5"); // fetches the last 5 notifications
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
        No recent notifications available ;(
      </p>
    </div>

    <?php } ?>
    
    <small class="d-block text-end mt-3">
      <a href="all_notifications.php">All updates</a>
    </small>
  </div>
  
  <div class="my-3 p-3 bg-body rounded shadow-sm">
    <h6 class="border-bottom pb-2 mb-0">Update Featured Providers (Select 6 Providers to feature on the homepage)</h6>

    <div class="form-check form-check-inline mt-3">
      <form action="" method="POST" onsubmit="return min6providers()">

        <?php // feteches all provider profiles
        $select_providers = $conn->prepare("SELECT * FROM `service_providers` ORDER BY provider_id DESC"); 
        $select_providers->execute();
        if($select_providers->rowCount() > 0){
          while($fetch_providers = $select_providers->fetch(PDO::FETCH_ASSOC)){
        ?>
        
        <input class="check" type="checkbox" name="featuredProviders[]" id="<?= $fetch_providers['provider_id']; ?>" value="<?= $fetch_providers['provider_name']; ?>">
        <label class="form-check-label me-3" for="<?= $fetch_providers['provider_id']; ?>"><?= $fetch_providers['provider_name']; ?></label>
        
        <?php } } ?>
        <br>
        <button type="submit" class="btn btn-outline-primary mt-2" name="updatefeaturedProviders" id="checkBtn">Update</button>
      </form>
    </div>
  </div>
  
  <div class="my-3 p-3 bg-body rounded shadow-sm">
    <h6 class="border-bottom pb-2 mb-0">Manage User Accounts</h6>
    
    <?php // feteches the profiles of 3 latest providers and display their info
    $select_providers = $conn->prepare("SELECT * FROM `service_providers` ORDER BY provider_id DESC LIMIT 3"); 
    $select_providers->execute();
    if($select_providers->rowCount() > 0){
      while($fetch_providers = $select_providers->fetch(PDO::FETCH_ASSOC)){
    ?>
    
    <div class="d-flex text-body-secondary pt-3">
      <img src="../uploaded_img/<?= $fetch_providers['profile_picture']; ?>" class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32">
      <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
        <div class="d-flex justify-content-between">
          <strong class="text-gray-dark"><?= $fetch_providers['provider_name']; ?></strong>
          <a href="view_seller_profile.php?provider_id=<?= $fetch_providers['provider_id']; ?>">View Profile</a>
        </div>
        <span class="d-block">Service Provider</span>
      </div>
    </div>
    
    <?php } }
    
    // feteches the profiles of 3 latest buyers and display their info
    $select_buyers = $conn->prepare("SELECT * FROM `service_buyers` ORDER BY buyer_id DESC LIMIT 3");
    $select_buyers->execute();
    if($select_buyers->rowCount() > 0){
      while($fetch_buyers = $select_buyers->fetch(PDO::FETCH_ASSOC)){
    ?>
    
    <div class="d-flex text-body-secondary pt-3">
      <img src="../uploaded_img/<?= $fetch_buyers['profile_picture']; ?>" class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32">
      <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
        <div class="d-flex justify-content-between">
          <strong class="text-gray-dark"><?= $fetch_buyers['buyer_name']; ?></strong>
          <a href="view_buyer_profile.php?buyer_id=<?= $fetch_buyers['buyer_id']; ?>">View Profile</a>
        </div>
        <span class="d-block">Service Buyer</span>
      </div>
    </div>
    
    <?php } } ?>
    
    <small class="d-block text-end mt-3">
      <a href="admin_users.php">All Accounts</a>
    </small>
  </div>
  
  <div class="container marketing mt-5">
    <div class="container mt-5">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        
      <?php
      $show_products = $conn->prepare("SELECT * FROM `services` ORDER BY service_id DESC LIMIT 6"); // fetches details of 6 latest services
      $show_products->execute();
      if($show_products->rowCount() > 0){
        while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){
          $prov_name = $fetch_products['provider_name'];
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
                <a href="admin_services.php" type="button" class="btn btn-sm btn-outline-secondary">Manage Service</a>
              </div>
              <small class="text-body-secondary"><?= $fetch_products['created_at']; ?></small> <small style="color: <?php if($fetch_products['approval'] == 'yes'){ echo"green";} else{ echo"red"; } ?>"> Approved? : <?= $fetch_products['approval']; ?></small>
            </div>
          </div>
        </div>
      </div>
      
      <?php } }?>

      </div>
    </div>
  </div>
</main>
<?php include 'footer.php'; ?>

<script> // js script to limit admin to select only 6 featured providers to be displayed on homepage
var checks = document.querySelectorAll(".check");
var max = 6;
for (var i = 0; i < checks.length; i++)
  checks[i].onclick = selectiveCheck;
function selectiveCheck (event) {
  var checkedChecks = document.querySelectorAll(".check:checked");
  if (checkedChecks.length >= max + 1)
    return false;
}
// this function prevents the admin from choosing less than 6 providers
function min6providers(){
  var checkedChecks = document.querySelectorAll(".check:checked");
  if (checkedChecks.length < max){
    alert("Please choose atleast 6 providers!");
    return false;
  }
}
</script>

  <!-- Bootstrap JavaScript File -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>