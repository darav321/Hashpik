<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // Use local Postfix
        $mail->isSMTP();
        $mail->Host = 'localhost';   
        $mail->SMTPAuth = false;     
        $mail->Port = 25;            
        $mail->SMTPSecure = false;   

        $mail->setFrom('no-reply@yourdomain.com', 'Hashpik');  

        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($message);
        $mail->AltBody = strip_tags($message);

        return $mail->send();
    } catch (Exception $e) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }
}
