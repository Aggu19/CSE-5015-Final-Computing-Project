<?php
@include 'db_config.php';
session_start();

if(isset($_SESSION['buyer_id'])){
    $buyer_id = $_SESSION['buyer_id'];

    $name_query = $conn->prepare("SELECT * FROM `service_buyers` WHERE buyer_id = '$buyer_id'");
    $name_query->execute();
    $fetch_name = $name_query->fetch(PDO::FETCH_ASSOC);

    $buyername = $fetch_name['buyer_name'];
};

if(isset($_POST['sendQuery'])){
    $msg = $_POST['query_msg'];
    $type = $_POST['query_type'];

    $insert_query = $conn->prepare("INSERT INTO `helpdesk_queries` (username, query_type, description) VALUES(?,?,?)");
    $insert_query->execute([$buyername, $type, $msg]); // inserts into queries table

    $notify = $conn->prepare("INSERT INTO `notifications` (username, message, type) VALUES(?,?,?)");
    $notify->execute([$buyername, 'just sent in a helpdesk query!', 'system']); // inserts into notifcations table .. to notify admin

    header('location:index.php?chat');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<main>
    <?php if(!isset($buyer_id)){ echo"<h3 class='mt-3 ms-2'>Please log in to chat with admin!</h3>";}else{ ?>

        <div class="container mb-3">
            <div class="row">
                <div class="col-12">
                    
                    <?php 
                    $query = $conn->prepare("SELECT * FROM `helpdesk_queries` WHERE username = '$buyername' ORDER BY query_id DESC"); // fetches queires posted by the respective logged in user
                    $query->execute();
                    if($query->rowCount() > 0){
                        while($fetch_queries = $query->fetch(PDO::FETCH_ASSOC)){ ?>
                        
                        <div class="card shadow-sm mt-3">
                            <div class="card-body">
                                <h6 class="text"><?= $fetch_queries['description']; ?></h6>
                                <?php if($fetch_queries['response'] != ''){?>
                                <small><a style="text-decoration:none;" data-bs-toggle="collapse" href="#<?= $fetch_queries['query_id']; ?>" role="button" aria-expanded="false" aria-controls="collapseExample">
                                    View Response</a></small></small> 
                                <?php } ?>
                            </div>
                        </div>

                        <?php if($fetch_queries['response'] != ''){?> <!-- if admin has responded show as below-->

                        <div class="collapse" id="<?= $fetch_queries['query_id']; ?>">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <p style="color:blue;">Admin Response:</p><h6 class="text"><?= $fetch_queries['response']; ?></h6>
                                </div>
                            </div>
                        </div>

                    <?php } } } ?>

                </div>
            </div>
        </div>   

    <?php } ?>
</main>

<!-- Bootstrap JavaScript File -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>