<?php
@include '../db_config.php';
session_start();

$provider_id = $_SESSION['provider_id'];
$service_id = $_GET['edit'];

if(!isset($provider_id)){
  header('location:../index.php');
};

if(!isset($service_id)){
  header('location:index.php');
};

if(isset($_GET['edit'])){
    $select_service = $conn->prepare("SELECT * FROM `services` WHERE service_id = '$service_id'");
    $select_service->execute();
    $fetch_service = $select_service->fetch(PDO::FETCH_ASSOC);
};

if(isset($_GET['delete'])){
  $service_id = $_GET['delete'];
  $delete_query = $conn->prepare("DELETE FROM `services` WHERE service_id = ? ");
  $delete_query->execute([$service_id]);
  echo"<script>alert('Service Deleted!');</script>";
};

if(isset($_POST['updateService'])){
  $name = $_POST['serviceName'];
  $price = $_POST['price'];
  $category = $_POST['category'];
  $description = $_POST['servicedesc'];
  $serviceSubCat = $_POST['serviceSubCat'];
  $promotion = $_POST['promotion'];  // Add promotion field

  $image = $_FILES['image']['name'];
  $image_size = $_FILES['image']['size'];
  $image_tmp_name = $_FILES['image']['tmp_name'];
  $image_folder = '../uploaded_img/'.$image;

  // Update query with promotion field
  $update_service = $conn->prepare("UPDATE `services` SET service_name = ?, description = ?, price = ?, category = ?, sub_category = ?, image = ?, promotion = ? WHERE service_id = '$service_id'");
  $update_service->execute([$name, $description, $price, $category, $serviceSubCat, $image, $promotion]);
  
  move_uploaded_file($image_tmp_name, $image_folder);
  header('location:provider_services.php?updated');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service | LankanServices</title>
    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="../css/sign-in.css" rel="stylesheet">
</head>
<body>
  <main>
    <?php include 'header.php'; ?>

    <div class="form-signin w-100 m-auto mt-3">
      <form action="" method="post" enctype="multipart/form-data">

        <h1 class="h3 mb-3 fw-normal">Edit: <?= $fetch_service['service_name']; ?></h1>
        <div class="row g-3">

          <div class="col-12">
            <img src="../uploaded_img/<?= $fetch_service['image']; ?>" class="bd-placeholder-img card-img-top" width="100%" height="225" >
            <label for="serviceimage" class="form-label">Change Service Image</label>
            <input type="file" name="image" class="form-control" id="serviceimage" default="<?= $fetch_service['image']; ?>" accept="image/jpg, image/jpeg, image/png" required>
          </div>
          
          <div class="col-12">
            <label for="serviceName" class="form-label">Service Name</label>
            <input type="text" class="form-control" id="serviceName" name="serviceName" placeholder="<?= $fetch_service['service_name']; ?>" value="<?= $fetch_service['service_name']; ?>" required>
          </div>
          
          <div class="col-12">
            <label for="serviceName" class="form-label">Price</label>
            <input type="number" class="form-control" id="price" name="price" placeholder="" value="<?= $fetch_service['price']; ?>" required>
          </div>
          
          <div class="col-12">
            <select name="category" class="form-select" aria-label="Default select example">
              <option selected disabled value="<?= $fetch_service['category']; ?>"><?= $fetch_service['category']; ?></option>
              <?php
              $service_category = $conn->prepare("SELECT * FROM `service_categories`");
              $service_category->execute();
              while($fetch_category = $service_category->fetch(PDO::FETCH_ASSOC)){
              ?>
              <option value="<?= $fetch_category['category_name']; ?>"><?= $fetch_category['category_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          
          <div class="col-12">
            <label for="serviceSubCat" class="form-label">Service Sub Category</label>
            <input type="text" class="form-control" id="serviceSubCat" name="serviceSubCat" placeholder="" value="<?= $fetch_service['sub_category']; ?>" required>
          </div>

          <!-- Promotion dropdown -->
          <div class="col-12">
            <label for="promotion" class="form-label">Promotion (%)</label>
            <select name="promotion" class="form-select" id="promotion">
              <option value="0" <?= $fetch_service['promotion'] == 0 ? 'selected' : ''; ?>>No Promotion</option>
              <option value="5" <?= $fetch_service['promotion'] == 5 ? 'selected' : ''; ?>>5%</option>
              <option value="10" <?= $fetch_service['promotion'] == 10 ? 'selected' : ''; ?>>10%</option>
              <option value="15" <?= $fetch_service['promotion'] == 15 ? 'selected' : ''; ?>>15%</option>
              <option value="20" <?= $fetch_service['promotion'] == 20 ? 'selected' : ''; ?>>20%</option>
            </select>
          </div>
        </div>
        
        <div class="col-12 mt-2">
          <label for="servicedesc" class="form-label">Service Description</label>
          <textarea class="form-control" id="servicedesc" name="servicedesc" rows="3" required style="resize: none;"><?= $fetch_service['description']; ?></textarea>
        </div>
        <button class="btn btn-primary w-100 py-2 mt-2" type="submit" name="updateService">UPDATE SERVICE</button>
      </form>
      <a href="edit_service.php?delete=<?= $fetch_service['service_id']; ?>" onclick="return confirm('Delete this service?');" type="button" class="btn btn-outline-danger w-100 py-2 mt-2">Delete Service</a>
    </div>
  </main>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
