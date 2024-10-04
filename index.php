<?php
@include 'db_config.php';
session_start();

if(isset($_SESSION['buyer_id'])){
    $buyer_id = $_SESSION['buyer_id'];
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link href="css/carousel.css" rel="stylesheet"> <!-- CSS for carousel -->
    <link href="css/chat.css" rel="stylesheet"> <!-- CSS for chat -->
</head>
<body>
    <main>
        <?php include 'header.php'; ?>
        
        <div id="myCarousel" class="carousel slide mb-2" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img  src="images/image2.jpg" width="100%" height="100%">
                    <div class="container">
                        <div class="carousel-caption text-start">
                            <h1>LankanServices.</h1>
                            <h3 class="opacity-88">Discover the Best Local Services</h3>
                            <p class="opacity-75">Find top-rated professionals and get the job done right, every time.</p>
                            <form action="search.php" method="post" class="d-flex" role="search">
                                <input class="form-control me-2" type="search" placeholder="Search for services..." name="search_box" aria-label="Search" required>
                                <button class="btn btn-lg btn-primary" name="search_btn" value="search" type="submit">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br><br>
        <div class="container marketing">
            <div class="container">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                    <?php
                    $show_products = $conn->prepare("SELECT * FROM `services` WHERE approval = 'yes' ORDER BY service_id DESC LIMIT 9");
                    $show_products->execute();
                    if($show_products->rowCount() > 0){
                        while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){
                            $provider_name =  $fetch_products['provider_name'];
                            $select_provider_id = $conn->prepare("SELECT provider_id FROM `service_providers` WHERE provider_name = '$provider_name'");
                            $select_provider_id->execute();
                            $fetch_provider_id = $select_provider_id->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <div class="col">
                        <div class="card shadow-sm">
                            <img src="uploaded_img/<?= $fetch_products['image']; ?>" class="bd-placeholder-img card-img-top" width="100%" height="225">
                            <div class="card-body">
                                <p class="card-text"><?= $fetch_products['service_name']; ?></p>
                                <p class="card-text"><?= $fetch_products['description']; ?></p>
                                <p class="card-text">Price: LKR <?= $fetch_products['price']; ?>/=</p>
                                <p class="card-text"><a href="view_seller_profile.php?provider_id=<?= $fetch_provider_id['provider_id']; ?>" style="text-decoration: none;"><?= $fetch_products['provider_name']; ?></a></p>
                                <p class="card-text"><?= $fetch_products['category']; ?> / <?= $fetch_products['sub_category']; ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group">
                                        <?php if(!isset($buyer_id)){?> <!-- checks if user is logged in and blocks from accessing the checkout page-->
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="usernotloggedin()">Book Service</button>
                                        <?php }else{ ?>
                                            <form action="book_service.php" method="GET">
                                                <input type="hidden" name="buyer_id" value="<?= $fetch_profile['buyer_id']; ?>">
                                                <input type="hidden" name="service_id" value="<?= $fetch_products['service_id']; ?>">
                                                <input type="hidden" name="provider_id" value="<?= $fetch_provider_id['provider_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" name="book">Book Service</a>
                                            </form>
                                        <?php } ?>
                                    </div>
                                    <small class="text-body-secondary"><?= $fetch_products['created_at']; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } } ?>
                </div>
            </div>

            <hr class="featurette-divider">

            <!-- Feautured seller profiles -->
            <div class="row">
                <center><h3 class="mb-5"">Featured Providers</h3></center>
                <?php 
                $select_f_providers = $conn->prepare("SELECT * FROM `featured_providers`");
                $select_f_providers->execute();
                if($select_f_providers->rowCount() > 0){
                    while($fetch_f_providers = $select_f_providers->fetch(PDO::FETCH_ASSOC)){
                        $ft_provider = $fetch_f_providers['provider_name'];
                        $select_providers_detils = $conn->prepare("SELECT * FROM `service_providers` WHERE provider_name = '$ft_provider'");
                        $select_providers_detils->execute();
                        $fetch_providers = $select_providers_detils->fetch(PDO::FETCH_ASSOC)
                ?>
                <div class="col-lg-4">
                    <img src="uploaded_img/<?= $fetch_providers['profile_picture']; ?>" class="bd-placeholder-img rounded-circle" width="140" height="140">
                    <h2 class="fw-normal"><?= $fetch_providers['provider_name']; ?></h2>
                    <p><?= $fetch_providers['email']; ?>.</p>
                    <form action="view_seller_profile.php" method="GET">
                        <input type="hidden" name="provider_id" value="<?= $fetch_providers['provider_id']; ?>">
                        <p><button type="submit" class="btn btn-secondary">View Profile &raquo;</button></p>
                    </form>
                </div>
                <?php } } ?>
            </div>

            <hr class="featurette-divider">

            <div class="row featurette">
                <div class="col-md-7 order-md-2">
                    <h2 class="featurette-heading fw-normal lh-1">About us<span class="text-body-secondary">.</span></h2>
                    <p class="lead">LankanServices connects you with trusted local service providers across Sri Lanka. Our mission is to make finding reliable services simple and convenient.</p>
                </div>
                <div class="col-md-5 order-md-1">
                    <svg class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto" width="500" height="500" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 500x500" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="var(--bs-secondary-bg)"/><text x="50%" y="50%" fill="var(--bs-secondary-color)" dy=".3em">500x500</text></svg>
                </div>
            </div>
        </div><!-- /.container -->
        
        <!-- Chat Icon -->
        <div class="chat-icon" onclick="toggleChatBox()">
            <img src="images/chat.png" alt="Chat with us" title="Send admin a query" style="width: 40px; height: 40px;">
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
                    <input type="text" name="query_msg" placeholder="<?php if(!isset($buyer_id)){echo"Please log in to chat with admin!";}else{echo"Type your query here...";}?>" required />
                    <select name="query_type" required>
                        <option value="technical_issue">Technical Issue</option>
                        <option value="account_issue">Account Issue</option>
                        <option value="general_inquiry">General Inquiry</option>
                    </select>
                    <button type="submit" title="Send admin a query" name="sendQuery" <?php if(!isset($buyer_id)){echo"disabled";}?> >Send</button>
                </form>
            </div>
        </div>

    </main>

    <?php include 'stay_informed.php'; ?>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/common.js"></script>
    
</body>
</html>