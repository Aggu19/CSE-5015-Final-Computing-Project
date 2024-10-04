<?php
@include 'db_config.php';
session_start();

$buyer_id = $_SESSION['buyer_id'];

if(!isset($buyer_id)){
  header('location:index.php');
};

if(isset($_GET['review'])){
    $bookingId = $_GET['review'];
    $query=$conn->prepare("SELECT * FROM `service_bookings` WHERE booking_id = '$bookingId'");
    $query->execute();
    $fetch_booking = $query->fetch(PDO::FETCH_ASSOC);

    $service_id = $fetch_booking['service_id'];
    $query2=$conn->prepare("SELECT * FROM `services` WHERE service_id = '$service_id'");
    $query2->execute();
    $fetch_service = $query2->fetch(PDO::FETCH_ASSOC);
};

if(isset($_POST['rate_review'])){
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $bookingId = $_POST['bookingid'];

    $query=$conn->prepare("UPDATE `service_bookings` SET ratings = '$rating', reviews = '$review' WHERE booking_id = '$bookingId'");
    $query->execute();

    if($query){
        header('location:buyer_bookings.php?reviewed');
    }
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Booking | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="css/rating.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
        <main>
            <div class="container mt-5">
                <div class="row">

                    <h4>Rate / Review Your Booking:</h4>
                    <div class="col-4">
                        <div class="card shadow-sm">
                            <img src="uploaded_img/<?= $fetch_service['image']; ?>" class="bd-placeholder-img card-img-top" width="225" height="225" >
                            <div class="card-body">
                                <p class="card-text">Service: <?= $fetch_service['service_name']; ?></p>
                                <p class="card-text">Provider: <?= $fetch_service['provider_name']; ?></p>
                                <p class="card-text">Appointment: <?= $fetch_booking['appointment_date']; ?></p>
                                <p class="card-text">Price: LKR <?= $fetch_service['price']; ?>/=</p>
                                <p class="card-text"><?= $fetch_service['category']; ?> / <?= $fetch_service['sub_category']; ?></p>
                                <p class="card-text">Payment Method: <?= $fetch_booking['payment_method']; ?></p>
                                <?php if($fetch_booking['service_bundle'] != ''){ ?><p class="card-text">Added Service Bundle: <?= $fetch_booking['service_bundle']; ?></p><?php } ?>
                                <p class="card-text" style="color:green">Payment Status: <?= $fetch_booking['payment_status']; ?></p>
                                <p class="card-text" style="color:green">Booking Status: <?= $fetch_booking['status']; ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if($fetch_booking['ratings'] == '' && $fetch_booking['reviews'] == ''){?>

                        <div class="col-6">
                            <div class="feedback-form">
                                <h2>Rating / Review Form</h2>
                                <form action="" method="post" id="feedbackForm">
                                    <div class="rating">
                                        <input type="radio" id="star5" name="rating" value="5">
                                        <label for="star5">&#9733;</label>
                                        <input type="radio" id="star4" name="rating" value="4">
                                        <label for="star4">&#9733;</label>
                                        <input type="radio" id="star3" name="rating" value="3">
                                        <label for="star3">&#9733;</label>
                                        <input type="radio" id="star2" name="rating" value="2">
                                        <label for="star2">&#9733;</label>
                                        <input type="radio" id="star1" name="rating" value="1">
                                        <label for="star1">&#9733;</label>
                                    </div>
                                    <div class="comment">
                                        <label for="review">Your Review:</label><br>
                                        <textarea id="review" name="review" required></textarea>
                                    </div>
                                    <input type="hidden" name="bookingid" value="<?= $fetch_booking['booking_id']; ?>">
                                    <button type="submit" class="submit-btn" name="rate_review">Submit</button>
                                </form>
                            </div>
                        </div>

                    <?php }else{ ?>
                        <div class="col-6">
                            <div class="card shadow-sm">
                                <h3 class="mt-3 ms-3">You've already reviewed this booking!</h3>
                                <h5 class="mt-1 ms-3">Your rating: <?= $fetch_booking['ratings']; ?>/5</h5>
                                <h5 class="ms-3">Your review: <?= $fetch_booking['reviews']; ?></h5>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </main>
    <?php include 'footer.php'; ?>
  <!-- Bootstrap JavaScript File -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>