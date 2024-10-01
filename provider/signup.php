<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
// the above files & libraries are required to send email to user

include '../db_config.php'; // the database connection file
session_start();

if(isset($_POST['signup'])){ // checks if the signup button is pressed

    $verification_code = rand(1000,10000); // create a random 4 digit code 

    //collect user submitted data
   $company_name = $_POST['company_name'];
   $email = $_POST['email'];
   $password = md5($_POST['password']);

   $select = $conn->prepare("SELECT * FROM `service_providers` WHERE email = ?");
   $select->execute([$email]);

   if($select->rowCount() > 0){
      echo"<script> alert('Email is already in use!'); </script>";
   }else{
        $insert = $conn->prepare("INSERT INTO `service_providers`(email, password, provider_name, verification_code) VALUES(?,?,?,?)");
        $insert->execute([$email, $password, $company_name, $verification_code]); // inserts user details into DB

        if($insert){
              $notification_message = "Hooray, Good News! A New service provider has just registered to our website.";
              $notification_type  = "system";
              $send_notification = $conn->prepare("INSERT INTO `notifications` (username, message, type) VALUES(?,?,?)");
              $send_notification->execute([$company_name, $notification_message, $notification_type]);
               //the code below emails a welcome message and the verification code to the user
               $mail = new PHPMailer(true);

               $mail->isSMTP();
               $mail->Host = 'smtp.gmail.com';
               $mail->SMTPAuth = true;
               $mail->Username = 'lankanservices@gmail.com';
               $mail->Password = 'qfqxxnqxnzerdqga';
               $mail->SMTPSecure = 'ssl';
               $mail->Port = 465;
           
               $mail->setFrom('lankanservices@gmail.com');
           
               $mail->addAddress($_POST["email"]);
           
               $mail->isHTML(true);
           
               $mail->Subject = 'Welcome to LankanServices!';
               $mail->Body = 'Thank you for registering as a Service Provider at LankanServices, We hope you will enjoy your time spent on our platform. <br> 
                                    Your verification code is: ' . $verification_code;
           
               $mail->send();
           
               echo "<script> alert('Signup was sucessfull! You will be now redirected to the verification page, please check your email for the verification code.');</script>";

               $_SESSION['email'] = $email;
               header('location:provider_verify.php'); // if success redirects to the verification page
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup as a Provider | LankanServices</title>

    <!-- Main Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link href="../css/sign-in.css" rel="stylesheet"> <!-- CSS for login/signup/verify pages -->
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
  
<main class="form-signin w-100 m-auto">
  <form action="" method="post">
    <img class="mb-4" src="../images/logo.png" alt="" width="72" height="57">
    <h1 class="h3 mb-3 fw-normal">Provider Signup Form</h1>

    <div class="row g-3">
        <div class="col-12">
            <label for="firstName" class="form-label">Company Name</label>
            <input type="text" class="form-control" id="fullname" name="company_name" placeholder="" value="" required>
        </div>
    </div>

    <div class="form-floating mt-3">
      <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email" required>
      <label for="floatingInput">Email address</label>
    </div>
    <div class="form-floating">
      <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" required minlength="5">
      <label for="floatingPassword">Password</label>
    </div>

    <button class="btn btn-primary w-100 py-2" type="submit" name="signup">SIGNUP</button>
    <p class="mt-5 text-body-secondary">Are you a buyer? <a href="../signup.php">Click here to register</a></p>
    <p class="mt-2 text-body-secondary">Already have an account? <a href="../login.php">Login Here</a></p>
    <p class="mt-2 text-body-secondary">&copy; 2024 | LankanServices</p>
  </form>
</main>

  <!-- Bootstrap JavaScript File -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>