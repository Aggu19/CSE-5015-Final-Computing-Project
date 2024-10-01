<?php
@include '../db_config.php';
session_start();

$provider_id = $_SESSION['provider_id'];

if (!isset($provider_id)) {
    header('location:../index.php');
};

if (isset($_GET['updated'])) {
    echo "<script>alert('Service Was Successfully Updated!');</script>";
}

if (isset($_POST['addservice'])) {

    $name = $_POST['serviceName'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['servicedesc'];
    $serviceSubCat = $_POST['serviceSubCat'];
    $seller_name = $fetch_profile['provider_name'];
    $promotion = $_POST['promotion']; // Getting the promotion percentage

    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    $select_products = $conn->prepare("SELECT * FROM `services` WHERE service_name = ?");
    $select_products->execute([$name]);

    if ($select_products->rowCount() > 0) {
        echo "<script>alert('Service already exists!');</script>";
    } else {

        $insert_products = $conn->prepare("INSERT INTO `services`(provider_name, service_name, description, price, category, sub_category, image, promotion) VALUES(?,?,?,?,?,?,?,?)");
        $insert_products->execute([$seller_name, $name, $description, $price, $category, $serviceSubCat, $image, $promotion]);

        move_uploaded_file($image_tmp_name, $image_folder);

        if ($insert_products) {
            $send_notification = $conn->prepare("INSERT INTO `notifications` (username, message, type) VALUES(?,?,?)");
            $send_notification->execute([$seller_name, 'A new service has been added by one of our service providers, please approve it to be publicly displayed.', 'system']);

            echo "<script>alert('Service Successfully Added!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage My Services | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/sign-in.css" rel="stylesheet">
</head>
<body>
<main>
<?php include 'header.php'; ?>

    <div class="form-signin w-100 m-auto mt-3">
      <form action="" method="post" enctype="multipart/form-data">
        <h1 class="h3 mb-3 fw-normal">Add a new service</h1>
        <div class="row g-3">
          <!-- Other form fields like service name, price, etc. -->
          <div class="col-12">
              <label for="serviceName" class="form-label">Service Name</label>
              <input type="text" class="form-control" id="serviceName" name="serviceName" placeholder="" value="" required>
          </div>
          <div class="col-12">
              <label for="price" class="form-label">Price</label>
              <input type="number" class="form-control" id="price" name="price" placeholder="" value="" required>
          </div>
          <div class="col-12">
              <select name="category" class="form-select">
                <option selected disabled>Select Service Category</option>
                <?php
                  $service_category = $conn->prepare("SELECT * FROM `service_categories`");
                  $service_category->execute();
                  while ($fetch_category = $service_category->fetch(PDO::FETCH_ASSOC)) {
                      echo "<option value='{$fetch_category['category_name']}'>{$fetch_category['category_name']}</option>";
                  }
                ?>
              </select>
          </div>
          <!-- New dropdown for promotion -->
          <div class="col-12">
              <label for="promotion" class="form-label">Promotion Percentage</label>
              <select name="promotion" class="form-select" required>
                <option selected disabled>Select Promotion</option>
                <option value="0">No Promotion</option>
                <option value="5">5% Off</option>
                <option value="10">10% Off</option>
                <option value="15">15% Off</option>
                <option value="20">20% Off</option>
              </select>
          </div>
          <div class="col-12">
              <label for="serviceSubCat" class="form-label">Service Sub Category</label>
              <input type="text" class="form-control" id="serviceSubCat" name="serviceSubCat" placeholder="" value="" required>
          </div>
          <div class="col-12">
            <label for="serviceimage" class="form-label">Upload Service Image</label>
            <input type="file" name="image" class="form-control" id="serviceimage" accept="image/jpg, image/jpeg, image/png" required>
          </div>
        </div>
        <div class="col-12 mt-2">
          <label for="servicedesc" class="form-label">Service Description</label>
          <textarea class="form-control" id="servicedesc" name="servicedesc" rows="3" required style="resize: none;"></textarea>
        </div>
        <button class="btn btn-primary w-100 py-2 mt-2" type="submit" name="addservice">ADD SERVICE</button>
      </form>
    </div>

    <!-- The rest of your code, like displaying services -->
</main>
<?php include 'footer.php'; ?>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
