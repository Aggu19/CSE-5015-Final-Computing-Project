<?php
@include '../db_config.php';
session_start();

$provider_id = $_SESSION['provider_id'];

$name_query = $conn->prepare("SELECT * FROM `service_providers` WHERE provider_id = '$provider_id'");
$name_query->execute();
$fetch_name = $name_query->fetch(PDO::FETCH_ASSOC);

$providername = $fetch_name['provider_name'];

if(!isset($provider_id)){
    header('location:../index.php');
};

if(isset($_POST['sendQuery'])){
    $msg = $_POST['query_msg'];
    $type = $_POST['query_type'];

    $insert_query = $conn->prepare("INSERT INTO `helpdesk_queries` (username, query_type, description) VALUES(?,?,?)");
    $insert_query->execute([$providername, $type, $msg]);

    $notify = $conn->prepare("INSERT INTO `notifications` (username, message, type) VALUES(?,?,?)");
    $notify->execute([$providername, 'just sent in a helpdesk query!', 'system']);

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
    <div class="container mb-3">
        <div class="row">
            <div class="col-12">
                <?php 
                $query = $conn->prepare("SELECT * FROM `helpdesk_queries` WHERE username = '$providername' ORDER BY query_id DESC");
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

                    <?php if($fetch_queries['response'] != ''){?>

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
</main>

<!-- Bootstrap JavaScript File -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>