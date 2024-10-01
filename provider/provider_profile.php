<?php
@include '../db_config.php';
session_start();

$provider_id = $_SESSION['provider_id'];

if(!isset($provider_id)){
    header('../location:index.php');
};

if(isset($_POST['updateprofile'])){

  $name = $_POST['companyName'];
  $email = $_POST['email'];
  $provider_bio = $_POST['provider_bio'];
  $location = $_POST['location'];
  $availability = $_POST['availability'];

  $update_profile = $conn->prepare("UPDATE `service_providers` SET email = ?, provider_name = ?, provider_bio = ?, availability = ?, location = ? WHERE provider_id = ?");
  $update_profile->execute([$email, $name, $provider_bio, $availability, $location, $provider_id]);

  $profile_pic = $_FILES['newProfilePic']['name'];
  $profile_pic_size = $_FILES['newProfilePic']['size'];
  $profile_pic_tmp_name = $_FILES['newProfilePic']['tmp_name'];
  $profile_pic_folder = '../uploaded_img/'.$profile_pic;

  if(!empty($profile_pic)){
    if($profile_pic_size > 2000000){
      echo"<script>alert('Profile Pic size is too large!');</script>";
    }else{
      $update_profile_pic = $conn->prepare("UPDATE `service_providers` SET profile_picture = ? WHERE provider_id = ?");
      $update_profile_pic->execute([$profile_pic, $provider_id]);
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
}

if(isset($_POST['changepass'])){
  $currentPass = $_POST['currentPass'];
  $currentPassInput = md5($_POST['currentPassInput']);
  $newPass = md5($_POST['newPass']);
  $confimNewPass = md5($_POST['confimNewPass']);

  if($currentPass != $currentPassInput){
    echo"<script>alert('Current password is incorrect!');</script>";
  }elseif($newPass != $confimNewPass){
    echo"<script>alert('New passwords do not match!');</script>";
  }else{
    $change_pass = $conn->prepare("UPDATE `service_providers` SET password = ? WHERE provider_id = ?");
    $change_pass->execute([$newPass, $provider_id]);
    echo"<script>alert('Password Updated Successfully!');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage My Profile | LankanServices</title>
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
              <img src="../uploaded_img/<?= $fetch_profile['profile_picture']; ?>" width="100" height="100" alt="Profile" class="rounded-circle">
              <h3><?= $fetch_profile['provider_name']; ?></h3> 
              <h4><?= $fetch_profile['email']; ?> <?php if($fetch_profile['is_verified'] == 1) { ?> <img src="../images/patch-check-fill.svg" title="Email is verified"> <?php } ?></h4>
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
                    <div class="col-lg-9 col-md-8"><?= $fetch_profile['provider_name']; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8"><?= $fetch_profile['email']; ?></div>
                  </div>

                  
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Bio</div>
                    <div class="col-lg-9 col-md-8"><?= $fetch_profile['provider_bio']; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Location</div>
                    <div class="col-lg-9 col-md-8"><?= $fetch_profile['location']; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Rating</div>
                    <div class="col-lg-9 col-md-8"><?= $fetch_profile['rating']; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Availabilty</div>
                    <div class="col-lg-9 col-md-8"><?= $fetch_profile['availability']; ?></div>
                  </div>


                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                        <img src="../uploaded_img/<?= $fetch_profile['profile_picture']; ?>" width="60" height="60" alt="Profile">
                        <div class="pt-2">
                          <input type="file" class="btn btn-primary btn-sm" title="Upload new profile image" name="newProfilePic" accept="image/png, image/jpeg, image/jpg">
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Company Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="companyName" type="text" class="form-control" id="fullName" value="<?= $fetch_profile['provider_name']; ?>" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" id="Email" value="<?= $fetch_profile['email']; ?>" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Bio</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="provider_bio" type="text" class="form-control" id="fullName" value="<?= $fetch_profile['provider_bio']; ?>" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="location" class="col-md-4 col-lg-3 col-form-label">Location</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="location" type="text" class="form-control" id="location" value="<?= $fetch_profile['location']; ?>" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Availabilty</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="availability" type="text" class="form-control" id="fullName" value="<?= $fetch_profile['availability']; ?>" required>
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