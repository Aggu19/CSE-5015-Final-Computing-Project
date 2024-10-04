<?php
@include 'db_config.php';
session_start();

$buyer_id = $_SESSION['buyer_id'];

// Redirect to login if buyer is not logged in
if (!isset($buyer_id)) {
    header('location:index.php');
    exit;
}

// Fetch buyer profile
$fetch_profile_stmt = $conn->prepare("SELECT * FROM `service_buyers` WHERE buyer_id = ?");
$fetch_profile_stmt->execute([$buyer_id]);
$fetch_profile = $fetch_profile_stmt->fetch(PDO::FETCH_ASSOC);

if (!$fetch_profile) {
    echo "Buyer profile not found!";
    exit();
}

$selected_service_id = $_GET['service_id'];
$show_service = $conn->prepare("SELECT * FROM `services` WHERE service_id = ?");
$show_service->execute([$selected_service_id]);
$fetch_service = $show_service->fetch(PDO::FETCH_ASSOC);

// Check if there is a promotion
if ($fetch_service['promotion'] !== null && $fetch_service['promotion'] > 0) {
    $promotion_amount = $fetch_service['promotion']; // Promotion value
    $final_price = $fetch_service['price'] - $promotion_amount; // Calculate final price after applying promotion
} else {
    $promotion_amount = 0; // No promotion
    $final_price = $fetch_service['price']; // Original price
}

// In case no promotions are available
if ($promotion_amount == 0) {
    $noPromotionMessage = "No Promotions or Ads available for this service.";
} else {
    $noPromotionMessage = ""; // Reset message
}

if (isset($_POST['book_service'])) { // If booking form is filled and button is pressed
    $b_id = $buyer_id;
    $s_id = $_GET['service_id'];
    $p_id = $_GET['provider_id'];
    $address = $_POST['address1'] . ' ' . $_POST['address2'];
    $app_date = $_POST['dateTime'];
    $payMethod = $_POST['paymentMethod'];
    $deliveryOption = $_POST['deliveryOption']; // Store the delivery option

    if (isset($_GET['bundle'])) { // If the user selects a bundle
        $bundle_service_id = $_GET['bundle'];
        $query_bundle = $conn->prepare("SELECT * FROM `services` WHERE service_id = ?");
        $query_bundle->execute([$bundle_service_id]);
        $fetch_bundle_service = $query_bundle->fetch(PDO::FETCH_ASSOC);

        $bundle_service_name = $fetch_bundle_service['service_name'] . ' By ' . $fetch_bundle_service['provider_name'] . ' | ' . $fetch_bundle_service['price'] . '/=';

        $bookService = $conn->prepare("INSERT INTO `service_bookings` (buyer_id, service_id, provider_id, address, appointment_date, payment_method, service_bundle, delivery_opt, final_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $bookService->execute([$b_id, $s_id, $p_id, $address, $app_date, $payMethod, $bundle_service_name, $deliveryOption, $final_price]); // Insert a new record in the bookings table with delivery option and final price
    } else {
        $bookService = $conn->prepare("INSERT INTO `service_bookings` (buyer_id, service_id, provider_id, address, appointment_date, payment_method, delivery_opt, final_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $bookService->execute([$b_id, $s_id, $p_id, $address, $app_date, $payMethod, $deliveryOption, $final_price]); // Insert a new record in the bookings table with delivery option and final price
    }

    // Insert notification
    $username = $fetch_profile['buyer_name'];
    $notification_message = "just placed a new booking!";
    $notification_type = "system";
    $notification = $conn->prepare("INSERT INTO `notifications` (username, message, type) VALUES (?, ?, ?)");
    $notification->execute([$username, $notification_message, $notification_type]); // Insert a new record in notifications table to notify the admin

    header('location:buyer_bookings.php?service_booked');
    exit();
} elseif (isset($_GET['bundle'])) {
    echo "<script>alert('Service added to bundle!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Services | LankanServices</title>
    
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <main>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <div class="row">
          <h3>Book services from <?= htmlspecialchars($fetch_service['provider_name']); ?></h3>
            <div class="col">
              <div class="card shadow-sm">
                <img src="uploaded_img/<?= htmlspecialchars($fetch_service['image']); ?>" class="bd-placeholder-img card-img-top" width="100%" height="225">
                <div class="card-body">
                  <p class="card-text"><?= htmlspecialchars($fetch_service['service_name']); ?></p>
                  <p class="card-text"><?= htmlspecialchars($fetch_service['description']); ?></p>
                  <p class="card-text">Price: LKR <?= htmlspecialchars($fetch_service['price']); ?>/=</p>
                  <?php if ($promotion_amount > 0): ?>
                      <p class="card-text">Promotion: <?= htmlspecialchars($promotion_amount); ?>% off</p>
                      <p class="card-text">Final Price: LKR <?= htmlspecialchars($final_price); ?>/=</p>
                  <?php else: ?>
                      <p class="card-text"><?= htmlspecialchars($noPromotionMessage); ?></p>
                  <?php endif; ?>
                  <p class="card-text"><?= htmlspecialchars($fetch_service['category']); ?> / <?= htmlspecialchars($fetch_service['sub_category']); ?></p>
                  <p class="card-text">Ratings & Reviews by other buyers:</p>
                  <ul>
                    <?php
                    $ratingsReviews = $conn->prepare("SELECT * FROM `service_bookings` WHERE service_id = ?");
                    $ratingsReviews->execute([$selected_service_id]);
                    if ($ratingsReviews->rowCount() > 0) {
                      while ($fetch_ratingsReviews = $ratingsReviews->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <li><?php if (empty($fetch_ratingsReviews['ratings']) || empty($fetch_ratingsReviews['reviews'])) { ?> 
                          <p class="card-text">No ratings/reviews available.</p> 
                          <?php break; } else { ?>
                          <p class="card-text"><?= htmlspecialchars($fetch_ratingsReviews['ratings']); ?>/5 | <?= htmlspecialchars($fetch_ratingsReviews['reviews']); ?></p><?php } ?>
                        </li>
                        <?php } } ?>
                  </ul>
                </div>
              </div>
            </div>

            <div class="col-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                      <form action="" method="POST" class="row g-3">
                        <div class="col-md-12">
                          <label for="inputName" class="form-label">Full Name</label>
                          <input type="text" class="form-control" id="name" value="<?= htmlspecialchars($fetch_profile['buyer_name']); ?>" required>
                        </div>

                        <div class="col-12">
                          <label for="inputAddress" class="form-label">Address</label>
                          <input type="text" class="form-control" id="inputAddress" name="address1" placeholder="1234 Main St" required>
                        </div>
                        <div class="col-12">
                          <label for="inputAddress2" class="form-label">Address 2</label>
                          <input type="text" class="form-control" id="inputAddress2" name="address2" placeholder="Apartment, studio, or floor" required>
                        </div>
                        <div class="col-md-6">
                          <label for="inputDateTime" class="form-label">Choose your appointment Date & Time</label>
                          <input type="datetime-local" class="form-control" id="inputDateTime" name="dateTime" required>
                        </div>
                        <div class="col-md-6">
                        <label for="paymentMethod" class="form-label">Payment Method</label>
                        <select class="form-select" aria-label="Default select example" name="paymentMethod" required>
                          <option value="Debit/Credit Card">Debit/Credit Card</option>
                          <option value="Cash on Service">Cash on Service</option>
                          <option value="PayPal">PayPal</option>
                        </select>
                        </div>
                        <div class="col-md-6">
                        <label for="deliveryOption" class="form-label">Delivery Option</label>
                        <select class="form-select" aria-label="Default select example" name="deliveryOption" required>
                          <option value="Door step ">Door step </option>
                          <option value="Uber">Uber</option>
                          <option value="PickMe">PickMe </option>
                        </select>
                        </div>
                        <div class="col-12">
                          <button type="submit" class="btn btn-primary" name="book_service">Book Now</button>
                        </div>
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
