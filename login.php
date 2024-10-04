<?php

@include 'db_config.php';
session_start();

if(isset($_POST['login'])){ // if login button is pressed

   $email = $_POST['email'];
   $pass = $_POST['password'];

   if($email == 'admin@lankanservices.com' && $pass == 'admin123'){
      $_SESSION['admin_id'] = $email;
      header('location:admin/'); // if user is admin, redirect to admin page

   }else{
      $pass = md5($_POST['password']); // hash password

      $sql = "SELECT * FROM `service_buyers` WHERE email = ? AND password = ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$email, $pass]);
      $rowCount = $stmt->rowCount();  // checks if entered email & password exists in the DB table
   
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
   
      if($rowCount > 0){ // check if user is a servie buyer
         if($row['two_factor_enabled'] == 1){ // check if two factor auth is enabled
            $_SESSION['email'] = $email;
            $_SESSION['buyer_id'] = $row['buyer_id'];
            $_SESSION['otp'] = rand(1000,10000); // generate random 4 didgit code as OTP
            header('location:two_factor_auth.php'); // send him to two factor auth page
         }else{
            $_SESSION['buyer_id'] = $row['buyer_id']; // if two factor not enabled, start login session with buyer_id
            header('location:index.php'); // and redirect user to buyer page
         }

      }else{
         $sql = "SELECT * FROM `service_providers` WHERE email = ? AND password = ?";
         $stmt = $conn->prepare($sql);
         $stmt->execute([$email, $pass]);
         $rowCount = $stmt->rowCount();  // checks if entered email & password exists in the DB table
      
         $row = $stmt->fetch(PDO::FETCH_ASSOC);
      
         if($rowCount > 0){
            $_SESSION['provider_id'] = $row['provider_id'];
            header('location:provider/'); // if user is provider, redirects to provider page

         }else{
            echo"<script> alert('Incorrect email or password!'); </script>"; // the account doesnt exist or the details are incorrect
         }
      }
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | LankanServices</title>

    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link href="css/sign-in.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
<main class="form-signin w-100 m-auto">

  <form action="" method="post">
    <img class="mb-4" src="images/logo.png" alt="" width="72" height="57">
    <h1 class="h3 mb-3 fw-normal">Please login</h1>
    <div class="form-floating">
      <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email" required>
      <label for="floatingInput">Email address</label>
    </div>
    <div class="form-floating">
      <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" required>
      <label for="floatingPassword">Password</label>
    </div>
    <button class="btn btn-primary w-100 py-2" type="submit" name="login">LOGIN</button>
    <p class="mt-5 text-body-secondary">Dont have an account? <a href="signup.php">Signup Here</a></p>
    <p class="mt-3 mb-3 text-body-secondary">&copy; 2024 | LankanServices</p>
  </form>

</main>
<!-- Bootstrap JavaScript File -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>