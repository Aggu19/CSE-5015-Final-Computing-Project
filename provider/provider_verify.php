<?php

@include '../db_config.php';
session_start();

if(isset($_POST['verify'])){ // checks if verify button is pressed

    $email = $_SESSION['email']; // email from signup form
    $input_code = $_POST['verify_code'];

    $sql = "SELECT * FROM `service_providers` WHERE email = ? AND verification_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email, $input_code]);
    $rowCount = $stmt->rowCount(); // checks if the entered code and code in the DB are equal

    if($rowCount > 0){
        $sql2 = $conn->prepare("UPDATE `service_providers` SET is_verified = 1 WHERE email = ?");
        $sql2->execute([$email]);
        $rowCount2 = $sql2->rowCount(); // updates is_verified to '1' i.e yes

        if($rowCount2 >0){
            echo "<script>
                    alert('Verification Successful! You can login now.');
                    document.location.href = '../login.php';</script>"; // if all works fine, redirects to login page
        }else{
            echo "<script>alert('Something went wrong :(')</script>";
        }

    }else{
        echo "<script>alert('Verification Failed!')</script>";
    }

}

if(!isset($_SESSION['email'])){
    header('location:../index.php'); // checks if user is coming after filling the signup form, if negative it redirects to home page
}else{

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Verification | LankanServices</title>

    <!-- Bootstrap CSS File -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link href="../css/sign-in.css" rel="stylesheet"> <!-- CSS for login/signup/verify pages -->
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
  
<main class="form-signin w-100 m-auto">
  <form action="" method="post">
    <img class="mb-4" src="../images/logo.png" alt="" width="72" height="57">
    <h1 class="h3 mb-3 fw-normal">Verify your account</h1>
    <p class="mt-1 mb-1 text-body-secondary">Check your email for the verification code.</p>

    <div class="form-floating">
      <input type="number" class="form-control" name="verify_code" required minlength="4">
      <label for="floatingPassword">Verification Code</label>
    </div>

    <button class="btn btn-primary w-100 py-2 mt-3" type="submit" name="verify">VERIFY</button>

    <p class="mt-3 mb-3 text-body-secondary">&copy; 2024 | LankanServices</p>
  </form>
</main>

    <!-- Bootstrap JavaScript File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
<?php
}
?>