<?php
@include '../db_config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings | LankanServices</title>

    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <main>
        <?php include 'header.php'; ?>

        <div class="container mt-5">
            <div class="row">
                <h3>All Bookings</h3>

                <?php
                // Fetch all service bookings
                $show_bookings = $conn->prepare("SELECT * FROM `service_bookings` ORDER BY booking_id DESC");
                $show_bookings->execute();
                if ($show_bookings->rowCount() > 0) {
                    while ($fetch_bookings = $show_bookings->fetch(PDO::FETCH_ASSOC)) {

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
                            <div class="card shadow-sm mt-2">
                                <img src="../uploaded_img/<?= htmlspecialchars($fetch_service['image']); ?>" class="bd-placeholder-img card-img-top" width="225" height="225">
                                <div class="card-body">
                                    <p class="card-text">Service: <?= htmlspecialchars($fetch_service['service_name']); ?></p>
                                    <?php if ($fetch_bookings['service_bundle'] != '') { ?>
                                        <p class="card-text">Added Service Bundle: <?= htmlspecialchars($fetch_bookings['service_bundle']); ?></p>
                                    <?php } ?>
                                    <p class="card-text">Buyer: <?= htmlspecialchars($fetch_buyerr['buyer_name']); ?></p>
                                    <p class="card-text">Provider: <?= htmlspecialchars($fetch_service['provider_name']); ?></p>
                                    <p class="card-text">Appointment: <?= htmlspecialchars($fetch_bookings['appointment_date']); ?></p>
                                    <p class="card-text">Price: LKR <?= htmlspecialchars($fetch_service['price']); ?>/=</p>
                                    <p class="card-text"><?= htmlspecialchars($fetch_service['category']); ?> / <?= htmlspecialchars($fetch_service['sub_category']); ?></p>
                                    <p class="card-text">Payment Method: <?= htmlspecialchars($fetch_bookings['payment_method']); ?></p>

                                    <!-- Display the delivery option -->
                                    <p class="card-text">Delivery Option: <?= htmlspecialchars($fetch_bookings['delivery_opt']) ?: 'Not Specified'; ?></p>

                                    <!-- Display the delivery status -->
                                    <p class="card-text">Delivery Status: <?= !empty($fetch_bookings['delivery_status']) ? htmlspecialchars($fetch_bookings['delivery_status']) : 'Not Specified'; ?></p>

                                    <p class="card-text" style="color: <?= ($fetch_bookings['payment_status'] == 'pending') ? 'cadetblue' : ($fetch_bookings['payment_status'] == 'paid' ? 'green' : 'red'); ?>">Payment Status: <?= htmlspecialchars($fetch_bookings['payment_status']); ?></p>
                                    <p class="card-text" style="color: <?= ($fetch_bookings['status'] == 'pending') ? 'cadetblue' : ($fetch_bookings['status'] == 'approved' ? 'blue' : ($fetch_bookings['status'] == 'completed' ? 'green' : 'red')); ?>">Booking Status: <?= htmlspecialchars($fetch_bookings['status']); ?></p>
                                </div>
                            </div>
                        </div>

                <?php
                    }
                } else { ?>
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
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
