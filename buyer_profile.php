<?php
@include 'db_config.php';
session_start();

$buyer_id = $_SESSION['buyer_id'];

if(!isset($buyer_id)){
    header('location:index.php');
};

if(isset($_GET['enable_two_factor'])){
  $two_factor_query=$conn->prepare("UPDATE `service_buyers` SET two_factor_enabled = 1 WHERE buyer_id = '$buyer_id'");
  $two_factor_query->execute();

  if($two_factor_query){
    echo"<script>alert('Two Factor Authentication Enabled Successfully');</script>";
  }
};
// Disable 2FA
if (isset($_GET['disable_two_factor'])) {
  $two_factor_query = $conn->prepare("UPDATE `service_buyers` SET two_factor_enabled = 0 WHERE buyer_id = ?");
  $two_factor_query->execute([$buyer_id]);

  if ($two_factor_query) {
      echo "<script>alert('Two-Factor Authentication Disabled Successfully');</script>";
  }
};

if(isset($_POST['updateprofile'])){ // the code below executes when Save Chnages on profile i pressed

  $name = $_POST['fullName'];
  $email = $_POST['email'];
  $address = $_POST['address'];

  $update_profile = $conn->prepare("UPDATE `service_buyers` SET email = ?, buyer_name = ?, address = ? WHERE buyer_id = ?");
  $update_profile->execute([$email, $name, $address, $buyer_id]);

  $profile_pic = $_FILES['newProfilePic']['name'];
  $profile_pic_size = $_FILES['newProfilePic']['size'];
  $profile_pic_tmp_name = $_FILES['newProfilePic']['tmp_name'];
  $profile_pic_folder = 'uploaded_img/'.$profile_pic;

  if(!empty($profile_pic)){
    if($profile_pic_size > 2000000){
      echo"<script>alert('Profile Pic size is too large!');</script>";
    }else{
      $update_profile_pic = $conn->prepare("UPDATE `service_buyers` SET profile_picture = ? WHERE buyer_id = ?");
      $update_profile_pic->execute([$profile_pic, $buyer_id]);
      if($update_profile_pic){
        move_uploaded_file($profile_pic_tmp_name, $profile_pic_folder);
      }
    }
  }
  
  if($update_profile){
    echo"<script>alert('User Profile Updated Successfully');</script>";
  }else{
    echo"<script>alert('Something went wrong ;(');</script>";
  }
};

if(isset($_POST['changepass'])){ // the code below executes when change password button is pressed
  $currentPass = $_POST['currentPass'];
  $currentPassInput = md5($_POST['currentPassInput']);
  $newPass = md5($_POST['newPass']);
  $confimNewPass = md5($_POST['confimNewPass']);

  if($currentPass != $currentPassInput){
    echo"<script>alert('Current password is incorrect!');</script>";
  }elseif($newPass != $confimNewPass){
    echo"<script>alert('New passwords do not match!');</script>";
  }else{
    $change_pass = $conn->prepare("UPDATE `service_buyers` SET password = ? WHERE buyer_id = ?");
    $change_pass->execute([$newPass, $buyer_id]);
    echo"<script>alert('Password Updated Successfully!');</script>";
  }
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | LankanServices</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body style="overflow-x:hidden">
<main>
    <?php include 'header.php'; ?>

    <section class="section profile">
      <div class="row">

        <div class="col-xl-4 ms-auto">
          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
              <img src="uploaded_img/<?= $fetch_profile['profile_picture']; ?>" width="100" height="100" alt="Profile" class="rounded-circle">
              <h3><?= $fetch_profile['buyer_name']; ?></h3> 
              <h4><?= $fetch_profile['email']; ?> <?php if($fetch_profile['is_verified'] == 1) { ?> <img src="images/patch-check-fill.svg" title="Email is verified"> <?php } ?></h4>
            </div>
          </div>
        </div>

        <div class="col-xl-6 me-auto">
          <div class="card">
            <div class="card-body pt-3">

              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">
                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                </li>
              </ul>

              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                  <h5 class="card-title mt-4">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Full Name</div>
                    <div class="col-lg-9 col-md-8"><?= $fetch_profile['buyer_name']; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8"><?= $fetch_profile['email']; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Address</div>
                    <div class="col-lg-9 col-md-8"><?= $fetch_profile['address']; ?></div>
                  </div>

                  <div class="text-left mt-5" style="position: relative;">
                  <?php if ($fetch_profile['two_factor_enabled'] == 0) { ?>
                      <!-- Enable 2FA button -->
                      <a href="buyer_profile.php?enable_two_factor=<?= $fetch_profile['buyer_id']; ?>" 
                        class="btn btn-outline-primary" id="enableButton" style="position: relative;">Enable 2 Factor Auth</a>
                  <?php } else { ?>
                      <!-- 2FA Enabled button -->
                      <a href="buyer_profile.php" class="btn btn-outline-primary" id="enableButton" 
                        style="pointer-events: none; position: relative;">2FA Enabled</a>
                        
                      <!-- Disable 2FA button -->
                      <a href="buyer_profile.php?disable_two_factor=<?= $fetch_profile['buyer_id']; ?>" 
                        class="btn btn-outline-danger" id="disableButton" 
                        style="position: absolute; left: 0; top: 0; opacity: 0; transition: opacity 0.3s ease;">
                        Disable 2 Factor Auth
                      </a>
                  <?php } ?>
              </div>

              <script>
                  document.addEventListener("DOMContentLoaded", function() {
                      // Only run this script if 2FA is enabled
                      <?php if ($fetch_profile['two_factor_enabled'] == 1) { ?>
                          const enableButton = document.getElementById('enableButton');
                          const disableButton = document.getElementById('disableButton');

                          // Show the "Disable 2FA" button when hovering over the "Enable 2FA" button
                          enableButton.addEventListener('mouseover', function() {
                              disableButton.style.opacity = '1'; // Show the disable button
                          });

                          // Hide the "Disable 2FA" button when not hovering
                          enableButton.addEventListener('mouseout', function() {
                              disableButton.style.opacity = '0'; // Hide the disable button
                              setTimeout(() => {
                                  disableButton.style.display = 'none'; // Hide it completely after fade out
                              }, 300); // Wait for the fade-out transition to complete
                          });

                          // Show the disable button when the page loads, if it’s already enabled
                          disableButton.style.display = 'inline-block'; // Show it
                          setTimeout(() => {
                              disableButton.style.opacity = '0'; // Start hidden
                          }, 10); // Allow DOM update before starting the fade out
                      <?php } ?>
                  });
              </script>


                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                  <!-- Profile Edit Form -->
                  <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                        <img src="uploaded_img/<?= $fetch_profile['profile_picture']; ?>" width="60" height="60" alt="Profile">
                        <div class="pt-2">
                          <input type="file" class="btn btn-primary btn-sm" title="Upload new profile image" name="newProfilePic" accept="image/png, image/jpeg, image/jpg">
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="fullName" type="text" class="form-control" id="fullName" value="<?= $fetch_profile['buyer_name']; ?>" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="address" type="text" class="form-control" id="Address" value="<?= $fetch_profile['address']; ?>" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" id="Email" value="<?= $fetch_profile['email']; ?>" required>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary" name="updateprofile">Save Changes</button>
                    </div>
                  </form><!-- End Profile Edit Form -->
                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password Form -->
                  <form action="" method="POST">
                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                      <div class="col-md-8 col-lg-9">
                      <input name="currentPass" type="hidden" class="form-control" id="currentPass" value="<?= $fetch_profile['password']; ?>">
                        <input name="currentPassInput" type="password" class="form-control" id="currentPassInput" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="newPass" type="password" class="form-control" id="newPass" required minlength="5">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Confirm New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="confimNewPass" type="password" class="form-control" id="confimNewPass" required minlength="5">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary" name="changepass">Change Password</button>
                    </div>
                  </form><!-- End Change Password Form -->
                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</main>
</body>
</html>