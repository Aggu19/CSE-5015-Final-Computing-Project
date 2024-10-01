<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

@include 'db_config.php';
session_start();

$buyer_id = $_SESSION['buyer_id'];
$email = $_SESSION['email'];
$otp = $_SESSION['otp']; // getting the OTP, Email and BuyerID from login page and storing it in variables

if(isset($buyer_id)){ // checking wether the user is coming from the login page
    if(!isset($_POST['submit_otp'])){
    //above 'if' is to make sure user hasn't already pressed submitOTP btn to avoid sending a new email with OTP after submitOTP btn is pressed, coz if page reloads, the send email func wil be activated.
        $mail = new PHPMailer(true);
    
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lankanservices@gmail.com';
        $mail->Password = 'qfqxxnqxnzerdqga';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
               
        $mail->setFrom('lankanservices@gmail.com');
               
        $mail->addAddress($email);
               
        $mail->isHTML(true);
               
        $mail->Subject = 'Your OTP';
        $mail->Body = 'To ensure that you are the person who is trying to login in to your account, please enter the OTP below in the authentication page,<br><br> 
                                OTP: ' . $otp;
               
        $mail->send(); // send the email
    }
}else{ // if not from login page .. send him back to homepage
    header('location:index.php');
};

if(isset($_POST['submit_otp'])){ // if submit OTP btn is pressed 
    $input_otp = $_POST['otp']; // stores the user input OTP
    if($otp == $input_otp){ // checks user input OTP against the OTP
        $_SESSION['buyer_id'] = $buyer_id;
        header('location:index.php'); // create a login session and send him to home page
    }else{
        echo"<script>alert('OTP is Incorrect!');</script>"; // if not matching give a feedback
    }
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two Factor Authentication | LankanServices</title>

    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link href="css/sign-in.css" rel="stylesheet"> <!-- CSS for login/signup/verify pages -->
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
  
<main class="form-signin w-100 m-auto">
  <form action="" method="post">
    <img class="mb-4" src="images/logo.png" alt="" width="72" height="57">
    <h1 class="h3 mb-3 fw-normal">Enter OTP</h1>
    <p class="mt-1 mb-1 text-body-secondary">Check your email for the OTP. Don't leave or refresh this page.</p>

    <div class="form-floating">
      <input type="number" class="form-control" name="otp" required minlength="4">
      <label for="floatingPassword">OTP</label>
    </div>

    <button class="btn btn-primary w-100 py-2 mt-3" type="submit" name="submit_otp">Submit</button>

    <p class="mt-3 mb-3 text-body-secondary">&copy; 2024 | LankanServices</p>
  </form>
</main>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>