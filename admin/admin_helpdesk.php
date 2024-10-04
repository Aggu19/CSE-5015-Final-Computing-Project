<?php
@include '../db_config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:../index.php');
};

if(isset($_POST['respond'])){ // when admin submits a response to the query
    $query_id = $_POST['query_id'];
    $response = $_POST['adminResponse'];

    $update_response = $conn->prepare("UPDATE `helpdesk_queries` SET response = '$response' WHERE query_id = '$query_id'");
    $update_response->execute();
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Helpdesk | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        
        <div class="container mb-3">
            <div class="row">
                <div class="col-12">
                    
                    <?php $query = $conn->prepare("SELECT * FROM `helpdesk_queries` ORDER BY query_id DESC"); // fetches all queries
                    $query->execute();
                    if($query->rowCount() > 0){
                        while($fetch_queries = $query->fetch(PDO::FETCH_ASSOC)){ ?>
                        
                        <div class="card shadow-sm mt-3">
                            <div class="card-body">
                                <small>User: <?= $fetch_queries['username']; ?> | <?= $fetch_queries['created_at']; ?> | <?= $fetch_queries['query_type']; ?> | <?= $fetch_queries['status']; ?> | 
                                <a style="text-decoration:none;" data-bs-toggle="collapse" href="#<?= $fetch_queries['query_id']; ?>" role="button" aria-expanded="false" aria-controls="collapseExample">Reply</a></small>
                                <h6 class="text mt-2"><?= $fetch_queries['description']; ?></h6>
                            </div>
                        </div>

                        <div class="collapse" id="<?= $fetch_queries['query_id']; ?>">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <p style="color:blue;">Admin Response:</p>
                                    <?php if($fetch_queries['response'] == ''){ // if admin hasn't responded yet, display the response textarea ?>
                                        <form action="" method="POST" class="row g-3">
                                            <div class="col-md-4">
                                                <input type="hidden" name="query_id" value="<?= $fetch_queries['query_id']; ?>">
                                                <input class="form-control" type="text" name="adminResponse" placeholder="Enter your response here..." required>
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn btn-outline-primary" type="submit" name="respond">Send Response</button>
                                            </div>
                                        </form>
                                    <?php }else{ // if admin has responded, display the response ?>
                                        <h6 class="text"><?= $fetch_queries['response']; ?></h6>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    <?php } }else{ // if helpdesk query table is empty, display as below ?>

                        <div class="container mt-5">
                            <div class="row">
                                <div class="col-5">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h4 class="text">No Helpdesk Queries Available</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>

    </main>
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
</body>
</html>