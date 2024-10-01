<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

@include 'db_config.php'; // Include your database configuration
//session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email from form and sanitize it
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    
    // Validate email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Get buyer_id from session or another source
        $buyer_id = $_SESSION['buyer_id']; // Adjust this based on your session management
        
        // Check if buyer exists
        $query = "SELECT * FROM service_buyers WHERE buyer_id = :buyer_id";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':buyer_id', $buyer_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single row

        if ($result) {
            // Check if already subscribed
            if ($result['subscribed'] !== 'Yes') {
                // Update subscription status
                $update_query = "UPDATE service_buyers SET subscribed = 'Yes' WHERE buyer_id = :buyer_id";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bindValue(':buyer_id', $buyer_id, PDO::PARAM_INT);

                if ($update_stmt->execute()) {
                    // Send the welcome email using PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'lankanservices@gmail.com'; 
                        $mail->Password = 'qfqxxnqxnzerdqga'; 
                        $mail->SMTPSecure = 'ssl';
                        $mail->Port = 465;

                        $mail->setFrom('lankanservices@gmail.com', 'Lankan Services');
                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = 'Welcome to LankanServices!';
                        $mail->Body = 'Thank you for subscribing to LankanServices. Stay informed for updates!';

                        $mail->send();
                        $success_message = "Subscription successful! A welcome email has been sent.";
                    } catch (Exception $e) {
                        $error_message = "Failed to send welcome email. Mailer Error: {$mail->ErrorInfo}";
                    }
                } else {
                    $error_message = "Failed to subscribe. Please try again.";
                }
            } else {
                $error_message = "This email is already subscribed.";
            }
        } else {
            $error_message = "Buyer not found.";
        }
    } else {
        $error_message = "Invalid email address. Please enter a valid email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe to LankanServices</title>
</head>
<body>
    <div class="hero-section" align="center">
        <h2>Subscribe us to get to know better</h2>
        <form action="" method="post">
            <label for="email">Enter your email:</label>
            <input type="email" id="email" name="email" placeholder="Your email" required>
            <input type="submit" value="Subscribe">
        </form>

        <!-- Display success or error message -->
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
