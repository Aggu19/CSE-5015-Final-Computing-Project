<?php
@include '../db_config.php';
session_start();

$provider_id = $_SESSION['provider_id'];

if(!isset($provider_id)){
    header('location:../index.php');
}

if(isset($_POST['updateStatus'])){
  $status = $_POST['status'];
  $bookingID = $_POST['booking_id'];
  $updateStatus = $conn->prepare("UPDATE `service_bookings` SET status = ? WHERE booking_id = ?");
  $updateStatus->execute([$status, $bookingID]);
  if($updateStatus){
    echo "<script>alert('Booking Status Updated!');</script>";
  }
}

if(isset($_POST['updateDelivery'])){
  $deliveryOption = $_POST['deliveryOption'];
  $bookingID = $_POST['booking_id'];
  $updateDelivery = $conn->prepare("UPDATE `service_bookings` SET delivery_status = ? WHERE booking_id = ?");
  $updateDelivery->execute([$deliveryOption, $bookingID]);
  if($updateDelivery){
    echo "<script>alert('Delivery Option Updated!');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage My Bookings | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<main>
  <?php include 'header.php';?>

  <div class="container mt-5">
      <div class="row">
        <h3>Manage Bookings</h3>
          
          <?php 
          $show_bookings = $conn->prepare("SELECT * FROM `service_bookings` WHERE provider_id = ? ORDER BY booking_id DESC");
          $show_bookings->execute([$provider_id]);
          if($show_bookings->rowCount() > 0){
            while($fetch_bookings = $show_bookings->fetch(PDO::FETCH_ASSOC)){
              $service_id = $fetch_bookings['service_id'];
              $service_details = $conn->prepare("SELECT * FROM `services` WHERE service_id = ?");
              $service_details->execute([$service_id]);
              $fetch_service = $service_details->fetch(PDO::FETCH_ASSOC);

              $buyer_id = $fetch_bookings['buyer_id'];
              $buyerr = $conn->prepare("SELECT * FROM `service_buyers` WHERE buyer_id = ?");
              $buyerr->execute([$buyer_id]);
              $fetch_buyerr = $buyerr->fetch(PDO::FETCH_ASSOC);
          ?>

      <div class="col-4">
        <div class="card shadow-sm">
          <img src="../uploaded_img/<?= htmlspecialchars($fetch_service['image']); ?>" class="bd-placeholder-img card-img-top" width="225" height="225">
          <div class="card-body">
            <p class="card-text">Service: <?= htmlspecialchars($fetch_service['service_name']); ?></p>
            <p class="card-text">Buyer: <?= htmlspecialchars($fetch_buyerr['buyer_name']); ?></p>
            <p class="card-text">Appointment: <?= htmlspecialchars($fetch_bookings['appointment_date']); ?></p>
            <?php if($fetch_bookings['service_bundle'] != ''){ ?><p class="card-text">Added Service Bundle: <?= htmlspecialchars($fetch_bookings['service_bundle']); ?></p><?php } ?>
            <p class="card-text">Price: LKR <?= htmlspecialchars($fetch_service['price']); ?>/=</p>
            <p class="card-text">Payment Method: <?= htmlspecialchars($fetch_bookings['payment_method']); ?></p>
            <p class="card-text">Payment Status: <?= htmlspecialchars($fetch_bookings['payment_status']); ?></p>
            <p class="card-text">Booking Status: <?= htmlspecialchars($fetch_bookings['status']); ?></p>

            <!-- Display the delivery option -->
            <p class="card-text">Delivery Option: 
                <span style="color: <?= ($fetch_bookings['delivery_opt'] ); ?>">
                    <?= ($fetch_bookings['delivery_opt']); ?>
                </span>
            </p>

         <!-- Display the delivery status -->
            <p class="card-text">Delivery Status: 
                <span style="color: <?= (isset($fetch_bookings['delivery_status']) && $fetch_bookings['delivery_status'] == 'Pending') ? 'red' : 'green'; ?>">
                    <?= isset($fetch_bookings['delivery_status']) ? htmlspecialchars($fetch_bookings['delivery_status']) : 'Not Specified'; ?>
                </span>
            </p>

            <?php if($fetch_bookings['status'] == "pending" || $fetch_bookings['status'] == "approved"){ ?>
              <!-- Form to update booking status -->
              <form action="" method="POST">
                <input type="hidden" name="booking_id" value="<?= $fetch_bookings['booking_id']; ?>">
                <select name="status" class="form-select" aria-label="Default select example" required>
                  <option value="<?= htmlspecialchars($fetch_bookings['status']); ?>" selected disabled><?= htmlspecialchars($fetch_bookings['status']); ?></option>
                  <?php if($fetch_bookings['status'] == "pending"){?>
                  <option value="approved">Approve</option><?php  }?>
                  <option value="completed">Complete</option>
                  <option value="canceled">Cancel</option>
                </select>
                <button type="submit" class="btn btn-outline-success mt-3" name="updateStatus" onclick="return confirm('Do you want to update the status of this booking?')">Update Status</button>
              </form>

              <?php if($fetch_bookings['status'] == "pending" || $fetch_bookings['delivery_status'] == "pending"){ ?>
              <!-- Form to update delivery status -->
              <form action="" method="POST" class="mt-3">
                <input type="hidden" name="booking_id" value="<?= $fetch_bookings['booking_id']; ?>">
                <select name="deliveryOption" class="form-select" aria-label="Delivery Option" required>
                  <!-- Display the delivery status or fallback to 'Pending' if none exists -->
                  <option value="<?= isset($fetch_bookings['delivery_status']) ? $fetch_bookings['delivery_status'] : 'Pending'; ?>" selected disabled>
                      <?= isset($fetch_bookings['delivery_status']) ? $fetch_bookings['delivery_status'] : 'Pending'; ?>
                  </option>
                  <?php if($fetch_bookings['delivery_status'] == "pending"){?>
                  <option value="Shipped">Shipped</option><?php  }?>
                  <option value="Out for Delivery">Out for Delivery</option>
                  <option value="Delivered">Delivered</option>
                  <option value="Delivery Failed">Delivery Failed</option>
                  <option value="Canceled">Canceled</option>
                  <option value="Service Scheduled">Service Scheduled</option>
                  <option value="Service Delivered">Service Delivered</option>
                  <option value="Service Delayed">Service Delayed</option>
                  <option value="Pending">Pending</option>
                </select>
                <button type="submit" class="btn btn-outline-primary mt-3" name="updateDelivery" onclick="return confirm('Do you want to update the delivery status?')">Update Delivery</button>
              </form>

            <?php } ?>
            <?php } ?>

          </div>
        </div>
      </div>

      <?php } } else { ?>

          <div class="col-4">
            <div class="card shadow-sm">
              <div class="card-body">
                <h4 class="text">No Bookings Available ;(</h4>
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
