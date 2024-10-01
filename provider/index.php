<?php
@include '../db_config.php';
session_start();

$provider_id = $_SESSION['provider_id'];

if(!isset($provider_id)){
    header('location:../index.php');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="../css/chat.css" rel="stylesheet"> <!-- CSS for chat -->
</head>
<body>
<main>
    <?php include 'header.php'; ?>

    <br><br>
    <div class="container marketing">

      <div class="container">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

          <?php
          $provider = $fetch_profile['provider_name'];
          $provider_id = $fetch_profile['provider_id'];
          
          $select_products = $conn->prepare("SELECT * FROM `services` WHERE provider_name = '$provider'");
          $select_products->execute();
          $number_of_products = $select_products->rowCount();

          $select_bookings = $conn->prepare("SELECT * FROM `service_bookings` WHERE provider_id = '$provider_id' ");
          $select_bookings->execute();
          $number_of_bookings = $select_bookings->rowCount();
          
          $bookingsCompleted = $conn->prepare("SELECT * FROM `service_bookings` WHERE provider_id = '$provider_id' AND status = 'completed'");
          $bookingsCompleted->execute();
          $number_of_bookingsComp = $bookingsCompleted->rowCount();
          ?>
          
          <div class="col">
            <div class="card shadow-sm">
              <div class="card-body">
                <h3 class="card-text"><?= $number_of_products; ?> Total Services</h3>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="btn-group">
                    <a href="provider_services.php" type="button" class="btn btn-sm btn-outline-secondary">Manage Services</a>
                  </div>
                </div>
              </div> 
            </div>
          </div>
          
          <div class="col">
            <div class="card shadow-sm">
              <div class="card-body">
                <h3 class="card-text"><?= $number_of_bookings; ?> Total Orders</h3>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="btn-group">
                    <a href="provider_bookings.php" type="button" class="btn btn-sm btn-outline-secondary">Manage Bookings</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col">
            <div class="card shadow-sm">
              <div class="card-body">
                <h3 class="card-text"><?= $number_of_bookingsComp; ?> Orders Completed</h3>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="btn-group">
                    <a href="provider_bookings.php" type="button" class="btn btn-sm btn-outline-secondary">Manage Bookings</a>
                  </div>
                </div>
              </div> 
            </div>
          </div>
        
        </div>
      </div>
      
      <hr class="featurette-divider">
      
      <div class="container mt-5">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
          <?php
          $provider_name = $fetch_profile['provider_name'];
          $show_products = $conn->prepare("SELECT * FROM `services` WHERE provider_name = '$provider_name' ORDER BY service_id DESC");
          $show_products->execute();
          if($show_products->rowCount() > 0){
            while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
          ?>
          
          <div class="col">
            <div class="card shadow-sm">
              <img src="../uploaded_img/<?= $fetch_products['image']; ?>" class="bd-placeholder-img card-img-top" width="100%" height="225" >
              <div class="card-body">
                <p class="card-text"><?= $fetch_products['service_name']; ?></p>
                <p class="card-text"><?= $fetch_products['description']; ?></p>
                <p class="card-text">Price: LKR <?= $fetch_products['price']; ?>/=</p>
                <p class="card-text"><?= $fetch_products['category']; ?> / <?= $fetch_products['sub_category']; ?></p>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="btn-group">
                    <a href="edit_service.php?edit=<?= $fetch_products['service_id']; ?>" type="button" class="btn btn-sm btn-outline-secondary">Edit Service</a>
                  </div>
                  <small class="text-body-secondary"><?= $fetch_products['created_at']; ?> </small><small style="color:<?php if($fetch_products['approval'] == 'no'){ echo"red"; }else{ echo"green"; } ?>">Aprroved:  <?= $fetch_products['approval']; ?></small>
                </div>
              </div>
            </div>
          </div>
          <?php } }?>
        </div>
      </div>
    </div><!-- /.container -->

        <!-- Chat Icon -->
        <div class="chat-icon" onclick="toggleChatBox()">
            <img src="../images/chat.png" alt="Chat with us" title="Send admin a query" style="width: 40px; height: 40px;">
        </div>
        
        <!-- Chat Box -->
        <div  class="chat-box" id="chatBox" <?php if(!isset($_GET['chat'])){ ?> style="display: none;" <?php }?>>
            <div class="chat-header">
                <span>Chat with Admin</span>
                <button class="close-chat" onclick="toggleChatBox()" title="Close Chat Box">×</button>
            </div>
            <div class="chat-body">
                <iframe id="chatFrame" src="chat.php" frameborder="0" style="width: 100%; height: 100%;"></iframe>
            </div>
            <div class="chat-footer">
                <form action="chat.php" method="POST">
                    <input type="text" name="query_msg" placeholder="Type your query here..." required />
                    <select name="query_type" required>
                        <option value="technical_issue">Technical Issue</option>
                        <option value="account_issue">Account Issue</option>
                        <option value="general_inquiry">General Inquiry</option>
                    </select>
                    <button type="submit" title="Send admin a query" name="sendQuery">Send</button>
                </form>
            </div>
        </div>

</main>

    <?php include 'footer.php'; ?>
    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/common.js"></script>

</body>
</html>