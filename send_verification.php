<?php
require 'sendMail.php';
require 'database.php';

if(!isset($_POST['email'])) {
    die("Email not received");
}

$email = $_POST['email'];

$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$subject = "Welcome to Hashpik";
$message = "Now you can get tons of images from different websites on single platform";

if(sendEmail($email, $subject, $message)) {
    echo "Verification email sent!";
} else {
    echo "Failed to send email!";
}


 