<?php
@include 'db_config.php';
session_start();

$buyer_id = $_SESSION['buyer_id'];

if (!isset($buyer_id)) {
    header('location:index.php');
}

if (isset($_GET['reviewed'])) {
    echo "<script>alert('Your Review was sent successfully!!');</script>";
}

if (isset($_GET['service_booked'])) {
    echo "<script>alert('Service was booked successfully!');</script>";
}

if (isset($_GET['cancel'])) {
    $bookingID = $_GET['cancel'];

    $query = $conn->prepare("UPDATE `service_bookings` SET status = 'canceled' WHERE booking_id = ?");
    $query->execute([$bookingID]);

    $query2 = $conn->prepare("UPDATE `service_bookings` SET payment_status = 'failed' WHERE booking_id = ?");
    $query2->execute([$bookingID]);

    if ($query && $query2) {
        echo "<script>alert('The Booking is successfully cancelled!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="css/rating.css" rel="stylesheet">
</head>
<body>
<main>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <h3>My Bookings</h3>

            <?php
            // Fetch bookings and services
            $show_bookings = $conn->prepare("SELECT * FROM `service_bookings` WHERE buyer_id = '$buyer_id' ORDER BY booking_id DESC");
            $show_bookings->execute();
            if ($show_bookings->rowCount() > 0) {
                while ($fetch_bookings = $show_bookings->fetch(PDO::FETCH_ASSOC)) {

                    $service_id = $fetch_bookings['service_id'];
                    $service_details = $conn->prepare("SELECT * FROM `services` WHERE service_id = '$service_id'");
                    $service_details->execute();
                    $fetch_service = $service_details->fetch(PDO::FETCH_ASSOC);
                    ?>

                    <div class="col-4">
                        <div class="card shadow-sm">
                            <img src="uploaded_img/<?= $fetch_service['image']; ?>" class="bd-placeholder-img card-img-top" width="225" height="225">
                            <div class="card-body">
                                <p class="card-text">Service: <?= $fetch_service['service_name']; ?></p>
                                <p class="card-text">Provider: <?= $fetch_service['provider_name']; ?></p>
                                <p class="card-text">Appointment: <?= $fetch_bookings['appointment_date']; ?></p>
                                <p class="card-text">Price: LKR <?= $fetch_service['price']; ?>/=</p>
                                <p class="card-text"><?= $fetch_service['category']; ?> / <?= $fetch_service['sub_category']; ?></p>
                                <p class="card-text">Payment Method: <?= $fetch_bookings['payment_method']; ?></p>

                                <?php if ($fetch_bookings['service_bundle'] != '') { ?>
                                    <p class="card-text">Added Service Bundle: <?= $fetch_bookings['service_bundle']; ?></p>
                                <?php } ?>

                                <p class="card-text" style="color: <?php if ($fetch_bookings['payment_status'] == 'pending') {
                                    echo 'cadetblue';
                                } elseif ($fetch_bookings['payment_status'] == 'paid') {
                                    echo 'green';
                                } else {
                                    echo 'red';
                                } ?>">Payment Status: <?= $fetch_bookings['payment_status']; ?></p>

                                <p class="card-text" style="color: <?php if ($fetch_bookings['status'] == 'pending') {
                                    echo 'cadetblue';
                                } elseif ($fetch_bookings['status'] == 'approved') {
                                    echo 'blue';
                                } elseif ($fetch_bookings['status'] == 'completed') {
                                    echo 'green';
                                } else {
                                    echo 'red';
                                } ?>">Booking Status: <?= $fetch_bookings['status']; ?></p>

                                <!-- Display delivery option -->
                                <p class="card-text">Delivery Option: <?= $fetch_bookings['delivery_opt']; ?></p>
                                
                        <!-- Display delivery status with color coding -->
                        <p class="card-text" style="color: 
                            <?php 
                                if ($fetch_bookings['delivery_status'] == 'Pending') {
                                    echo 'orange';
                                } elseif (
                                    $fetch_bookings['delivery_status'] == 'Delivered' || 
                                    $fetch_bookings['delivery_status'] == 'Service Delivered'
                                ) {
                                    echo 'green';
                                } elseif (
                                    $fetch_bookings['delivery_status'] == 'Shipped' || 
                                    $fetch_bookings['delivery_status'] == 'Out for Delivery' || 
                                    $fetch_bookings['delivery_status'] == 'Service Scheduled'
                                ) {
                                    echo 'black';
                                } else {
                                    echo 'red';
                                } 
                            ?>">
                            Delivery Option: <?= $fetch_bookings['delivery_status']; ?>
                        </p>

                                <?php if ($fetch_bookings['ratings'] != '') { ?>
                                    <p class="card-text">My Ratings: <?= $fetch_bookings['ratings']; ?>/5</p>
                                <?php } ?>

                                <?php if ($fetch_bookings['reviews'] != '') { ?>
                                    <p class="card-text">My Review: <?= $fetch_bookings['reviews']; ?></p>
                                <?php } ?>

                                <?php if ($fetch_bookings['status'] != 'canceled' && $fetch_bookings['status'] != 'completed') { ?>
                                    <a href="buyer_bookings.php?cancel=<?= $fetch_bookings['booking_id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Do you really want to cancel this booking?')">Cancel Booking</a>
                                <?php }
                                if ($fetch_bookings['status'] == 'completed') { ?>
                                    <a class="btn btn-outline-primary" href="review_bookings.php?review=<?= $fetch_bookings['booking_id']; ?>">Review Booking</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                <?php }
            } else { ?>

            </div>
        </div>

        <div class="container mt-5">
            <div class="row">
                <div class="col-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="text">No Bookings Available</h4>
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
